<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // public function index()
    // {
    //     $vendor = auth()->user()->vendor;//get products for the authenticated vendor with inventory data
    //     $products = Product::where('vendor_id', $vendor->id)->
    //     with(['images', 'variants.inventory'])->latest()->get();

    //     return response()->json($products);
    // }
    // // show sigle product
//     public function show($id)
//     {
//         $product = Product::with(['images', 'variants.inventory'])->findOrFail($id);
//         return response()->json($product);
//     }
//Update product

 public function index()
    {
        $vendor = auth()->user()->vendor;

        $products = Product::with(['images','variants', 'category' ])->where('vendor_id', $vendor->id)->get();

        return response()->json([
            'products' => $products->map(function ($product) {

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category' => [
                    'id' => optional($product->category)->id,
                    'name' => optional($product->category)->name,
                ],

                    'main_image' => optional(
                        $product->images->where('is_main', 1)->first()
                    )->image_url,

                    'images' => $product->images->map(function ($img) {
                        return [
                            'id' => $img->id,
                            'url' => $img->image_url,
                            'is_main' => $img->is_main,
                            'sort_order' => $img->sort_order,
                        ];
                    }),
                    'variants' => $product->variants->map(function ($v) {
                        return [
                            'id' => $v->id,
                            'sku' => $v->sku,
                            'price' => $v->price,
                            'attributes' => $v->attributes,
                        ];
                        
                    }),
                ];
            })
        ]);
    }

    // 📦 Single product
    public function show($id)
    {
       $vendor = auth()->user()->vendor;

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,

            'main_image' => optional(
                $product->images->where('is_main', 1)->first()
            )->image_url,

            'images' => $product->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'url' => $img->image_url,
                    'is_main' => $img->is_main,
                    'sort_order' => $img->sort_order,
                ];
            }),

            'variants' => $product->variants,
        ]);
    }

//create product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'sku' => 'required|unique:products',
            'category_id' => 'required|exists:categories,id',
        ]);
        $vendor = auth()->user()->vendor;
        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'sku' => $request->sku,
            'status' => 'active',
        ]);
        //create inventory record
        $variant = $product->variants()->create([
        'sku' => $request->sku . '-DEFAULT',
        'price' => $request->price,
        'attributes' => null,
]);

$variant->inventory()->create([
    'stock' => $request->stock ?? 0,
    'reserved_stock' => 0,
]);
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product->load('variants.inventory')
        ], 201);    
    }

   public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // update product only safe fields
    $product->update($request->only([
        'name',
        'description',
        'price',
        'sale_price',
        'category_id',
        'status'
    ]));

    // update default variant (first variant)
    $variant = $product->variants()->first();

    if ($variant) {
        $variant->update([
            'sku' => $request->sku ?? $variant->sku,
            'price' => $request->price ?? $variant->price,
        ]);

        // update inventory
        if ($variant->inventory && $request->has('stock')) {
            $variant->inventory->update([
                'stock' => $request->stock
            ]);
        }
    }

    return response()->json([
        'message' => 'Product updated successfully',
        'data' => $product->load('variants.inventory')
    ]);
}
}
