<?php
namespace App\States\Vendor;
class Pending extends VendorState
{
    public static function label(): string
    {
        return 'pending';
    }
}