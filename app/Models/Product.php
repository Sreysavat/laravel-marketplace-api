<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\Vendor;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'description',
        'price',
        'sale_price',
        'sku',
        'status'
    ];
    public function store()
{
    return $this->belongsTo(Store::class);
}
 public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
