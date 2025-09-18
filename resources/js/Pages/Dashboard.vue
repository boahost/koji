<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

import { 
    UserGroupIcon,
    ShoppingBagIcon,
    UserIcon,
    CurrencyDollarIcon,
    ArrowTrendingUpIcon,
    CalendarIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    statistics: Object
});

const formatCurrency = (value) => {
    return Number(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const cards = ref([
    { 
        title: 'Total de Clientes', 
        value: props.statistics.customers, 
        icon: UserGroupIcon,
        color: 'text-blue-600',
        bgColor: 'bg-blue-50'
    },
    { 
        title: 'Vendas Hoje', 
        value: `R$ ${formatCurrency(props.statistics.sales?.today || 0)}`, 
        icon: CurrencyDollarIcon,
        color: 'text-green-600',
        bgColor: 'bg-green-50'
    },
    { 
        title: 'Vendas do Mês', 
        value: `R$ ${formatCurrency(props.statistics.sales?.thisMonth || 0)}`, 
        icon: ArrowTrendingUpIcon,
        color: 'text-purple-600',
        bgColor: 'bg-purple-50'
    },
    { 
        title: 'Total de Vendas', 
        value: `R$ ${formatCurrency(props.statistics.sales?.total || 0)}`, 
        icon: ShoppingBagIcon,
        color: 'text-indigo-600',
        bgColor: 'bg-indigo-50'
    },
]);

const getPercentageChange = (current, previous) => {
    if (previous === 0) return current > 0 ? 100 : 0;
    return ((current - previous) / previous) * 100;
};

const salesData = ref(props.statistics.sales?.last7Days || []);
const chartData = ref(props.statistics.sales?.last30Days || []);

onMounted(() => {
    // Aqui você pode adicionar lógica para inicializar gráficos se necessário
    console.log('Dashboard mounted with sales data:', props.statistics.sales);
});
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
                    <p class="mt-2 text-sm text-gray-500">Acompanhe as estatísticas do seu negócio.</p>
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                        </dd>
                    </div>
                </div>
            </TransitionGroup>

            <!-- Gráfico de Vendas dos Últimos 7 Dias -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas dos Últimos 7 Dias</h3>
                <div class="space-y-4">
                    <div v-if="salesData.length > 0" class="grid grid-cols-7 gap-2">
                        <div 
                            v-for="(day, index) in salesData" 
                            :key="index"
                            class="text-center"
                        >
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500 mb-1">{{ day.date }}</div>
                                <div class="text-lg font-semibold text-gray-900">R$ {{ formatCurrency(day.total) }}</div>
                                <div class="text-xs text-gray-400">{{ day.orders }} pedidos</div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        Nenhuma venda registrada nos últimos 7 dias
                    </div>
                </div>
            </div>

            <!-- Comparação Mensal -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Vendas do Mês Atual vs Anterior -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Comparação Mensal</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500">Mês Atual</p>
                                <p class="text-2xl font-bold text-green-600">R$ {{ formatCurrency(statistics.sales?.thisMonth || 0) }}</p>
                            </div>
                            <CalendarIcon class="h-8 w-8 text-green-600" />
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500">Mês Anterior</p>
                                <p class="text-2xl font-bold text-gray-600">R$ {{ formatCurrency(statistics.sales?.lastMonth || 0) }}</p>
                            </div>
                            <CalendarIcon class="h-8 w-8 text-gray-400" />
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500">Variação:</span>
                                <span 
                                    class="ml-2 text-sm font-semibold"
                                    :class="getPercentageChange(statistics.sales?.thisMonth || 0, statistics.sales?.lastMonth || 0) >= 0 ? 'text-green-600' : 'text-red-600'"
                                >
                                    {{ getPercentageChange(statistics.sales?.thisMonth || 0, statistics.sales?.lastMonth || 0).toFixed(1) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Produtos Vendidos -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Produtos Vendidos</h3>
                    <div class="space-y-3">
                        <div v-if="statistics.sales?.topProducts && statistics.sales.topProducts.length > 0">
                            <div 
                                v-for="(product, index) in statistics.sales.topProducts" 
                                :key="index"
                                class="flex justify-between items-center py-2"
                            >
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                                    <p class="text-xs text-gray-500">{{ product.total_quantity }} unidades</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">R$ {{ formatCurrency(product.total_revenue) }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            Nenhum produto vendido ainda
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Vendas dos Últimos 30 Dias -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas dos Últimos 30 Dias</h3>
                <div class="space-y-4">
                    <div v-if="chartData.length > 0" class="grid grid-cols-10 gap-1">
                        <div 
                            v-for="(day, index) in chartData" 
                            :key="index"
                            class="text-center"
                        >
                            <div class="bg-blue-50 rounded p-2">
                                <div class="text-xs text-gray-500 mb-1">{{ day.date }}</div>
                                <div class="text-sm font-semibold text-blue-600">R$ {{ formatCurrency(day.total) }}</div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        Nenhuma venda registrada nos últimos 30 dias
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
