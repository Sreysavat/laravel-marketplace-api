<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\auth;

class CheckoutController extends Controller
{
    public function checkout(Request $request){
    $request->validate([
    'shipping_address' => 'required|string|max:1000',
]);
    $user = auth::user();

    $cart = Cart::where('user_id', $user->id)->with(['items.product','items.variant'])->first();
    //cart empty
     if (!$cart || $cart->items->isEmpty()) {

        return response()->json([
            'message' => 'Cart is empty'
        ], 400);
    }
     DB::beginTransaction();

     try{
        $subtotal = 0;

        foreach($cart->items as $item){
            
                if ($item->variant) {
    $inventory = $item->variant->inventory;

    if (!$inventory || $inventory->stock < $item->quantity) {
        return response()->json([
            'message' => 'Insufficient stock for ' . $item->product->name
        ], 400);
    }
} else {
    if ($item->product->stock < $item->quantity) {
        return response()->json([
            'message' => 'Insufficient stock for ' . $item->product->name
        ], 400);
    }
}
                $subtotal += (
                    $item->price * $item->quantity
                );
            }
        $shippingFee = 0;
        $tax = 0;
        $discount = 0;

          $total = (
            $subtotal
            + $shippingFee
            + $tax
            - $discount
        );
    //create order
    $order = Order::create([

            'user_id' => $user->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_address' => $request->shipping_address,
    ]);
        $order->statusHistories()->create([
        'status' => 'pending',
        'note' => 'Order created',
        'changed_by' => Auth::id(),
    ]);
        // create order items
        foreach($cart ->items as $item){
            OrderItem::create([
            'order_id' =>$order->id,
            'product_id' => $item->product_id,
            'product_variant_id' => $item->product_variant_id,
            'product_name' => $item->product->name,
            'sku' => optional($item->variant)->sku,
            'price' => $item->price,
            'quantity' => $item->quantity,


                'total' => (
                    $item->price * $item->quantity
                ),
            ]);
                // decrement stock
                if (
                    $item->variant &&
                    $item->variant->inventory
                ) {

                    $item->variant->inventory
                        ->decrement(
                            'stock',
                            $item->quantity
                        );
                }
            }
    // clear cart
      $cart->items()->delete();

        DB::commit();
        return response()->json([
        'message' => 'Checkout successful',
        'order' => $order->load('items')
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}
}
