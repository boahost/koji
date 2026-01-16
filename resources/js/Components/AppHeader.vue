<template>
    <header style="height:85px;" class="bg-white shadow-lg sticky top-0 z-30 mb-0 md:mb-4 border-b-4 border-[#C0A062]">
        <!-- Menu Desktop -->
        <nav class="hidden md:block bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative flex items-center justify-center py-2">
                    <!-- Logo (Esquerda) -->
                    <div class="absolute left-0 z-50" style="margin-top:13px;">
                        <Link :href="route('customer.dashboard')" class="inline-block transform transition-transform duration-300 hover:scale-105">
                            <img :src="logo" class="h-24 w-auto object-contain drop-shadow-md" alt="Consulta de Imóveis">
                        </Link>
                    </div>

                    <!-- Menu (Centro) -->
                    <div class="flex items-center space-x-6" style="margin-top:10px;">
                        <!-- Início -->
                        <Link
                            :href="route('customer.dashboard')"
                            class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 group"
                            :class="{
                                'bg-[#231F20] text-[#C0A062] shadow-md border border-[#C0A062]/20': route().current('customer.dashboard'),
                                'text-gray-600 hover:text-[#C0A062] hover:bg-gray-50': !route().current('customer.dashboard')
                            }"
                        >
                            <HomeIcon class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Início</span>
                        </Link>

                        <!-- Pedidos -->
                        <Link
                            :href="route('orders.index')"
                            class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 group relative"
                            :class="{
                                'bg-[#231F20] text-[#C0A062] shadow-md border border-[#C0A062]/20': route().current('orders.*'),
                                'text-gray-600 hover:text-[#C0A062] hover:bg-gray-50': !route().current('orders.*')
                            }"
                        >
                            <ShoppingBagIcon class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Pedidos</span>
                            <span v-if="ordersCount > 0" 
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center animate-pulse"
                            >
                                {{ ordersCount }}
                            </span>
                        </Link>

                        <!-- Carrinho -->
                        <Link
                            :href="route('cart.index')"
                            class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 group relative"
                            :class="{
                                'bg-[#231F20] text-[#C0A062] shadow-md border border-[#C0A062]/20': route().current('cart.*'),
                                'text-gray-600 hover:text-[#C0A062] hover:bg-gray-50': !route().current('cart.*')
                            }"
                        >
                            <ShoppingCartIcon class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Carrinho</span>
                            <span v-if="cartItemCount > 0" 
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center animate-pulse"
                            >
                                {{ cartItemCount }}
                            </span>
                        </Link>

                        <!-- Perfil -->
                        <Link
                            :href="route('customer.profile')"
                            class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 group"
                            :class="{
                                'bg-[#231F20] text-[#C0A062] shadow-md border border-[#C0A062]/20': route().current('customer.profile'),
                                'text-gray-600 hover:text-[#C0A062] hover:bg-gray-50': !route().current('customer.profile')
                            }"
                        >
                            <UserIcon class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Perfil</span>
                        </Link>
                    </div>

                    <!-- Busca e Filtro (Direita) -->
                    <div class="absolute right-0 flex items-center gap-2">
                        <!-- Search -->
                        <div class="relative w-48 lg:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" aria-hidden="true" />
                            </div>
                            <input
                                id="search"
                                name="search"
                                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-full text-sm bg-gray-50 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#C0A062] focus:bg-white focus:border-transparent transition-all duration-200"
                                placeholder="Buscar..."
                                type="search"
                                v-model="search"
                                @input="$emit('search-historico', search)"
                            >
                        </div>

                        <!-- Botão de Filtro -->
                        <button 
                            @click="$emit('toggle-filters')"
                            class="p-2 rounded-full bg-gray-100 hover:bg-[#C0A062] hover:text-[#231F20] relative flex items-center justify-center border border-gray-200 transition-all duration-200 group"
                            aria-label="Abrir filtros"
                        >
                            <FunnelIcon class="h-5 w-5 text-gray-500 group-hover:text-[#231F20] transition-colors" />
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import logo from '../../css/logo.png'
import { 
    MagnifyingGlassIcon, 
    FunnelIcon,
    HomeIcon,
    ShoppingBagIcon,
    ShoppingCartIcon,
    UserIcon,
} from '@heroicons/vue/24/outline'
import debounce from 'lodash/debounce'
import axios from 'axios'

const emit = defineEmits(['toggle-filters'])

const search = ref('')

// Contadores
const cartItemCount = ref(0)
const ordersCount = ref(0)

// Atualiza o contador do carrinho
const updateCartCount = async () => {
    try {
        const response = await axios.get(route('cart.count'))
        cartItemCount.value = response.data.count
    } catch (error) {
        console.error('Erro ao buscar contagem do carrinho:', error)
    }
}

// Atualiza o contador de pedidos
const updateOrdersCount = async () => {
    try {
        const response = await axios.get(route('customer.orders.count'))
        ordersCount.value = response.data.count
    } catch (error) {
        console.error('Erro ao buscar contagem de pedidos:', error)
    }
}

// Atualiza os contadores ao montar o componente
onMounted(() => {
    updateCartCount()
    updateOrdersCount()

    // Escuta eventos de atualização do carrinho
    window.addEventListener('cart-updated', (event) => {
        if (event.detail && typeof event.detail.count === 'number') {
            cartItemCount.value = event.detail.count
        } else {
            updateCartCount()
        }
    })
})
</script>
