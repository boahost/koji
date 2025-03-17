<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ResellerAuthController extends Controller
{
    public function showLogin()
    {

        return Inertia::render('Resellers/Auth/Login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        \Log::info('Tentativa de login de revendedor', ['email' => $credentials['email']]);

        $revendedor = \App\Models\Reseller::where('email', $credentials['email'])->first();

        if (!$revendedor) {
            \Log::info('Revendedor não encontrado');
            return back()->withErrors([
                'email' => 'Revendedor não encontrado.',
            ]);
        }
        
        \Log::info('Revendedor encontrado, tentando autenticar');
        if (Auth::guard('reseller')->attempt($credentials)) {
            \Log::info('Login bem sucedido');
            $request->session()->regenerate();
            return redirect()->route('reseller.dashboard');
        }
        \Log::info('Falha na autenticação');
        return back()->withErrors([
            'email' => 'As credenciais informadas não correspondem aos nossos registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('reseller')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('reseller.login');
    }
}
