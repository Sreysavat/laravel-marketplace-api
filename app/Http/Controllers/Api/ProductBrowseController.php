<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductBrowseController extends Controller
{
    public function index(Request $request){
        // Implementation for browsing products
        $products = Product::with(['images', 'variants', 'category'])->where('status', 'active');
         //filter products by category
         if($request->category_id){
        $products->where('category_id', $request->category_id);
    }
    //minimum price filter
    if($request->min_price){
        $products->where('price', '>=', $request->min_price);
    }
    //maximum price filter
    if($request->max_price){
        $products->where('price', '<=', $request->max_price);
    }
    //search by name
    if($request->search){
        $products->where('name', 'LIKE', '%'.$request->search.'%');
    }
    $products = $products->latest()->paginate(10);

        return response()->json($products);
    }
    //show single product
    public function show($id){
        $product = Product::with(['images', 'variants.inventory', 'category'])->where('status', 'active')->findOrFail($id);
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'category' => [
                'id' => optional($product->category)->id,
                'name' => optional($product->category)->name,
            ],
            'main_image' => optional(
                $product->images->where('is_main', 1)->first())->image_url,
            'images' => $product->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'url' => $img->image_url,
                ];
            }),
            'variants' => $product->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'price' => $v->price,
                    'stock' => optional($v->inventory)->stock,
                    'attributes' => $v->attributes,
                ];
            }),
        ]);
    }
}