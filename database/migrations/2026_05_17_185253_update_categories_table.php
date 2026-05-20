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
        Schema::table('categories', function (Blueprint $table) {

            $table->string('name')->after('id');
            $table->string('slug')->unique()->after('name');

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->cascadeOnDelete()
                ->after('slug');

            $table->string('image')->nullable()->after('parent_id');

            $table->boolean('is_active')->default(true)->after('image');

            $table->unsignedInteger('sort_order')->default(0)->after('is_active');

            // 🔥 nested set (required by your project spec)
            $table->unsignedInteger('_lft')->nullable()->index();
            $table->unsignedInteger('_rgt')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            $table->dropColumn([
                'name',
                'slug',
                'parent_id',
                'image',
                'is_active',
                'sort_order',
                '_lft',
                '_rgt'
            ]);
        });
    }
};