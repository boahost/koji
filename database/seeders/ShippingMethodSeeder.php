<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se já existem métodos de frete
        if (ShippingMethod::count() > 0) {
            $this->command->info('Métodos de frete já existem. Pulando...');
            return;
        }
        
        // Cria os métodos de frete
        ShippingMethod::create([
            'name' => 'Entrega Padrão',
            'description' => 'Entrega em até 7 dias úteis',
            'value' => 15.90,
            'active' => true,
        ]);
        
        ShippingMethod::create([
            'name' => 'Entrega Expressa',
            'description' => 'Entrega em até 3 dias úteis',
            'value' => 25.90,
            'active' => true,
        ]);
        
        ShippingMethod::create([
            'name' => 'Entrega no Mesmo Dia',
            'description' => 'Entrega no mesmo dia para pedidos até 12h',
            'value' => 39.90,
            'active' => true,
        ]);
        
        $this->command->info('Métodos de frete criados com sucesso!');
    }
}
