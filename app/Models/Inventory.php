<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;

class Inventory extends Model
{
    protected $fillable = [
    'product_variant_id',
    'stock',
    'reserved_stock',
];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
