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
        Schema::create('payouts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('vendor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);

            $table->string('method')
                ->nullable();
            // bank, bakong, aba, wing

            $table->string('account_name')
                ->nullable();

            $table->string('account_number')
                ->nullable();

            $table->string('status')
                ->default('pending');
            // pending approved rejected paid

            $table->text('note')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};