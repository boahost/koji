<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('codigo_imovel');
            $table->json('resposta_api');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_queries');
    }
}; 