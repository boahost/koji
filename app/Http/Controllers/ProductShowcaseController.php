<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductShowcaseController extends Controller
{
    public function cart()
    {
        return Inertia::render('Customers/Cart/Index', [
            'cartItems' => [], // Aqui virá a lógica do carrinho
        ]);
    }
    public function index(Request $request)
    {
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

        return Inertia::render('Products/Showcase', [
            'products' => $products,
            'categories' => $categories,
            'departments' => $departments,
            'filters' => $request->only(['search', 'category', 'department'])
        ]);
    }
}
