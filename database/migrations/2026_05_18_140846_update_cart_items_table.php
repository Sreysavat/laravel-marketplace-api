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
       Schema::table('cart_items', function (Blueprint $table) {

            // add cart relationship
            $table->foreignId('cart_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            // product relation
            $table->foreignId('product_id')
                ->after('cart_id')
                ->constrained()
                ->cascadeOnDelete();

            // optional variant
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // quantity
            $table->integer('quantity')
                ->default(1)
                ->after('product_variant_id');

            // price snapshot
            $table->decimal('price', 10, 2)
                ->after('quantity');

            // prevent duplicate same product in cart
            $table->unique([
                'cart_id',
                'product_id',
                'product_variant_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('cart_items', function (Blueprint $table) {

            $table->dropUnique([
                'cart_id',
                'product_id',
                'product_variant_id'
            ]);

            $table->dropForeign(['cart_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);

            $table->dropColumn([
                'cart_id',
                'product_id',
                'product_variant_id',
                'quantity',
                'price'
            ]);
        });
    }
};