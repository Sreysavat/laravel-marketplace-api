<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
   public function store(Request $request, $productId)
{
    $request->validate([
        'images' => 'required',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'product_variant_id' => 'nullable|exists:product_variants,id',
    ]);

    $product = Product::findOrFail($productId);

    $uploadedImages = [];

    $files = $request->file('images');

    if (!is_array($files)) {
        $files = [$files];
    }

    foreach ($files as $index => $file) {

        $path = $file->store('products', 'public');

        $isMain = !$product->images()
            ->where('is_main', true)
            ->exists();

        $image = ProductImage::create([
            'product_id' => $product->id,
            'product_variant_id' => $request->product_variant_id,
            'image_path' => $path,
            'is_main' => $isMain,
            'sort_order' => $index + 1,
        ]);

        $uploadedImages[] = $image;
    }

    return response()->json([
        'message' => 'Images uploaded successfully',
        'images' => $uploadedImages
    ], 201);
}
        public function index($productId)
    {
        $images = ProductImage::where('product_id', $productId)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'images' => $images
        ]);
    }

    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
    }
    public function setMain($id)
    {
        $image = ProductImage::findOrFail($id);
        ProductImage::where('product_id', $image->product_id)
            ->update([
                'is_main' => false
            ]);

        // selected image as main
        $image->update([
            'is_main' => true
        ]);

        return response()->json([
            'message' => 'Main image updated successfully',
            'image' => $image
        ]);
    }
}