<?php

namespace App\States\Order;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class OrderState extends State
{
    abstract public static function label(): string;

    public static function config(): StateConfig
    {
        return parent::config()

            ->default(Pending::class)

            ->allowTransition(Pending::class, Paid::class)
            ->allowTransition(Paid::class, Packed::class)
            ->allowTransition(Packed::class, Shipped::class)
            ->allowTransition(Shipped::class, Delivered::class)
            ->allowTransition(Delivered::class, Completed::class)
            ->allowTransition(Pending::class, Cancelled::class)

            ->registerState([
                'pending' => Pending::class,
                'paid' => Paid::class,
                'packed' => Packed::class,
                'shipped' => Shipped::class,
                'delivered' => Delivered::class,
                'completed' => Completed::class,
                'cancelled' => Cancelled::class,
            ]);
    }
}