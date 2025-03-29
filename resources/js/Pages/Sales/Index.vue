<template>
    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Vendas</h1>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-4 space-y-4">
                <form @submit.prevent="filter" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            v-model="localFilters.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option value="">Todos</option>
                            <option value="pending">Pendente</option>
                            <option value="processing">Processando</option>
                            <option value="completed">Concluído</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>

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

            <!-- Orders Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pedido
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ order.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ order.customer.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(order.created_at).toLocaleDateString('pt-BR') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                R$ {{ calculateOrderTotal(order) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                        'bg-blue-100 text-blue-800': order.status === 'processing',
                                        'bg-green-100 text-green-800': order.status === 'completed',
                                        'bg-red-100 text-red-800': order.status === 'cancelled'
                                    }"
                                >
                                    {{ getStatusLabel(order.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <Link
                                    :href="route('sales.show', order.id)"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Ver detalhes
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="orders.prev_page_url"
                            :href="orders.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Anterior
                        </Link>
                        <Link
                            v-if="orders.next_page_url"
                            :href="orders.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Próxima
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ orders.from }}</span>
                                até
                                <span class="font-medium">{{ orders.to }}</span>
                                de
                                <span class="font-medium">{{ orders.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link
                                    v-for="(link, index) in orders.links"
                                    :key="index"
                                    :href="link.url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    :class="{
                                        'z-10 bg-blue-50 border-blue-500 text-blue-600': link.active,
                                        'cursor-not-allowed bg-gray-50 text-gray-400': !link.url
                                    }"
                                    v-html="link.label"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
    orders: Object,
    filters: Object
});

const localFilters = ref({
    status: props.filters.status || '',
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || ''
});

const calculateOrderTotal = (order) => {
    return Number(order.total || 0).toFixed(2);
};

const filter = () => {
    const params = {};
    
    if (localFilters.value.status) {
        params.status = localFilters.value.status;
    }
    
    if (localFilters.value.start_date) {
        params.start_date = localFilters.value.start_date;
    }
    
    if (localFilters.value.end_date) {
        params.end_date = localFilters.value.end_date;
    }

    window.location.href = route('sales.index', params);
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
</script> 