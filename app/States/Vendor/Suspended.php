<?php

namespace App\States\Vendor;

class Suspended extends VendorState
{
    public static function label(): string
    {
        return 'suspended';
    }
}