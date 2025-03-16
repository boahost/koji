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

        if (Auth::guard('reseller')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('reseller.dashboard'));
        }

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
