<template>
    <div class="min-h-screen bg-gray-50">
        <Head title="Minha Conta" />

        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">Minha Conta</h1>
                    <Link 
                        :href="route('products')"
                        class="text-sm font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200"
                    >
                        Voltar às Compras
                    </Link>
                </div>
            </div>
        </header>

        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- User Info Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-black to-gray-900 flex items-center justify-center">
                                    <UserIcon class="h-6 w-6 text-white" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Olá, usuário</h3>
                                <p class="text-sm text-gray-500">Bem-vindo à sua área do cliente</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mt-6">
                    <div class="sm:hidden">
                        <label for="tabs" class="sr-only">Selecione uma aba</label>
                        <select
                            id="tabs"
                            name="tabs"
                            v-model="currentTab"
                            class="block w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        >
                            <option v-for="tab in tabs" :key="tab.name">{{ tab.name }}</option>
                        </select>
                    </div>
                    <div class="hidden sm:block">
                        <nav class="flex space-x-4" aria-label="Tabs">
                            <button
                                v-for="tab in tabs"
                                :key="tab.name"
                                @click="currentTab = tab.name"
                                :class="[
                                    currentTab === tab.name
                                        ? 'bg-gray-900 text-white'
                                        : 'text-gray-500 hover:text-gray-700',
                                    'px-3 py-2 font-medium text-sm rounded-md flex items-center space-x-2'
                                ]"
                            >
                                <component :is="tab.icon" class="h-5 w-5" />
                                <span>{{ tab.name }}</span>
                                <span
                                    v-if="tab.count !== undefined"
                                    :class="[
                                        currentTab === tab.name ? 'bg-gray-800' : 'bg-gray-100',
                                        'rounded-full py-0.5 px-2.5 text-xs font-medium'
                                    ]"
                                >
                                    {{ tab.count }}
                                </span>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Panels -->
                <div class="mt-6">
                    <!-- Pedidos Panel -->
                    <div v-if="currentTab === 'Pedidos'" class="bg-white shadow rounded-lg divide-y divide-gray-200">
                        <div v-for="order in orders" :key="order.id" class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <component :is="order.icon" class="h-6 w-6" :class="order.statusColor" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Pedido {{ order.id }}</p>
                                        <p class="text-sm text-gray-500">{{ order.date }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ order.total }}</p>
                                    <p class="text-sm" :class="order.statusColor">{{ order.status }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button
                                    class="text-sm font-medium bg-gradient-to-r from-black to-gray-900 text-white px-4 py-2 rounded-lg hover:from-gray-800 hover:to-gray-700 transition-all duration-200"
                                >
                                    Ver Detalhes
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Favoritos Panel -->
                    <div v-if="currentTab === 'Favoritos'" class="bg-white shadow rounded-lg p-6">
                        <div class="text-center py-12">
                            <HeartIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum favorito</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece a favoritar produtos para vê-los aqui.</p>
                            <div class="mt-6">
                                <Link
                                    :href="route('products')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-black to-gray-900 rounded-lg hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200"
                                >
                                    Ver Produtos
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Pagamentos Panel -->
                    <div v-if="currentTab === 'Pagamentos'" class="bg-white shadow rounded-lg p-6">
                        <div class="text-center py-12">
                            <CreditCardIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum cartão cadastrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Adicione um cartão para agilizar suas compras.</p>
                            <div class="mt-6">
                                <button
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-black to-gray-900 rounded-lg hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200"
                                >
                                    Adicionar Cartão
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    UserIcon, 
    ShoppingBagIcon, 
    HeartIcon, 
    CreditCardIcon,
    ClockIcon,
    TruckIcon,
} from '@heroicons/vue/24/outline';

const tabs = [
    { name: 'Pedidos', icon: ShoppingBagIcon, count: 0 },
    { name: 'Favoritos', icon: HeartIcon, count: 0 },
    { name: 'Pagamentos', icon: CreditCardIcon },
];

const orders = [
    {
        id: '#12345',
        date: '15/03/2025',
        status: 'Em processamento',
        total: 'R$ 299,90',
        icon: ClockIcon,
        statusColor: 'text-yellow-600',
    },
    {
        id: '#12344',
        date: '14/03/2025',
        status: 'Enviado',
        total: 'R$ 159,90',
        icon: TruckIcon,
        statusColor: 'text-blue-600',
    },
];

const currentTab = ref('Pedidos');
</script>
