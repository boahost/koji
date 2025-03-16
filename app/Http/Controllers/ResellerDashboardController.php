<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ResellerDashboardController extends Controller
{
    public function index()
    {
        $reseller = auth()->guard('reseller')->user();
        
        return Inertia::render('Resellers/Dashboard/Index', [
            'receivable' => [
                'total' => 1500.00, // Aqui você colocará a lógica real do cálculo
                'pending' => 500.00,
                'paid' => 1000.00
            ]
        ]);
    }

    public function profile()
    {
        $reseller = auth()->guard('reseller')->user();
        
        return Inertia::render('Resellers/Dashboard/Profile', [
            'reseller' => [
                'name' => $reseller->name,
                'email' => $reseller->email,
                'document' => $reseller->document,
                'commission_rate' => $reseller->commission_rate
            ]
        ]);
    }
}
