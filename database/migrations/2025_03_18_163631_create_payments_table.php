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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->string('payment_method'); // credit_card, pix
            $table->string('payment_gateway')->default('pagseguro');
            $table->string('transaction_id')->nullable();
            $table->string('status'); // pending, approved, refused, refunded, cancelled
            $table->decimal('amount', 10, 2);
            $table->json('gateway_response')->nullable();
            
            // Campos para cartão de crédito
            $table->string('card_brand')->nullable();
            $table->string('card_last_digits')->nullable();
            $table->string('installments')->nullable();
            
            // Campos para PIX
            $table->text('pix_qr_code')->nullable();
            $table->text('pix_qr_code_url')->nullable();
            $table->text('pix_code')->nullable();
            $table->timestamp('pix_expiration_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
