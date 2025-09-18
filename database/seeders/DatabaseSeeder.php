<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Database\Seeders\ShippingMethodSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria o usuário de teste apenas se não existir
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            $this->command->info('Usuário de teste criado com sucesso!');
        } else {
            $this->command->info('Usuário de teste já existe. Pulando...');
        }
        
        // Cria o usuário administrador apenas se não existir
        if (!User::where('email', 'admin@admin.com')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]);
            $this->command->info('Usuário administrador criado com sucesso!');
        } else {
            $this->command->info('Usuário administrador já existe. Pulando...');
        }
        
        // Cria produto padrão para crédito na carteira
        if (!Product::where('id', 9999)->exists()) {
            Product::create([
                'id' => 9999,
                'name' => 'Crédito na Carteira',
                'description' => 'Produto interno para adicionar saldo à carteira do cliente.',
                'price' => 0.01, // Preço será ajustado no carrinho
                'category_id' => 1, // Ajuste se necessário
                'department_id' => 1, // Ajuste se necessário
                'images' => json_encode([]),
                'featured_image' => null,
            ]);
            $this->command->info('Produto padrão de crédito na carteira criado!');
        } else {
            $this->command->info('Produto padrão de crédito na carteira já existe. Pulando...');
        }
        
        // Chama o seeder de métodos de frete
        $this->call([
            ShippingMethodSeeder::class,
        ]);
    }
}
