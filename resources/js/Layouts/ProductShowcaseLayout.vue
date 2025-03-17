<template>
    <div class="min-h-screen bg-gray-50 pb-16">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('products')" class="text-xl font-bold text-gray-800">
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
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Buscar produtos..."
                                    type="search"
                                    v-model="search"
                                    @input="debouncedSearch"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Cart -->
                    <div class="flex items-center">
                        <button class="p-2 rounded-full hover:bg-gray-100 relative">
                            <ShoppingCartIcon class="h-6 w-6 text-gray-400 hover:text-gray-500" />
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 px-2 py-0.5 text-xs font-medium bg-blue-500 text-white rounded-full">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <p class="text-base text-gray-400 text-center">&copy; 2025 DF Store. Todos os direitos reservados.</p>
                </div>
            </div>
        </footer>
        <!-- Mobile Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-around py-2">
                    <!-- Início -->
                    <Link
                        :href="route('products')"
                        class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                        :class="{
                            'text-indigo-600': currentRoute === 'products',
                            'text-gray-500 hover:text-indigo-600': currentRoute !== 'products'
                        }"
                    >
                        <div class="p-2 rounded-full transition-all duration-200 ease-in-out"
                            :class="{
                                'bg-indigo-50 scale-110': currentRoute === 'products',
                                'group-hover:bg-indigo-50 group-hover:scale-110': currentRoute !== 'products'
                            }">
                            <HomeIcon class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-medium mt-1">Início</span>
                    </Link>

                    <!-- Carrinho -->
                    <Link
                        :href="route('cart')"
                        class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                        :class="{
                            'text-indigo-600': currentRoute === 'cart',
                            'text-gray-500 hover:text-indigo-600': currentRoute !== 'cart'
                        }"
                    >
                        <div class="p-2 rounded-full transition-all duration-200 ease-in-out relative"
                            :class="{
                                'bg-indigo-50 scale-110': currentRoute === 'cart',
                                'group-hover:bg-indigo-50 group-hover:scale-110': currentRoute !== 'cart'
                            }">
                            <ShoppingCartIcon class="w-6 h-6" />
                            <span class="absolute -top-1 -right-1 bg-indigo-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-bounce">0</span>
                        </div>
                        <span class="text-xs font-medium mt-1">Carrinho</span>
                    </Link>

                    <!-- Minha Conta -->
                    <Link
                        :href="route('customer.login')"
                        class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                        :class="{
                            'text-indigo-600': currentRoute === 'customer.login',
                            'text-gray-500 hover:text-indigo-600': currentRoute !== 'customer.login'
                        }"
                    >
                        <div class="p-2 rounded-full transition-all duration-200 ease-in-out"
                            :class="{
                                'bg-indigo-50 scale-110': currentRoute === 'customer.login',
                                'group-hover:bg-indigo-50 group-hover:scale-110': currentRoute !== 'customer.login'
                            }">
                            <UserIcon class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-medium mt-1">Minha Conta</span>
                    </Link>
                </div>
            </div>
        </nav>
    </div>
</template>

<style>
.scale-110 {
    transform: scale(1.1);
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-25%);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>

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

const search = ref('')

const debouncedSearch = debounce(() => {
    router.get(
        route('products'),
        { search: search.value },
        { preserveState: true, preserveScroll: true }
    )
}, 300)
</script>
