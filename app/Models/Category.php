<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use App\Models\Product;
class Category extends Model
{
    use NodeTrait;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'image',
        'is_active',
        'sort_order',
    ];

     public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
