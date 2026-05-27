<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class VendorOrderController extends Controller
{
    //  order list
    public function index(){
        $vendorId = auth()->id();
        $orders = Order::whereHas('items.product',
        function ($query) use ($vendorId){
            $query ->where('vendor_id',$vendorId);
        })->with([
            'user', 'items.product'
        ])->latest()->get();

     return response()->json([
        'orders' => $orders,
        'vendor_id' => auth()->id(),
        'products' => auth()->user()->products
    ]);
}
    //show single
    public function show($id){
        $vendorId = auth()->id();

            $order = Order::whereHas(
        'items.product',
        function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->with([
        'user',
        'items.product',
        'statusHistories'
        ])->findOrFail($id);

    return response()->json([
        'order' => $order
    ]);
}
//update
    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:processing,shipped,delivered,cancelled'
    ]);

    $vendorId = auth()->id();

    $order = Order::whereHas(
        'items.product',
        function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }
    )->findOrFail($id);

    $order->update([
        'status' => $request->status
    ]);

    $order->statusHistories()->create([
        'status' => $request->status,
        'note' => 'Updated by vendor',
        'changed_by' => auth()->id(),
    ]);

    return response()->json([
        'message' => 'Order status updated successfully',
        'order' => $order
    ]);
}
}
