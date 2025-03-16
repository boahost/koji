<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

import { 
    CurrencyDollarIcon, 
    ChartBarIcon, 
    ArrowTrendingUpIcon,
    BanknotesIcon,
    EllipsisHorizontalIcon
} from '@heroicons/vue/24/outline';

const marketData = ref([
    { name: 'PETR4', price: 'R$ 35,84', change: '+2,34%', volume: '23,4M' },
    { name: 'VALE3', price: 'R$ 71,80', change: '-0,45%', volume: '15,2M' },
    { name: 'ITUB4', price: 'R$ 28,85', change: '+1,67%', volume: '19,8M' },
]);

const cards = ref([
    { 
        title: 'Total de Ativos', 
        value: '15', 
        trend: '+2,5%', 
        icon: ChartBarIcon,
        color: 'text-primary-600',
        bgColor: 'bg-primary-50'
    },
    { 
        title: 'Valor da Carteira', 
        value: 'R$ 25.430,00', 
        trend: '+3,2%', 
        icon: BanknotesIcon,
        color: 'text-green-600',
        bgColor: 'bg-green-50'
    },
    { 
        title: 'Rendimento Mensal', 
        value: 'R$ 1.250,00', 
        trend: '+5,8%', 
        icon: CurrencyDollarIcon,
        color: 'text-indigo-600',
        bgColor: 'bg-indigo-50'
    },
]);


</script>

<template>
    <Head title="Painel de Controle" />

    <DashboardLayout>
        <div class="space-y-8">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100 transition-all duration-300 ease-in-out hover:shadow-md">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-primary-500">
                        Painel de Controle
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Acompanhe seus investimentos em tempo real.</p>
                </div>
            </div>

            <!-- Cards Grid -->
            <TransitionGroup
                enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                move-class="transition-transform duration-300"
                appear
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="(card, index) in cards"
                        :key="card.title"
                        class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 ease-in-out group hover:scale-[1.02] hover:bg-gray-50/50"
                    >
                        <dt>
                            <div :class="[card.bgColor, 'absolute rounded-xl p-3 transition-transform duration-300 ease-in-out group-hover:scale-110']">
                                <component :is="card.icon" :class="[card.color, 'h-6 w-6']" aria-hidden="true" />
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">{{ card.title }}</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-6">
                            <p class="text-2xl font-semibold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-700">{{ card.value }}</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                <ArrowTrendingUpIcon class="h-4 w-4 transition-transform duration-300 ease-in-out group-hover:translate-y-[-2px]" aria-hidden="true" />
                                {{ card.trend }}
                            </p>
                        </dd>
                    </div>
                </div>
            </TransitionGroup>

            <!-- Tabela do Mercado -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 ease-in-out hover:shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900 bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-primary-500">Mercado em Tempo Real</h2>
                        <button class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                            <EllipsisHorizontalIcon class="h-6 w-6" />
                        </button>
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variação</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <TransitionGroup
                                enter-active-class="transition ease-out duration-300"
                                enter-from-class="transform -translate-x-full"
                                enter-to-class="translate-x-0"
                                move-class="transition-transform duration-300"
                                appear
                            >
                                <tr 
                                    v-for="(item, index) in marketData" 
                                    :key="item.name" 
                                    class="hover:bg-gray-50/50 transition-all duration-200 ease-in-out group"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900 transition-colors duration-200 ease-in-out group-hover:text-primary-600">{{ item.name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                item.change.startsWith('+') 
                                                    ? 'text-green-700 bg-green-50 ring-green-600/20 group-hover:bg-green-100'
                                                    : 'text-red-700 bg-red-50 ring-red-600/20 group-hover:bg-red-100',
                                                'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset transition-colors duration-200 ease-in-out'
                                            ]"
                                        >
                                            {{ item.change }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.volume }}</td>
                                </tr>
                            </TransitionGroup>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
