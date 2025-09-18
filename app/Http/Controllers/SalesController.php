<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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

    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Vendas por dia no período
        $dailySales = Order::whereIn('status', ['completed', 'processing'])
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total_sales, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d/m/Y'),
                    'total' => (float) $item->total_sales,
                    'orders' => $item->orders_count
                ];
            });

        // Vendas por status
        $salesByStatus = Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->selectRaw('status, SUM(total) as total_sales, COUNT(*) as orders_count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'total' => (float) $item->total_sales,
                    'orders' => $item->orders_count
                ];
            });

        // Top produtos vendidos no período
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', ['completed', 'processing'])
            ->whereBetween('orders.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->selectRaw('products.name, SUM(order_items.quantity) as total_quantity, SUM(order_items.quantity * order_items.price) as total_revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Resumo geral
        $summary = [
            'total_sales' => Order::whereIn('status', ['completed', 'processing'])
                ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                ->sum('total'),
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                ->count(),
            'completed_orders' => Order::whereIn('status', ['completed', 'processing'])
                ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                ->count(),
            'average_order_value' => Order::whereIn('status', ['completed', 'processing'])
                ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                ->avg('total') ?? 0
        ];

        return Inertia::render('Sales/Reports', [
            'dailySales' => $dailySales,
            'salesByStatus' => $salesByStatus,
            'topProducts' => $topProducts,
            'summary' => $summary,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
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