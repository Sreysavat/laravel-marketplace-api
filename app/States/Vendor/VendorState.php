<?php

namespace App\States\Vendor;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class VendorState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransition(Pending::class, Approved::class)
            ->allowTransition(Pending::class, Rejected::class)
            ->allowTransition(Approved::class, Suspended::class);
    }
}