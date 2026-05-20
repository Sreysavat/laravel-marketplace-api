<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderStatusController extends Controller
{
    public function updateStatus(Request $request, int $orderId){
        $request->validate([
        'status'=> 'required|string',
        'note' => 'nullable|string'
        ]);

         if (!Auth::user() || !Auth::user()->hasRole('super-admin')) {
        return response()->json([
            'message' => 'Unauthorized. Only admin can update order status.'
        ], 403);
    }

        //update order status
       $order = Order::where('id', $orderId)->where('user_id', Auth::id())->firstOrFail();
        if ($order->status === $request->status) {
        return response()->json([
        'message' => 'Order already has this status'
    ], 400);
}
    $allowedTransitions = [
        'pending' => ['paid', 'cancelled'],
        'paid' => ['packed', 'refunded'],
        'packed' => ['shipped'],
        'shipped' => ['delivered'],
        'delivered' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    $currentStatus = $order->status;
    $newStatus = $request->status;

    if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
        return response()->json([
            'message' => "Invalid status transition from $currentStatus to $newStatus"
        ], 400);
    }

        $order->status= $request->status;
        $order->save();

        //save history
        $order->statusHistories()->create([
            'status' => $request->status,
            'note' => $request->note,
            'changed_by' => Auth::id()
        ]);
        return response()->json([
        'message' => 'Order status updated successfully',
        'data' => [
        'order_id' => $order->id,
        'order_number' => $order->order_number,
        'status' => $order->status,
    ]
     ]);
     
    }

    public function timeline(int $orderId)
{
    $order = Order::with('statusHistories')
    ->where('user_id', Auth::id())
    ->where('id', $orderId)
    ->first();

    if (!$order) {
    return response()->json([
        'message' => 'Order not found'
    ], 404);
}

    return response()->json([
        'order_number' => $order->order_number,
        'status' => $order->status,
        'timeline' => $order->statusHistories
    ]);
}

}
