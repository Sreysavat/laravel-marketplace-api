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
        Schema::table('payments', function (Blueprint $table) {

             if (!Schema::hasColumn('payments', 'paid_at')) {
        $table->timestamp('paid_at')->nullable()->after('status');
    }

    if (!Schema::hasColumn('payments', 'deeplink')) {
        $table->string('deeplink')->nullable()->after('transaction_id');
    }

    if (!Schema::hasColumn('payments', 'is_processed')) {
        $table->boolean('is_processed')->default(false);
    }

    if (!Schema::hasColumn('payments', 'failed_at')) {
        $table->timestamp('failed_at')->nullable();
    }

    if (!Schema::hasColumn('payments', 'failure_reason')) {
        $table->string('failure_reason')->nullable();
    }

    if (!Schema::hasColumn('payments', 'webhook_payload')) {
        $table->json('webhook_payload')->nullable();
    }
     });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropIndex(['order_id', 'status']);
            $table->dropIndex(['reference']);

            $table->dropColumn([
                'paid_at',
                'deeplink',
                'is_processed',
                'failed_at',
                'failure_reason',
                'webhook_payload'
            ]);
        });
    }
};