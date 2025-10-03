<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $totalUsers    = User::count();
        $revenue       = Order::where('status', 'completed')->sum('total_price');

        return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalUsers', 'revenue'));
    }
}
