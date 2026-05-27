<?php

namespace App\Services;

use Exception;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function deduct(Order $order)
    {
        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {

                $inventory = $item->variant
                    ->inventory()
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    throw new Exception(
                        'Inventory not found'
                    );
                }

                if ($inventory->stock < $item->quantity) {
                    throw new Exception(
                        'Insufficient stock'
                    );
                }

                $inventory->decrement(
                    'stock',
                    $item->quantity
                );
            }
        });
    }

    public function restore(Order $order)
    {
        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {

                $inventory = $item->variant
                    ->inventory()
                    ->lockForUpdate()
                    ->first();

                if ($inventory) {

                    $inventory->increment(
                        'stock',
                        $item->quantity
                    );
                }
            }
        });
    }
}