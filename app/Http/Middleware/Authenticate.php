<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            if ($request->is('revendedor*')) {
                return route('reseller.login');
            }
            if ($request->is('minha-conta*', 'favoritos*', 'carrinho*')) {
                return route('customer.login');
            }
            return route('login');
        }
        return null;
    }
}
