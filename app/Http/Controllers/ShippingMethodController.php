<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShippingMethodController extends Controller
{
    public function index()
    {
        return Inertia::render('ShippingMethods/Index', [
            'shippingMethods' => ShippingMethod::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('ShippingMethods/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'value' => 'required|numeric|min:0',
            'active' => 'required|boolean'
        ]);

        ShippingMethod::create($validated);

        return redirect()->route('shipping-methods.index')
            ->with('success', 'Frete cadastrado com sucesso!');
    }

    public function edit(ShippingMethod $shippingMethod)
    {
        return Inertia::render('ShippingMethods/Edit', [
            'shippingMethod' => $shippingMethod
        ]);
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'value' => 'required|numeric|min:0',
            'active' => 'required|boolean'
        ]);

        $shippingMethod->update($validated);

        return redirect()->route('shipping-methods.index')
            ->with('success', 'Frete atualizado com sucesso!');
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        return redirect()->route('shipping-methods.index')
            ->with('success', 'Frete excluído com sucesso!');
    }
}
