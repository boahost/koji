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
        if (Schema::hasTable('reseller_commissions') && !Schema::hasColumn('reseller_commissions', 'amount')) {
            Schema::table('reseller_commissions', function (Blueprint $table) {
                $table->decimal('amount', 10, 2)->after('order_id');
            });
        }

        if (Schema::hasTable('reseller_commissions') && !Schema::hasColumn('reseller_commissions', 'payment_data')) {
            Schema::table('reseller_commissions', function (Blueprint $table) {
                $table->json('payment_data')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reseller_commissions') && Schema::hasColumn('reseller_commissions', 'amount')) {
            Schema::table('reseller_commissions', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }

        if (Schema::hasTable('reseller_commissions') && Schema::hasColumn('reseller_commissions', 'payment_data')) {
            Schema::table('reseller_commissions', function (Blueprint $table) {
                $table->dropColumn('payment_data');
            });
        }
    }
}; 