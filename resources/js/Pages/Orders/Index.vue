<template>
    <BottomNavLayout>
        <AppHeader @search-historico="$emit('search-historico', $event)" />
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
                    <div v-for="order in paginatedOrders" :key="order.id" class="p-4 sm:p-6">
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
                        

                        
                        <!-- Ações -->
                        <div class="mt-4 flex justify-end">
                            <Link :href="route('orders.show', order.id)"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-[#231F20] hover:text-[#231F20]/80 hover:bg-gray-50 text-sm font-medium rounded-full transition-colors"
                                title="Ver Detalhes"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver Detalhes
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Controles de Paginação -->
                <div v-if="orders.length > 0 && totalPages > 1" class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <!-- Informações da página -->
                        <div class="text-sm text-gray-600">
                            Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a {{ Math.min(currentPage * itemsPerPage, orders.length) }} de {{ orders.length }} pedidos
                        </div>
                        
                        <!-- Controles de navegação -->
                        <div class="flex items-center space-x-2">
                            <!-- Botão Anterior -->
                            <button 
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                :class="currentPage === 1 ? 'text-gray-400' : 'text-gray-600 hover:text-[#231F20]'"
                            >
                                <ChevronLeftIcon class="w-5 h-5" />
                            </button>
                            
                            <!-- Números das páginas -->
                            <div class="flex items-center space-x-1">
                                <button 
                                    v-for="page in totalPages" 
                                    :key="page"
                                    @click="goToPage(page)"
                                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                                    :class="page === currentPage 
                                        ? 'bg-[#231F20] text-white' 
                                        : 'text-gray-600 hover:text-[#231F20] hover:bg-gray-50'"
                                >
                                    {{ page }}
                                </button>
                            </div>
                            
                            <!-- Botão Próximo -->
                            <button 
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                :class="currentPage === totalPages ? 'text-gray-400' : 'text-gray-600 hover:text-[#231F20]'"
                            >
                                <ChevronRightIcon class="w-5 h-5" />
                            </button>
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
                    <Link :href="route('customer.dashboard')"
                        class="mt-6 inline-flex items-center justify-center px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                    >
                        Explorar Produtos
                    </Link>
                </div>
            </div>
        </div>
        </main>
    </BottomNavLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import BottomNavLayout from '@/Layouts/BottomNavLayout.vue'
import AppHeader from '@/Components/AppHeader.vue'
import { ShoppingBagIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    auth: Object,
    orders: Array
})

// Paginação
const currentPage = ref(1)
const itemsPerPage = 3

// Calcula os pedidos da página atual
const paginatedOrders = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return props.orders.slice(start, end)
})

// Calcula o número total de páginas
const totalPages = computed(() => {
    return Math.ceil(props.orders.length / itemsPerPage)
})

// Função para ir para a próxima página
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}

// Função para ir para a página anterior
const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

// Função para ir para uma página específica
const goToPage = (page) => {
    currentPage.value = page
}

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
