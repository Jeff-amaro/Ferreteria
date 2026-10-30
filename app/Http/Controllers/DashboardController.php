<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Alert;
use App\Models\Client;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        if ($user->role === UserRole::Cliente) {
            return redirect()->route('catalog.index');
        }

        $stats = [
            'sales_today' => (float) SalesOrder::whereDate('created_at', today())
                ->where('status', '!=', 'anulado')->sum('total'),
            'sales_month' => (float) SalesOrder::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', '!=', 'anulado')->sum('total'),
            'orders_pending' => SalesOrder::pending()->count(),
            'products' => Product::active()->count(),
            'low_stock' => Product::active()->lowStock()->count(),
            'clients' => Client::active()->count(),
            'quotations' => Quotation::whereIn('status', ['borrador', 'emitida'])->count(),
            'alerts' => Alert::pending()->count(),
        ];

        $recentOrders = SalesOrder::with('client')
            ->latest()
            ->take(6)
            ->get();

        $lowStockProducts = Product::with(['category', 'supplier'])
            ->active()
            ->lowStock()
            ->orderBy('stock')
            ->take(6)
            ->get();

        $topProducts = SalesOrderItem::selectRaw('product_name, sku, SUM(quantity) as total_quantity, SUM(subtotal) as total_amount')
            ->whereHas('salesOrder', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30))
                    ->where('status', '!=', 'anulado');
            })
            ->groupBy('product_name', 'sku')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        $monthlySales = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);

            return [
                'label' => $date->translatedFormat('M'),
                'total' => (float) SalesOrder::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', '!=', 'anulado')
                    ->sum('total'),
            ];
        });

        $maxMonthly = max(1, (float) $monthlySales->max('total'));

        return view('dashboard', compact(
            'stats',
            'recentOrders',
            'lowStockProducts',
            'topProducts',
            'monthlySales',
            'maxMonthly'
        ));
    }
}
