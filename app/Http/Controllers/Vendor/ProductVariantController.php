<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{

    public function store(Request $request, $productId)
    {
        $request->validate([
            'sku' => 'required|unique:product_variants',
            'price' => 'nullable|numeric',
            'attributes' => 'nullable|array',
            'stock' => 'nullable|integer|min:0',
        ]);
        $attributes = $request->input('attributes', []);

        if (is_string($attributes)) {
            $attributes = json_decode($attributes, true) ?? [];
        }

        $variant = ProductVariant::create([
            'product_id' => $productId,
            'sku' => $request->sku,
            'price' => $request->price,
            'attributes' => $attributes,
        ]);
        

        Inventory::create([
            'product_variant_id' => $variant->id,
            'stock' => $request->stock ,
            'reserved_stock' => 0,
        ]);
        return response()->json([
            'message' => 'Product variant created successfully',
            'variant' => $variant->load('inventory')
        ]);
       
    }
    public function index($productId)
    {
        return ProductVariant::where('product_id', $productId)->with('inventory')->get();
    }
    public function show($id)
{
    return ProductVariant::with('inventory')->findOrFail($id);
}
    public function update(Request $request, $id)
    {
        $variant = ProductVariant::find($id);
        $data = $request->all();
        if ($request->has('stock')) {
            if ($variant->inventory) {
                $variant->inventory->update([
                    'stock' => $request->stock,
                ]);
            } else {
                Inventory::create([
                    'product_variant_id' => $variant->id,
                    'stock' => $request->stock,
                    'reserved_stock' => 0,
                ]);
            }
        }
           $variant->update($data);
        return response()->json([
            'message' => 'Product variant updated successfully',
            'variant' => $variant->load('inventory')
        ]);
    }
    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        return response()->json([
            'message' => 'Product variant deleted successfully'
        ]);
    }
}
