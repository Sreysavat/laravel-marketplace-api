<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image_path',
        'is_main',
        'sort_order',

    ];
    protected $appends = ['image_url'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
