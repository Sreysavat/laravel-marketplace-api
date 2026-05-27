<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class VendorDashboardController extends Controller
{
    //
    public function dashboard(){
        $vendorId = auth()->id();
    //total product
        $totalProducts = Product::where('vendor_id',$vendorId)->count();
    //vendor order
        $baseOrders = Order::whereHas('items.product', function ($query) use ($vendorId) {
        $query->where('vendor_id', $vendorId);

        });
        $totalOrders = $baseOrders->count();
        
        $pendingOrders = Order::whereHas('items.product', function ($query) use ($vendorId) {
        $query->where('vendor_id', $vendorId);
    })->where('status', 'pending')->count();

    $completedOrders = Order::whereHas('items.product', function ($query) use ($vendorId) {
        $query->where('vendor_id', $vendorId);
    })->where('status', 'completed')->count();

    $totalSales = Order::whereHas('items.product', function ($query) use ($vendorId) {
        $query->where('vendor_id', $vendorId);
    })->where('status', 'completed')->sum('total');
        return response()->json([
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'total_sales' => $totalSales,
        ]);
    }
}