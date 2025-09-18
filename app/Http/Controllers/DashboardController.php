<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Reseller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalResellers = Reseller::count();

        // Estatísticas de vendas
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Vendas de hoje
        $todaySales = Order::whereDate('created_at', $today)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total');

        // Vendas do mês atual
        $thisMonthSales = Order::where('created_at', '>=', $thisMonth)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total');

        // Vendas do mês anterior
        $lastMonthSales = Order::whereBetween('created_at', [$lastMonth, $thisMonth])
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total');

        // Total de vendas
        $totalSales = Order::whereIn('status', ['completed', 'processing'])->sum('total');

        // Vendas dos últimos 7 dias
        $last7DaysSales = Order::where('created_at', '>=', Carbon::now()->subDays(7))
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total_sales, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d/m'),
                    'total' => (float) $item->total_sales,
                    'orders' => $item->orders_count
                ];
            });

        // Vendas dos últimos 30 dias (para gráfico)
        $last30DaysSales = Order::where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d/m'),
                    'total' => (float) $item->total_sales
                ];
            });

        // Top produtos vendidos
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', ['completed', 'processing'])
            ->selectRaw('products.name, SUM(order_items.quantity) as total_quantity, SUM(order_items.quantity * order_items.price) as total_revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'statistics' => [
                'customers' => $totalCustomers,
                'products' => $totalProducts,
                'resellers' => $totalResellers,
                'sales' => [
                    'today' => $todaySales,
                    'thisMonth' => $thisMonthSales,
                    'lastMonth' => $lastMonthSales,
                    'total' => $totalSales,
                    'last7Days' => $last7DaysSales,
                    'last30Days' => $last30DaysSales,
                    'topProducts' => $topProducts
                ]
            ]
        ]);
    }
}
