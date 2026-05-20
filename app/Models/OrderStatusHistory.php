<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;   

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'note',
        'changed_by'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
