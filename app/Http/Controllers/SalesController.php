<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'payment', 'items.product.department'])
            ->orderBy('created_at', 'desc');

        // Filtro por status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filtro por período
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->paginate(10);

        return Inertia::render('Sales/Index', [
            'orders' => $orders,
            'filters' => $request->only(['status', 'start_date', 'end_date'])
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'payment', 'items.product.department', 'items.product', 'shippingMethod']);

        return Inertia::render('Sales/Show', [
            'order' => $order
        ]);
    }
} 