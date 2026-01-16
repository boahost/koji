<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;
        // dd("entrou");
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Não redireciona se estiver na rota de produtos
                if ($request->route()->getName() === 'products') {
                    return $next($request);
                }

                if ($guard === 'customer') {
                    return redirect()->route('customer.dashboard');
                }

                if ($guard === 'reseller') {
                    return redirect()->route('reseller.dashboard');
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
