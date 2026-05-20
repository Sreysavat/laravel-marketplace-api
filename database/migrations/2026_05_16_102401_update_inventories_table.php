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
    Schema::table('inventories', function (Blueprint $table) {

        // only add if not exists
        if (!Schema::hasColumn('inventories', 'product_variant_id')) {

            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

        }

        // remove old column if exists
        if (Schema::hasColumn('inventories', 'product_id')) {
             $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        }

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('inventories', function (Blueprint $table) {

        if (Schema::hasColumn('inventories', 'product_variant_id')) {

            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');

        }

        if (!Schema::hasColumn('inventories', 'product_id')) {

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

        }

    });
}
};