<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Department;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductShowcaseController extends Controller
{
    public function cart()
    {
        return Inertia::render('Products/Cart', [
            'cartItems' => [], // Aqui virá a lógica do carrinho
            'auth' => auth()->guard('customer')->user()
        ]);
    }
    
    public function index(Request $request)
    {
        // Verificar se há um código de referência
        $referenceCode = $request->input('ref');
        if ($referenceCode) {
            // Armazenar o código de referência na sessão para uso posterior
            session(['reseller_reference' => $referenceCode]);
        }
        
        $query = Product::with(['category', 'department'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->input('category'), function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($request->input('department'), function ($query, $department) {
                $query->where('department_id', $department);
            });

        $products = $query->paginate(12)->withQueryString();
        
        $categories = Category::all();
        $departments = Department::all();
        
        // Verificar se há um revendedor associado ao código de referência
        $resellerInfo = null;
        if (session()->has('reseller_reference')) {
            $reseller = Reseller::where('reference_code', session('reseller_reference'))->first();
            if ($reseller) {
                $resellerInfo = [
                    'name' => $reseller->name,
                    'commission_rate' => $reseller->commission_rate
                ];
            }
        }

        return Inertia::render('Products/Showcase', [
            'products' => $products,
            'categories' => $categories,
            'departments' => $departments,
            'filters' => $request->only(['search', 'category', 'department']),
            'auth' => [
                'customer' => auth()->guard('customer')->user()
            ],
            'resellerInfo' => $resellerInfo
        ]);
    }
}
