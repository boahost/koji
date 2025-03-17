<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Adicionando campos que podem estar faltando
            if (!Schema::hasColumn('customers', 'document')) {
                $table->string('document')->unique();
            }
            if (!Schema::hasColumn('customers', 'cep')) {
                $table->string('cep', 8);
            }
            if (!Schema::hasColumn('customers', 'street')) {
                $table->string('street');
            }
            if (!Schema::hasColumn('customers', 'number')) {
                $table->string('number', 20);
            }
            if (!Schema::hasColumn('customers', 'complement')) {
                $table->string('complement')->nullable();
            }
            if (!Schema::hasColumn('customers', 'neighborhood')) {
                $table->string('neighborhood');
            }
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city');
            }
            if (!Schema::hasColumn('customers', 'state')) {
                $table->string('state', 2);
            }
            if (!Schema::hasColumn('customers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'document',
                'cep',
                'street',
                'number',
                'complement',
                'neighborhood',
                'city',
                'state',
                'deleted_at'
            ]);
        });
    }
};
