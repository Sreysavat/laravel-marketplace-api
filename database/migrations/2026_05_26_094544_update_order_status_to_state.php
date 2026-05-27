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
        DB::table('orders')
            ->where('status', 'pending')
            ->update([
                'status' => 'pending'
            ]);
    }

    public function down(): void
    {
        //
    }
};