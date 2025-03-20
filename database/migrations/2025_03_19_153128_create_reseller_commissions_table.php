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
        Schema::create('reseller_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('product_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('commission_rate', 5, 2); // Percentual da comissão (ex: 20.00 para 20%)
            $table->decimal('commission_amount', 10, 2); // Valor da comissão
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Garantir que cada item de pedido só tenha uma comissão
            $table->unique(['order_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_commissions');
    }
};
