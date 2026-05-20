<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Inventory;
class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'price',
    ];
    protected $hidden = ['stock'];
    protected $casts = [
        'attributes' => 'array',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function inventory()
    {
     return $this->hasOne(Inventory::class, 'product_variant_id');
    }  
}  
