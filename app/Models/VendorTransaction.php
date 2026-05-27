<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorTransaction extends Model
{
    protected $fillable = [
        'vendor_id',
        'order_id',
        'order_item_id',
        'amount',
        'commission',
        'net_amount',
        'type'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}