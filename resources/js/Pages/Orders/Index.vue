<template>
    <ProductShowcaseLayout :auth="auth">
        <div class="animate-fade-in-up">
            <!-- Cabeçalho -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold text-[#231F20]">
                    Meus Pedidos
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Acompanhe o status dos seus pedidos
                </p>
            </div>

            <div class="mt-6 space-y-6">
                <!-- Lista de Pedidos -->
                <div v-if="orders.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-100 divide-y divide-gray-100">
                    <div v-for="order in orders" :key="order.id" class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-medium text-[#231F20]">
                                        Pedido #{{ order.id }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="getStatusClass(order.status)"
                                    >
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ formatDate(order.created_at) }}
                                </p>
                            </div>
                            
                            <div class="mt-4 sm:mt-0 flex flex-col sm:items-end">
                                <p class="text-lg font-semibold text-[#231F20]">
                                    {{ formatCurrency(order.total) }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ order.items.length }} {{ order.items.length === 1 ? 'item' : 'itens' }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Resumo dos Itens -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <div v-for="item in order.items.slice(0, 3)" :key="item.id" 
                                class="w-12 h-12 bg-gray-100 rounded-md overflow-hidden border border-gray-200"
                            >
                                <img :src="`/storage/${item.product.featured_image}`" :alt="item.product.name"
                                    class="w-full h-full object-cover">
                            </div>
                            <div v-if="order.items.length > 3" 
                                class="w-12 h-12 bg-gray-100 rounded-md overflow-hidden border border-gray-200 flex items-center justify-center text-sm font-medium text-gray-600"
                            >
                                +{{ order.items.length - 3 }}
                            </div>
                        </div>
                        
                        <!-- Ações -->
                        <div class="mt-4 flex justify-end">
                            <Link :href="route('orders.show', order.id)"
                                class="inline-flex items-center px-3 py-1.5 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                            >
                                Ver Detalhes
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Sem Pedidos -->
                <div v-else class="bg-white rounded-lg p-8 shadow-sm border border-gray-100 text-center">
                    <ShoppingBagIcon class="w-16 h-16 mx-auto text-gray-400" />
                    <h2 class="mt-4 text-lg font-medium text-[#231F20]">Você ainda não tem pedidos</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Comece a comprar para ver seus pedidos aqui.
                    </p>
                    <Link :href="route('products')"
                        class="mt-6 inline-flex items-center justify-center px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                    >
                        Explorar Produtos
                    </Link>
                </div>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'
import { ShoppingBagIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    auth: Object,
    orders: Array
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
        year: 'numeric'
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
