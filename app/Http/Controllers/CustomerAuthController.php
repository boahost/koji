<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

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
        
        \Log::info('Tentativa de login', [
            'email' => $credentials['email'],
            'customer_exists' => $customer ? 'sim' : 'não',
            'password_provided' => !empty($credentials['password']) ? 'sim' : 'não'
        ]);

        if (!$customer) {
            \Log::warning('Tentativa de login com email não encontrado', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'Email não encontrado.',
            ]);
        }

        // Tenta fazer login
        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            \Log::info('Login bem-sucedido', ['email' => $credentials['email']]);
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'));
        }

        // Se chegou aqui, a senha está incorreta
        \Log::warning('Tentativa de login com senha incorreta', ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'A senha está incorreta.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('products');
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();

        return Inertia::render('Customers/Dashboard/Index', [
            'customer' => $customer,
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

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
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
}
