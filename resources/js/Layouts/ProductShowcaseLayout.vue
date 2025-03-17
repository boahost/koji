<template>
    <div class="min-h-screen bg-gray-50 pb-16">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('products')" class="text-xl font-bold bg-gradient-to-r from-black to-gray-900 bg-clip-text text-transparent">
                                DF Store
                            </Link>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="flex-1 flex items-center justify-center px-2 lg:px-6">
                        <div class="w-full max-w-lg lg:max-w-xs">
                            <label for="search" class="sr-only">Buscar produtos</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                </div>
                                <input
                                    id="search"
                                    name="search"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 sm:text-sm"
                                    placeholder="Buscar produtos..."
                                    type="search"
                                    v-model="search"
                                    @input="debouncedSearch"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Right Navigation -->
                    <div class="flex items-center space-x-4">
                        <!-- Cart -->
                        <Link :href="route('customer.cart')" class="p-2 rounded-full hover:bg-gray-100 relative">
                            <ShoppingCartIcon class="h-6 w-6 text-gray-400 hover:text-gray-500" />
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 px-2 py-0.5 text-xs font-medium bg-black text-white rounded-full">0</span>
                        </Link>

                        <!-- Login/Profile -->
                        <template v-if="auth">
                            <Link 
                                :href="route('customer.dashboard')"
                                class="hidden sm:flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-black to-gray-900 rounded-lg hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200"
                            >
                                <UserIcon class="h-5 w-5 mr-2" />
                                Olá, {{ auth.name }}
                            </Link>
                        </template>
                        <template v-else>
                            <Link 
                                :href="route('customer.login')"
                                class="hidden sm:flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-black to-gray-900 rounded-lg hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200"
                            >
                                <UserIcon class="h-5 w-5 mr-2" />
                                Entrar
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

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
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-around py-2">
                    <!-- Início -->
                    <Link
                        :href="route('products')"
                        class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                        :class="{
                            'text-black': currentRoute === 'products',
                            'text-gray-500 hover:text-black': currentRoute !== 'products'
                        }"
                    >
                        <div class="p-2 rounded-full transition-all duration-200 ease-in-out"
                            :class="{
                                'bg-gray-100 scale-110': currentRoute === 'products',
                                'group-hover:bg-gray-100 group-hover:scale-110': currentRoute !== 'products'
                            }">
                            <HomeIcon class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-medium mt-1">Início</span>
                    </Link>

                    <!-- Carrinho -->
                    <Link
                        :href="route('customer.cart')"
                        class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                        :class="{
                            'text-black': currentRoute === 'customer.cart',
                            'text-gray-500 hover:text-black': currentRoute !== 'customer.cart'
                        }"
                    >
                        <div class="p-2 rounded-full transition-all duration-200 ease-in-out relative"
                            :class="{
                                'bg-gray-100 scale-110': currentRoute === 'customer.cart',
                                'group-hover:bg-gray-100 group-hover:scale-110': currentRoute !== 'customer.cart'
                            }">
                            <ShoppingCartIcon class="w-6 h-6" />
                            <span class="absolute -top-1 -right-1 bg-black text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-bounce">0</span>
                        </div>
                        <span class="text-xs font-medium mt-1">Carrinho</span>
                    </Link>

                    <!-- Minha Conta -->
                    <template v-if="auth">
                        <Link
                            :href="route('customer.dashboard')"
                            class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                            :class="{
                                'text-black': currentRoute === 'customer.dashboard',
                                'text-gray-500 hover:text-black': currentRoute !== 'customer.dashboard'
                            }"
                        >
                            <div class="p-2 rounded-full transition-all duration-200 ease-in-out"
                                :class="{
                                    'bg-gray-100 scale-110': currentRoute === 'customer.dashboard',
                                    'group-hover:bg-gray-100 group-hover:scale-110': currentRoute !== 'customer.dashboard'
                                }">
                                <UserIcon class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-medium mt-1">Olá {{ auth.name }}</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('customer.login')"
                            class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                            :class="{
                                'text-black': currentRoute === 'customer.login',
                                'text-gray-500 hover:text-black': currentRoute !== 'customer.login'
                            }"
                        >
                            <div class="p-2 rounded-full transition-all duration-200 ease-in-out"
                                :class="{
                                    'bg-gray-100 scale-110': currentRoute === 'customer.login',
                                    'group-hover:bg-gray-100 group-hover:scale-110': currentRoute !== 'customer.login'
                                }">
                                <UserIcon class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-medium mt-1">Entrar</span>
                        </Link>
                    </template>
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
    ShoppingCartIcon, 
    HomeIcon,
    UserIcon,
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
