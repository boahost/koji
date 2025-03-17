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

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
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
        return Inertia::render('Customers/Dashboard/Index', [
            'customer' => Auth::guard('customer')->user(),
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
            'complement' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'document' => $request->document,
            'password' => Hash::make($request->password),
            'cep' => $request->cep,
            'street' => $request->street,
            'number' => $request->number,
            'complement' => $request->complement,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard');
    }

    public function favorites()
    {
        return Inertia::render('Customers/Favorites/Index', [
            'favorites' => [], // Aqui virá a lógica dos favoritos
        ]);
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'cep' => 'required|string',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->password && !Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual está incorreta.',
            ]);
        }

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'cep' => $request->cep,
            'street' => $request->street,
            'number' => $request->number,
            'complement' => $request->complement,
        ]);

        if ($request->password) {
            $customer->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return back()->with('success', 'Perfil atualizado com sucesso!');
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
