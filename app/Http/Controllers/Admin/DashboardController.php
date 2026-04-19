<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        $stats = [
            'today_orders' => Order::today()->count(),
            'today_revenue' => Order::today()->where('status', '!=', 'cancelled')->sum('total'),
            'pending_orders' => Order::pending()->count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
        ];
        
        $recentOrders = Order::with('items')
            ->latest()
            ->take(10)
            ->get();
        
        $pendingOrders = Order::with('items')
            ->pending()
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recentOrders', 'pendingOrders'));
    }
}
