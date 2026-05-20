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
        Schema::table('order_items', function (Blueprint $table) {

            // relation to order
            $table->foreignId('order_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            // product
            $table->foreignId('product_id')
                ->after('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // optional variant
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained()
                ->nullOnDelete();

            // snapshot data
            $table->string('product_name')
                ->after('product_variant_id');

            $table->string('sku')
                ->nullable()
                ->after('product_name');

            $table->decimal('price', 10, 2)
                ->after('sku');

            $table->integer('quantity')
                ->after('price');

            $table->decimal('total', 10, 2)
                ->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);

            $table->dropColumn([
                'order_id',
                'product_id',
                'product_variant_id',
                'product_name',
                'sku',
                'price',
                'quantity',
                'total'
            ]);
        });
    }
};