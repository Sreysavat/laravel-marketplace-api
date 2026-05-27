<?php

namespace App\States\Vendor;

class Rejected extends VendorState
{
    public static function label(): string
    {
        return 'rejected';
    }
}