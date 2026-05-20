<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;

class CartController extends Controller
{
    //view cart items for the authenticated user
    public function index(){
        $user = auth()->user();
        $cart = Cart::where('user_id' ,$user->id)->with('items.product.images', 'items.variant')->first();

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => 0,
                'subtotal' => 0,
            ], 200);
        }
        $subtotal =0;
        $totalItems = 0;
       $items = $cart->items->map(function($item) use (&$subtotal, &$totalItems) {
           $price = $item->price;
           $total = $price * $item->quantity;
              $subtotal += $total;
                $totalItems += $item->quantity;

        //main image of the product
        $image = optional($item->product->images->where('is_main', 1)->first())->image_url;
        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'name' => $item->product->name,
            'image' => $image,

        'variant' => $item->variant ? [
                'id' => $item->variant->id,
                'sku' => $item->variant->sku,
                'attributes' => $item->variant->attributes,
                'price' => $item->variant->price,
            ] : null,

            'quantity' => $item->quantity,
            'price' => $price,
            'total' => $total,
        ];
    });

        return response()->json([
            'items' => $items,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
        ], 200);
    }

    // Add item to cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $user = auth::user();
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);
        $product = Product::findOrFail($request->product_id);

       $price = $product->price;// Default to product price

        if ($request->product_variant_id) {// If variant is selected, get the price of the variant
            $variant = ProductVariant::findOrFail($request->product_variant_id);//get price of the variant
            $price = $variant->price;// Use variant price if variant is selected
        }
        $item = CartItem::where([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id,
        ])->first(); 

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }
        return response()->json([
            'message' => 'Item added to cart successfully',
        ], 200);
    }

    //update 
    public function update(Request $request ,$id){
        $request ->validate([
        'quantity' => 'required|integer|min:1'
        ]);
    $user = auth::user();
    //get user cart
    $cart = Cart::where('user_id', $user->id)->firstOrFail();
    //get item from cart
     $item = CartItem::where('cart_id', $cart->id)->where('id', $id)->firstOrFail();

    $item->update([
    'quantity' => $request->quantity
    ]);
    return response()->json([
        'message' => 'Cart update successfuly',
        'item' => $item
    ]);
    }

    // remove item cart 

    public function remove($id){
        $user = auth::user();
        $cart = Cart::where('user_id',$user->id)->firstOrFail();

        $item = CartItem::where('cart_id', $cart->id)->where('id',$id)->firstOrFail();
        $item->delete();

        return response()->json([
            'message' => 'Delete successfully'
        ]);
    }

    //clear cart 
public function clear()
{
    $user = auth::user();

    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {

        return response()->json([
            'message' => 'Cart is already empty'
        ]);
    }

    $cart->items()->delete();

    return response()->json([
        'message' => 'Cart cleared successfully'
    ]);
}
}
