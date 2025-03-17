&lt;script setup&gt;
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
&lt;/script&gt;

&lt;template&gt;
    &lt;div class="min-h-screen bg-gray-50"&gt;
        &lt;Head title="Minha Conta" /&gt;

        &lt;!-- Header --&gt;
        &lt;header class="bg-white shadow"&gt;
            &lt;div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"&gt;
                &lt;div class="flex items-center justify-between"&gt;
                    &lt;h1 class="text-2xl font-bold text-gray-900"&gt;Minha Conta&lt;/h1&gt;
                    &lt;Link 
                        :href="route('products')"
                        class="text-sm font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200"
                    &gt;
                        Voltar às Compras
                    &lt;/Link&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/header&gt;

        &lt;main class="py-10"&gt;
            &lt;div class="max-w-7xl mx-auto sm:px-6 lg:px-8"&gt;
                &lt;!-- User Info Card --&gt;
                &lt;div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200"&gt;
                    &lt;div class="px-4 py-5 sm:px-6"&gt;
                        &lt;div class="flex items-center"&gt;
                            &lt;div class="flex-shrink-0"&gt;
                                &lt;div class="h-12 w-12 rounded-full bg-gradient-to-r from-black to-gray-900 flex items-center justify-center"&gt;
                                    &lt;UserIcon class="h-6 w-6 text-white" /&gt;
                                &lt;/div&gt;
                            &lt;/div&gt;
                            &lt;div class="ml-4"&gt;
                                &lt;h3 class="text-lg font-medium leading-6 text-gray-900"&gt;Olá, usuário&lt;/h3&gt;
                                &lt;p class="text-sm text-gray-500"&gt;Bem-vindo à sua área do cliente&lt;/p&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/div&gt;

                &lt;!-- Tabs --&gt;
                &lt;div class="mt-6"&gt;
                    &lt;div class="sm:hidden"&gt;
                        &lt;label for="tabs" class="sr-only"&gt;Selecione uma aba&lt;/label&gt;
                        &lt;select
                            id="tabs"
                            name="tabs"
                            v-model="currentTab"
                            class="block w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        &gt;
                            &lt;option v-for="tab in tabs" :key="tab.name"&gt;{{ tab.name }}&lt;/option&gt;
                        &lt;/select&gt;
                    &lt;/div&gt;
                    &lt;div class="hidden sm:block"&gt;
                        &lt;nav class="flex space-x-4" aria-label="Tabs"&gt;
                            &lt;button
                                v-for="tab in tabs"
                                :key="tab.name"
                                @click="currentTab = tab.name"
                                :class="[
                                    currentTab === tab.name
                                        ? 'bg-gray-900 text-white'
                                        : 'text-gray-500 hover:text-gray-700',
                                    'px-3 py-2 font-medium text-sm rounded-md flex items-center space-x-2'
                                ]"
                            &gt;
                                &lt;component :is="tab.icon" class="h-5 w-5" /&gt;
                                &lt;span&gt;{{ tab.name }}&lt;/span&gt;
                                &lt;span
                                    v-if="tab.count !== undefined"
                                    :class="[
                                        currentTab === tab.name ? 'bg-gray-800' : 'bg-gray-100',
                                        'rounded-full py-0.5 px-2.5 text-xs font-medium'
                                    ]"
                                &gt;
                                    {{ tab.count }}
                                &lt;/span&gt;
                            &lt;/button&gt;
                        &lt;/nav&gt;
                    &lt;/div&gt;
                &lt;/div&gt;

                &lt;!-- Tab Panels --&gt;
                &lt;div class="mt-6"&gt;
                    &lt;!-- Pedidos Panel --&gt;
                    &lt;div v-if="currentTab === 'Pedidos'" class="bg-white shadow rounded-lg divide-y divide-gray-200"&gt;
                        &lt;div v-for="order in orders" :key="order.id" class="p-4 sm:p-6"&gt;
                            &lt;div class="flex items-center justify-between"&gt;
                                &lt;div class="flex items-center space-x-4"&gt;
                                    &lt;div class="flex-shrink-0"&gt;
                                        &lt;component :is="order.icon" class="h-6 w-6" :class="order.statusColor" /&gt;
                                    &lt;/div&gt;
                                    &lt;div&gt;
                                        &lt;p class="text-sm font-medium text-gray-900"&gt;Pedido {{ order.id }}&lt;/p&gt;
                                        &lt;p class="text-sm text-gray-500"&gt;{{ order.date }}&lt;/p&gt;
                                    &lt;/div&gt;
                                &lt;/div&gt;
                                &lt;div class="text-right"&gt;
                                    &lt;p class="text-sm font-medium text-gray-900"&gt;{{ order.total }}&lt;/p&gt;
                                    &lt;p class="text-sm" :class="order.statusColor"&gt;{{ order.status }}&lt;/p&gt;
                                &lt;/div&gt;
                            &lt;/div&gt;
                            &lt;div class="mt-4"&gt;
                                &lt;button
                                    class="text-sm font-medium bg-gradient-to-r from-black to-gray-900 text-white px-4 py-2 rounded-lg hover:from-gray-800 hover:to-gray-700 transition-all duration-200"
                                &gt;
                                    Ver Detalhes
                                &lt;/button&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;

                    &lt;!-- Favoritos Panel --&gt;
                    &lt;div v-if="currentTab === 'Favoritos'" class="bg-white shadow rounded-lg p-6"&gt;
                        &lt;div class="text-center py-12"&gt;
                            &lt;HeartIcon class="mx-auto h-12 w-12 text-gray-400" /&gt;
                            &lt;h3 class="mt-2 text-sm font-medium text-gray-900"&gt;Nenhum favorito&lt;/h3&gt;
                            &lt;p class="mt-1 text-sm text-gray-500"&gt;Comece a favoritar produtos para vê-los aqui.&lt;/p&gt;
                            &lt;div class="mt-6"&gt;
                                &lt;Link
                                    :href="route('products')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-black to-gray-900 rounded-lg hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200"
                                &gt;
                                    Ver Produtos
                                &lt;/Link&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;

                    &lt;!-- Pagamentos Panel --&gt;
                    &lt;div v-if="currentTab === 'Pagamentos'" class="bg-white shadow rounded-lg p-6"&gt;
                        &lt;div class="text-center py-12"&gt;
                            &lt;CreditCardIcon class="mx-auto h-12 w-12 text-gray-400" /&gt;
                            &lt;h3 class="mt-2 text-sm font-medium text-gray-900"&gt;Nenhum cartão cadastrado&lt;/h3&gt;
                            &lt;p class="mt-1 text-sm text-gray-500"&gt;Adicione um cartão para agilizar suas compras.&lt;/p&gt;
                            &lt;div class="mt-6"&gt;
                                &lt;button
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-black to-gray-900 rounded-lg hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200"
                                &gt;
                                    Adicionar Cartão
                                &lt;/button&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/main&gt;
    &lt;/div&gt;
&lt;/template&gt;
