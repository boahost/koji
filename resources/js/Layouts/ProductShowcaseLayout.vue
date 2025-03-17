<template>
    <div class="min-h-screen bg-gray-50 pb-16">
        <!-- Header -->
        <header class="bg-[#231F20] shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <!-- Logo Centralizada -->
                <div class="flex justify-center mb-3">
                    <Link :href="route('products')" class="inline-block">
                        <img src="/logo.png" class="h-10 sm:h-12 w-auto object-contain" alt="Logo">
                    </Link>
                </div>

                <!-- Container de Busca e Filtro -->
                <div class="flex items-center justify-between gap-3 max-w-2xl mx-auto">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </div>
                            <input
                                id="search"
                                name="search"
                                class="block w-full pl-10 pr-3 py-2.5 border-0 rounded-full text-sm bg-white/10 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-white/25 focus:bg-white/20 transition-all duration-200"
                                placeholder="Buscar produtos..."
                                type="search"
                                v-model="search"
                                @input="debouncedSearch"
                            >
                        </div>
                    </div>

                    <!-- Botão de Filtro -->
                    <button 
                        @click="$emit('toggle-filters')"
                        class="p-2.5 rounded-full bg-white/10 hover:bg-white/20 relative flex items-center justify-center min-w-[44px] h-[44px] border-0 transition-all duration-200"
                        aria-label="Abrir filtros"
                    >
                        <FunnelIcon class="h-5 w-5 text-white" />
                    </button>
                </div>
            </div>
        </header>

        <!-- Customer Greeting -->
        <div v-if="auth && auth.customer" class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-gray-600">
                    Olá, <span class="font-medium text-black">usuário</span>!
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>

        <!-- Mobile Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-[#231F20] border-t border-white/10 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-around py-2">
                    <!-- Início -->
                    <Link
                        :href="route('products')"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': currentRoute === 'products',
                            'text-gray-400 hover:text-white': currentRoute !== 'products'
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200"
                            :class="{
                                'bg-white/20': currentRoute === 'products',
                                'group-hover:bg-white/10': currentRoute !== 'products'
                            }">
                            <HomeIcon class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-medium mt-1">Início</span>
                    </Link>

                    <!-- Categorias -->
                    <Link
                        :href="route('categories')"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': currentRoute === 'categories',
                            'text-gray-400 hover:text-white': currentRoute !== 'categories'
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200"
                            :class="{
                                'bg-white/20': currentRoute === 'categories',
                                'group-hover:bg-white/10': currentRoute !== 'categories'
                            }">
                            <Squares2X2Icon class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-medium mt-1">Categorias</span>
                    </Link>

                    <!-- Carrinho -->
                    <Link
                        :href="route('customer.cart')"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': currentRoute === 'customer.cart',
                            'text-gray-400 hover:text-white': currentRoute !== 'customer.cart'
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200 relative"
                            :class="{
                                'bg-white/20': currentRoute === 'customer.cart',
                                'group-hover:bg-white/10': currentRoute !== 'customer.cart'
                            }">
                            <ShoppingCartIcon class="w-5 h-5" />
                            <span class="absolute -top-1 -right-1 bg-black text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">0</span>
                        </div>
                        <span class="text-[10px] font-medium mt-1">Carrinho</span>
                    </Link>

                    <!-- Conta -->
                    <Link
                        :href="auth ? route('customer.dashboard') : route('customer.login')"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': currentRoute === 'customer.dashboard' || currentRoute === 'customer.login',
                            'text-gray-400 hover:text-white': currentRoute !== 'customer.dashboard' && currentRoute !== 'customer.login'
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200"
                            :class="{
                                'bg-white/20': currentRoute === 'customer.dashboard' || currentRoute === 'customer.login',
                                'group-hover:bg-white/10': currentRoute !== 'customer.dashboard' && currentRoute !== 'customer.login'
                            }">
                            <UserIcon class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-medium mt-1 truncate max-w-[60px] text-center">{{ auth ? auth.name : 'Entrar' }}</span>
                    </Link>
                </div>
            </div>
        </nav>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { 
    MagnifyingGlassIcon, 
    FunnelIcon,
    ShoppingCartIcon, 
    HomeIcon,
    UserIcon,
    Squares2X2Icon
} from '@heroicons/vue/24/outline'
import debounce from 'lodash/debounce'
import { router } from '@inertiajs/vue3'

const page = usePage()
const currentRoute = computed(() => route().current())
const auth = computed(() => page.props.auth)
const cartItemCount = ref(0)

const search = ref('')

const debouncedSearch = debounce(() => {
    router.get(
        route('products'),
        { search: search.value },
        { preserveState: true, preserveScroll: true }
    )
}, 300)
</script>
