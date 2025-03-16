<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('reseller')->check()) {
            return redirect()->route('reseller.login');
        }

        return $next($request);
    }
}
