<template>
    <ProductShowcaseLayout :auth="auth">
        <div class="animate-fade-in-up">
            <!-- Cabeçalho -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-[#231F20]">
                            Pedido #{{ order.id }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Realizado em {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                    
                    <div class="mt-4 sm:mt-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                            :class="getStatusClass(order.status)"
                        >
                            {{ getStatusText(order.status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-6">
                <!-- Itens do Pedido -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-medium text-[#231F20]">Itens do Pedido</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        <div v-for="item in order.items" :key="item.id" class="p-4 sm:p-6">
                            <div class="flex gap-4">
                                <!-- Imagem do Produto -->
                                <div class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                    <img :src="`/storage/${item.product.featured_image}`" :alt="item.product.name"
                                        class="w-full h-full object-cover">
                                </div>

                                <!-- Informações do Produto -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-sm sm:text-base font-medium text-[#231F20] line-clamp-2">
                                                {{ item.product.name }}
                                            </h3>
                                            <p class="mt-1 text-xs text-[#231F20]/70">
                                                {{ item.product.department.name }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Preço e Quantidade -->
                                    <div class="mt-4 flex items-center justify-between gap-4">
                                        <div class="text-sm text-gray-500">
                                            {{ item.quantity }} × {{ formatCurrency(item.price) }}
                                        </div>
                                        <p class="text-base font-semibold text-[#231F20]">
                                            {{ formatCurrency(item.total) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações do Pagamento e Entrega -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pagamento -->
                    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                        <h2 class="text-lg font-medium text-[#231F20]">Pagamento</h2>
                        
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Método</span>
                                <span class="font-medium text-[#231F20]">
                                    {{ order.payment ? getPaymentMethodText(order.payment.payment_method) : 'Não definido' }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Status</span>
                                <span class="font-medium" :class="order.payment ? getPaymentStatusClass(order.payment.status) : ''">
                                    {{ order.payment ? getPaymentStatusText(order.payment.status) : 'Não processado' }}
                                </span>
                            </div>
                            <div v-if="order.payment && order.payment.payment_method === 'pix'" class="mt-2">
                                <Link :href="route('orders.pix.payment', order.id)" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                    <span>Ver QR Code PIX</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Entrega -->
                    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                        <h2 class="text-lg font-medium text-[#231F20]">Entrega</h2>
                        
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Método</span>
                                <span class="font-medium text-[#231F20]">
                                    {{ order.shipping_method.name }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Endereço</span>
                                <span class="font-medium text-[#231F20]">
                                    {{ order.address }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Cidade/Estado</span>
                                <span class="font-medium text-[#231F20]">
                                    {{ order.city }}/{{ order.state }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">CEP</span>
                                <span class="font-medium text-[#231F20]">
                                    {{ order.zip_code }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumo do Pedido -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-medium text-[#231F20]">Resumo do Pedido</h2>
                    <div class="mt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-[#231F20]">{{ formatCurrency(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Frete</span>
                            <span class="font-medium text-[#231F20]">{{ formatCurrency(order.shipping_cost) }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex justify-between items-baseline">
                                <span class="text-base font-medium text-[#231F20]">Total</span>
                                <span class="text-2xl font-bold text-[#231F20]">
                                    {{ formatCurrency(order.total) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <Link :href="route('orders.index')"
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                        >
                            Voltar para Meus Pedidos
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'

const props = defineProps({
    auth: Object,
    order: Object
})

// Formata o preço em reais
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}

// Formata a data
const formatDate = (dateString) => {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date)
}

// Retorna o texto do status do pedido
const getStatusText = (status) => {
    const statusMap = {
        'pending': 'Pendente',
        'processing': 'Processando',
        'completed': 'Concluído',
        'cancelled': 'Cancelado'
    }
    
    return statusMap[status] || status
}

// Retorna a classe CSS para o status do pedido
const getStatusClass = (status) => {
    const classMap = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    }
    
    return classMap[status] || 'bg-gray-100 text-gray-800'
}

// Retorna o texto do método de pagamento
const getPaymentMethodText = (method) => {
    const methodMap = {
        'credit_card': 'Cartão de Crédito',
        'pix': 'PIX'
    }
    
    return methodMap[method] || method
}

// Retorna o texto do status do pagamento
const getPaymentStatusText = (status) => {
    const statusMap = {
        'pending': 'Pendente',
        'approved': 'Aprovado',
        'refused': 'Recusado',
        'refunded': 'Reembolsado',
        'cancelled': 'Cancelado'
    }
    
    return statusMap[status] || status
}

// Retorna a classe CSS para o status do pagamento
const getPaymentStatusClass = (status) => {
    const classMap = {
        'pending': 'text-yellow-600',
        'approved': 'text-green-600',
        'refused': 'text-red-600',
        'refunded': 'text-blue-600',
        'cancelled': 'text-gray-600'
    }
    
    return classMap[status] || 'text-gray-600'
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
