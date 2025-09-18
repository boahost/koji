<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Wallet;
use App\Models\CartItem;
use Illuminate\Support\Facades\Http;
use App\Models\PropertyQuery;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Customers/Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verifica se o usuário existe
        $customer = Customer::where('email', $credentials['email'])->first();
        
        Log::info('Tentativa de login', [
            'email' => $credentials['email'],
            'customer_exists' => $customer ? 'sim' : 'não',
            'password_provided' => !empty($credentials['password']) ? 'sim' : 'não'
        ]);

        if (!$customer) {
            Log::warning('Tentativa de login com email não encontrado', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'Email não encontrado.',
            ]);
        }

        // Tenta fazer login
        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            Log::info('Login bem-sucedido', ['email' => $credentials['email']]);
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'));
        }

        // Se chegou aqui, a senha está incorreta
        Log::warning('Tentativa de login com senha incorreta', ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'A senha está incorreta.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        
        // Calcula o saldo total somando todos os registros da wallet
        $valorConta = Wallet::where('customer_id', $customer->id)->sum('valor');
        
        // Se não há registros, cria um registro inicial com saldo zero
        if ($valorConta === null) {
            $customer->wallet()->create(['valor' => 0]);
            $valorConta = 0;
        }

        return Inertia::render('Customers/Dashboard/Index', [
            'customer' => $customer,
            'walletValue' => $valorConta,
            'ordersCount' => 0, // TODO: Implementar contagem de pedidos
            'cartItemsCount' => 0, // TODO: Implementar contagem de itens no carrinho
        ]);
    }

    public function showRegister()
    {
        return Inertia::render('Customers/Auth/Register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'document' => 'required|string|unique:customers',
            'password' => 'required|string|min:8|confirmed',
            'cep' => 'required|string',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'complement' => 'nullable|string|max:255',
        ], [
            'neighborhood.required' => 'O campo bairro é obrigatório',
            'city.required' => 'O campo cidade é obrigatório',
            'state.required' => 'O campo estado é obrigatório',
            'state.size' => 'O campo estado deve ter exatamente 2 caracteres',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'document' => $request->document,
            'password' => Hash::make($request->password),
            'cep' => $request->cep,
            'street' => $request->street,
            'number' => $request->number,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => strtoupper($request->state),
            'complement' => $request->complement,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard');
    }

    public function profile()
    {
        return Inertia::render('Customers/Profile/Index', [
            'customer' => Auth::guard('customer')->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // Se estiver alterando a senha, valida apenas os campos da senha
        if ($request->filled('password')) {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ], [
                'current_password.required' => 'A senha atual é obrigatória.',
                'password.required' => 'A nova senha é obrigatória.',
                'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
                'password.confirmed' => 'A confirmação da nova senha não corresponde.',
            ]);

            // Verifica se a senha atual está correta
            if (!Hash::check($request->current_password, $customer->password)) {
                return back()->withErrors([
                    'current_password' => 'A senha atual está incorreta.',
                ]);
            }

            // Atualiza a senha
            $customer->forceFill([
                'password' => Hash::make($request->password),
            ])->save();

            // Atualiza a sessão para manter o usuário logado
            Auth::guard('customer')->login($customer);

            return back()->with('success', 'Senha atualizada com sucesso!');
        }
        // Se estiver alterando o endereço, valida apenas os campos do endereço
        else if ($request->filled(['cep', 'street', 'number'])) {
            $request->validate([
                'cep' => 'required|string',
                'street' => 'required|string|max:255',
                'number' => 'required|string|max:20',
                'neighborhood' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|size:2',
                'complement' => 'nullable|string|max:255',
            ]);

            // Atualiza o endereço
            $customer->update([
                'cep' => $request->cep,
                'street' => $request->street,
                'number' => $request->number,
                'neighborhood' => $request->neighborhood,
                'city' => $request->city,
                'state' => strtoupper($request->state),
                'complement' => $request->complement,
                // Nome, CPF e email não podem ser alterados
            ]);

            return back()->with('success', 'Endereço atualizado com sucesso!');
        }

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function orders()
    {
        $customer = Auth::guard('customer')->user();

        return Inertia::render('Customers/Orders/Index', [
            'customer' => $customer,
            'orders' => [], // TODO: Implementar listagem de pedidos
        ]);
    }

    public function ordersCount()
    {
        $customer = Auth::guard('customer')->user();
        $count = $customer->orders()->count();
        return response()->json(['count' => $count]);
    }

    public function favorites()
    {
        return Inertia::render('Customers/Favorites/Index', [
            'customer' => Auth::guard('customer')->user(),
            'favorites' => [], // TODO: Implementar listagem de favoritos
        ]);
    }

    public function cart()
    {
        $customer = Auth::guard('customer')->user();

        return Inertia::render('Customers/Cart/Index', [
            'customer' => $customer,
            'cartItems' => [], // TODO: Implementar listagem de itens do carrinho
        ]);
    }

    public function showForgotPassword()
    {
        return Inertia::render('Customers/Auth/ForgotPassword');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        Log::info('Tentativa de reset de senha', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        Log::info('Resultado do reset de senha', [
            'email' => $request->email,
            'status' => $status,
            'success' => $status === Password::RESET_LINK_SENT
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            Log::info('Email de reset enviado com sucesso', ['email' => $request->email]);
            return back()->with(['status' => 'Link de redefinição de senha enviado para seu email!']);
        } else {
            Log::warning('Erro ao enviar email de reset', ['email' => $request->email, 'status' => $status]);
            
            $errorMessage = match($status) {
                Password::RESET_THROTTLED => 'Muitas tentativas. Aguarde alguns segundos e tente novamente.',
                Password::INVALID_USER => 'Email não encontrado em nossa base de dados.',
                default => 'Não foi possível enviar o link de redefinição. Verifique se o email está correto.'
            };
            
            return back()->withErrors(['email' => $errorMessage]);
        }
    }

    public function showResetPassword(Request $request)
    {
        return Inertia::render('Customers/Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('customer.login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function addWalletValue(Request $request)
    {
        $request->validate([
            'valor' => 'required|numeric|min:0.01',
        ]);

        $valor = $request->valor;
        if (is_string($valor)) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            $valor = floatval($valor);
        }

        $customer = Auth::guard('customer')->user();
        $product = Product::find(9999);
        if (!$product) {
            return back()->withErrors(['message' => 'Produto de crédito não encontrado.']);
        }

        // Remove qualquer item de crédito já existente no carrinho
        CartItem::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->delete();

        // Adiciona o produto de crédito ao carrinho com o valor desejado
        CartItem::create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $valor,
            'reseller_id' => null
        ]);

        return redirect()->route('cart.index')->with('success', 'Produto de crédito adicionado ao carrinho. Realize o pagamento para creditar o saldo!');
    }

    public function buscarImovel(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        $wallet = $customer->wallet;
        if (!$wallet || $wallet->valor < 20) {
            return response()->json(['error' => 'Saldo insuficiente para realizar a consulta.'], 403);
        }

        $codigo = $request->codigo;
        $bearer = '06aef429-a981-3ec5-a1f8-71d38d86481e';
        $url = "https://gateway.apiserpro.serpro.gov.br/consulta-ccir-trial/v1/consultarDadosCcirPorCodigoImovel/{$codigo}";

        $response = Http::withHeaders([
            'accept' => '*/*',
            'x-signature' => '1',
            'Authorization' => 'Bearer ' . $bearer,
        ])->get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Erro ao consultar imóvel.'], 400);
        }

        // Abate R$20 do saldo e registra negativo na wallet
        // $wallet->valor -= 20;
        // $wallet->save();
        $customer->wallet()->create([
            'valor' => -20
        ]);

        // Registrar consulta no histórico
        PropertyQuery::create([
            'customer_id' => $customer->id,
            'codigo_imovel' => $codigo,
            'resposta_api' => $response->json(),
            'tipo_consulta' => 'codigo'
        ]);

        return response()->json(['data' => $response->json()]);
    }

    public function buscarPorNI(Request $request)
    {
        $request->validate([
            'ni' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        $wallet = $customer->wallet;
        if (!$wallet || $wallet->valor < 20) {
            return response()->json(['error' => 'Saldo insuficiente para realizar a consulta.'], 403);
        }

        $ni = $request->ni;
        $bearer = '06aef429-a981-3ec5-a1f8-71d38d86481e';
        $url = "https://gateway.apiserpro.serpro.gov.br/consulta-ccir-trial/v1/consultarCodigoImovelPorNI/{$ni}";

        $response = Http::withHeaders([
            'accept' => '*/*',
            'x-signature' => '1',
            'Authorization' => 'Bearer ' . $bearer,
        ])->get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Erro ao consultar imóveis por NI.'], 400);
        }

        // Abate R$20 do saldo e registra negativo na wallet
        // $wallet->valor -= 20;
        // $wallet->save();
        $customer->wallet()->create([
            'valor' => -20
        ]);

        // Registrar consulta por NI no histórico
        PropertyQuery::create([
            'customer_id' => $customer->id,
            'codigo_imovel' => null,
            'ni_consultado' => $ni,
            'resposta_api' => $response->json(),
            'tipo_consulta' => 'ni'
        ]);

        return response()->json(['data' => $response->json()]);
    }

    public function historicoImovel()
    {
        $customer = Auth::guard('customer')->user();
        $historico = $customer->propertyQueries()->orderByDesc('created_at')->get(['id', 'codigo_imovel', 'ni_consultado', 'resposta_api', 'tipo_consulta', 'created_at']);
        return response()->json(['historico' => $historico]);
    }

    public function historicoPorNI()
    {
        $customer = Auth::guard('customer')->user();
        $historico = $customer->propertyQueries()
            ->where('tipo_consulta', 'ni')
            ->orderByDesc('created_at')
            ->get(['id', 'ni_consultado', 'resposta_api', 'created_at']);
        return response()->json(['historico' => $historico]);
    }

    public function historicoPorCodigo()
    {
        $customer = Auth::guard('customer')->user();
        $historico = $customer->propertyQueries()
            ->where('tipo_consulta', 'codigo')
            ->orderByDesc('created_at')
            ->get(['id', 'codigo_imovel', 'resposta_api', 'created_at']);
        return response()->json(['historico' => $historico]);
    }

    public function exportarHistoricoImovelPdf()
    {
        $customer = Auth::guard('customer')->user();
        $historico = $customer->propertyQueries()->orderByDesc('created_at')->get();
        $pdf = Pdf::loadView('pdf.historico_imovel', ['historico' => $historico, 'customer' => $customer]);
        return $pdf->download('historico-consultas-imovel.pdf');
    }

    public function exportarConsultaImovelPdf($id)
    {
        $customer = Auth::guard('customer')->user();
        $consulta = $customer->propertyQueries()->where('id', $id)->firstOrFail();
        $pdf = Pdf::loadView('pdf.consulta_imovel', ['consulta' => $consulta, 'customer' => $customer]);
        return $pdf->download('consulta-imovel-'.$consulta->codigo_imovel.'.pdf');
    }
}
