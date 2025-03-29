<template>
    <DashboardLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Detalhes do Pedido #{{ order.id }}</h1>
                <Link
                    :href="route('sales.index')"
                    class="text-blue-600 hover:text-blue-900"
                >
                    Voltar para vendas
                </Link>
            </div>

            <!-- Order Status -->
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Status do Pedido</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ getStatusLabel(order.status) }}
                        </p>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full text-sm font-semibold"
                        :class="{
                            'bg-yellow-100 text-yellow-800': order.status === 'pending',
                            'bg-blue-100 text-blue-800': order.status === 'processing',
                            'bg-green-100 text-green-800': order.status === 'completed',
                            'bg-red-100 text-red-800': order.status === 'cancelled'
                        }"
                    >
                        {{ getStatusLabel(order.status) }}
                    </span>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações do Cliente</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nome</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.customer.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.customer.email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Telefone</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.customer.phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">CPF</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.customer.document }}</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações de Entrega</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Endereço</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.address }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cidade</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.city }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Estado</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.state }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">CEP</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.zip_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Método de Entrega</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.shipping_method.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Valor do Frete</p>
                        <p class="mt-1 text-sm text-gray-900">R$ {{ Number(order.shipping_cost || 0).toFixed(2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Itens do Pedido</h2>
                <div class="space-y-4">
                    <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between py-4 border-b border-gray-200 last:border-0">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img
                                    :src="item.product.featured_image ? `/storage/${item.product.featured_image}` : '/images/no-image.png'"
                                    :alt="item.product.name"
                                    class="h-16 w-16 object-cover rounded-lg"
                                />
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ item.product.name }}</h3>
                                <p class="text-sm text-gray-500">{{ item.product.department.name }}</p>
                                <p class="text-sm text-gray-500">Quantidade: {{ item.quantity }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">R$ {{ Number(item.price || 0).toFixed(2) }}</p>
                            <p class="text-sm text-gray-500">Total: R$ {{ calculateItemTotal(item) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações de Pagamento</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Método de Pagamento</p>
                        <p class="mt-1 text-sm text-gray-900">{{ getPaymentMethodLabel(order.payment.payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status do Pagamento</p>
                        <p class="mt-1 text-sm text-gray-900">{{ getPaymentStatusLabel(order.payment.status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">ID da Transação</p>
                        <p class="mt-1 text-sm text-gray-900">{{ order.payment.transaction_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Data do Pagamento</p>
                        <p class="mt-1 text-sm text-gray-900">{{ new Date(order.payment.created_at).toLocaleDateString('pt-BR') }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white shadow-sm rounded-lg p-4">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Resumo do Pedido</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Subtotal</span>
                        <span class="text-sm text-gray-900">R$ {{ Number(order.subtotal || 0).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Frete</span>
                        <span class="text-sm text-gray-900">R$ {{ Number(order.shipping_cost || 0).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <span class="text-base font-medium text-gray-900">Total</span>
                        <span class="text-base font-medium text-gray-900">R$ {{ Number(order.total || 0).toFixed(2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
    order: Object
});

const calculateItemTotal = (item) => {
    const price = Number(item.price || 0);
    const quantity = Number(item.quantity || 0);
    return (price * quantity).toFixed(2);
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

const getPaymentMethodLabel = (method) => {
    const labels = {
        credit_card: 'Cartão de Crédito',
        pix: 'PIX'
    };
    return labels[method] || method;
};

const getPaymentStatusLabel = (status) => {
    const labels = {
        pending: 'Pendente',
        approved: 'Aprovado',
        refused: 'Recusado',
        cancelled: 'Cancelado'
    };
    return labels[status] || status;
};
</script> 