<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\States\Order\OrderState;
use Spatie\ModelStates\HasStates;
class Order extends Model
{
     use HasStates;
    protected $casts = [
    'status' => OrderState::class,
];
    protected $fillable = [
    'user_id',
    'order_number',
    'subtotal',
    'shipping_fee',
    'tax',
    'discount',
    'total',
    'status',
    'payment_status',
    'shipping_address',
    ];
    public function items(){
        return $this->hasMany(OrderItem::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function statusHistories()
{
    return $this->hasMany(OrderStatusHistory::class);
}
public function payment()
{
    return $this->hasOne(Payment::class);
}
}
