<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
     protected $fillable = [
        'vendor_id',
        'amount',
        'method',
        'account_name',
        'account_number',
        'status',
        'note',
        'approved_by',
        'approved_at',
        'paid_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
