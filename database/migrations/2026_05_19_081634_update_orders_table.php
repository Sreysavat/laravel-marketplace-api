<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::table('orders', function (Blueprint $table) {

            // customer
            $table->foreignId('user_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            // order number
            $table->string('order_number')
                ->unique()
                ->after('user_id');

            // money
            $table->decimal('subtotal', 10, 2)
                ->default(0)
                ->after('order_number');

            $table->decimal('shipping_fee', 10, 2)
                ->default(0);

            $table->decimal('tax', 10, 2)
                ->default(0);

            $table->decimal('discount', 10, 2)
                ->default(0);

            $table->decimal('total', 10, 2)
                ->default(0);

            // order status
            $table->enum('status', [
                'pending',
                'paid',
                'packed',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('pending');

            // payment status
            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed'
            ])->default('pending');

            // address snapshot
            $table->text('shipping_address')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'user_id',
                'order_number',
                'subtotal',
                'shipping_fee',
                'tax',
                'discount',
                'total',
                'status',
                'payment_status',
                'shipping_address'
            ]);
        });
    }
};