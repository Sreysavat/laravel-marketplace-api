<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
   class Payment extends Model
{
     protected $fillable = [
        'order_id',
        'provider',
        'reference',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'response',
    ];

     protected $casts = [
        'response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}