<?php

namespace App\Models;
use App\States\Vendor\VendorState;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;
class Vendor extends Model
{
     use HasStates;
     protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'address',
        'logo',
        'banner',
        'description',
        'status',
        'approved_at',
        'suspended_at',
    ];

   protected $casts = [
        'status' => VendorState::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}