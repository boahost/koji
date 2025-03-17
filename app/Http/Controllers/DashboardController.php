<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalResellers = Reseller::count();

        return Inertia::render('Dashboard', [
            'statistics' => [
                'customers' => $totalCustomers,
                'products' => $totalProducts,
                'resellers' => $totalResellers,
            ]
        ]);
    }
}
