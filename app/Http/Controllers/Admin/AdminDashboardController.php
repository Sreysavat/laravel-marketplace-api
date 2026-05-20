<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;

class AdminDashboardController extends Controller
{
    
     public function index()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_vendors' => Vendor::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
        ]);
    }
}
