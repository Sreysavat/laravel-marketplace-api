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
        Schema::table('product_images', function (Blueprint $table) {
           $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('is_main');
        });
    }
    
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
            $table->dropColumn('sort_order');
        });
    }
};
