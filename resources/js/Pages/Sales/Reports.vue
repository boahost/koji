<template>
    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Relatórios de Vendas</h1>
            </div>

            <!-- Filtros -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form @submit.prevent="filter" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                        <input
                            type="date"
                            id="start_date"
                            v-model="localFilters.start_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                        <input
                            type="date"
                            id="end_date"
                            v-model="localFilters.end_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                    </div>

                    <div class="flex items-end">
                        <button
                            type="submit"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Resumo Geral -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <CurrencyDollarIcon class="h-8 w-8 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total de Vendas</p>
                            <p class="text-2xl font-semibold text-gray-900">R$ {{ formatCurrency(summary.total_sales) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <ShoppingBagIcon class="h-8 w-8 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total de Pedidos</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ summary.total_orders }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <CheckCircleIcon class="h-8 w-8 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pedidos Concluídos</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ summary.completed_orders }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <ChartBarSquareIcon class="h-8 w-8 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Ticket Médio</p>
                            <p class="text-2xl font-semibold text-gray-900">R$ {{ formatCurrency(summary.average_order_value) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Vendas por Dia -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas por Dia</h3>
                <div v-if="dailySales.length > 0" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div 
                            v-for="(day, index) in dailySales" 
                            :key="index"
                            class="bg-gray-50 rounded-lg p-4"
                        >
                            <div class="text-sm text-gray-500 mb-1">{{ day.date }}</div>
                            <div class="text-lg font-semibold text-gray-900">R$ {{ formatCurrency(day.total) }}</div>
                            <div class="text-xs text-gray-400">{{ day.orders }} pedidos</div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                    Nenhuma venda registrada no período selecionado
                </div>
            </div>

            <!-- Vendas por Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas por Status</h3>
                <div v-if="salesByStatus.length > 0" class="space-y-3">
                    <div 
                        v-for="(status, index) in salesByStatus" 
                        :key="index"
                        class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0"
                    >
                        <div class="flex items-center">
                            <span 
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="getStatusClass(status.status)"
                            >
                                {{ getStatusLabel(status.status) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">R$ {{ formatCurrency(status.total) }}</div>
                            <div class="text-xs text-gray-500">{{ status.orders }} pedidos</div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                    Nenhuma venda registrada no período selecionado
                </div>
            </div>

            <!-- Top Produtos Vendidos -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Produtos Vendidos</h3>
                <div v-if="topProducts.length > 0" class="space-y-3">
                    <div 
                        v-for="(product, index) in topProducts" 
                        :key="index"
                        class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0"
                    >
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                            <p class="text-xs text-gray-500">{{ product.total_quantity }} unidades</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">R$ {{ formatCurrency(product.total_revenue) }}</div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                    Nenhum produto vendido no período selecionado
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
    CurrencyDollarIcon,
    ShoppingBagIcon,
    CheckCircleIcon,
    ChartBarSquareIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    dailySales: Array,
    salesByStatus: Array,
    topProducts: Array,
    summary: Object,
    filters: Object
});

const localFilters = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || ''
});

const formatCurrency = (value) => {
    return Number(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const filter = () => {
    const params = {};
    
    if (localFilters.value.start_date) {
        params.start_date = localFilters.value.start_date;
    }
    
    if (localFilters.value.end_date) {
        params.end_date = localFilters.value.end_date;
    }

    window.location.href = route('sales.reports', params);
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pendente',
        processing: 'Processando',
        completed: 'Concluído',
        cancelled: 'Cancelado'
    };
    return labels[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script> 