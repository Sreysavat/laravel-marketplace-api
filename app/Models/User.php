<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Models\VendorApplication;
use App\Models\VendorWallet;
use App\Models\Payout;
use App\Models\VendorTransaction;

#[Fillable(['name', 'email', 'password', 'phone', 'image', 'role_id','fcm_token'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasRoles, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
//     public function vendor()
// {
//     return $this->hasOne(Vendor::class);
// }
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // // }
    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'vendor_id');
    // }

    public function vendorApplication()
{
    return $this->hasOne(VendorApplication::class);
}
 public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }
    public function wallet()
{
    return $this->hasOne(VendorWallet::class, 'vendor_id');
}

public function vendorTransactions()
{
    return $this->hasMany(VendorTransaction::class, 'vendor_id');
}

public function payouts()
{
    return $this->hasMany(Payout::class, 'vendor_id');
}
}
