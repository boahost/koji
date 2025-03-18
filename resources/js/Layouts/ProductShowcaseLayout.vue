<template>
    <div class="min-h-screen bg-gray-50 pb-16">
        <AppHeader @toggle-filters="$emit('toggle-filters')">
            <template #default>filter</template>
        </AppHeader>

        <!-- Customer Greeting -->
        <div v-if="auth" class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Olá, <span class="font-medium text-[#231F20]">{{ auth.name ?? auth.customer.name }}</span>!
                    </p>
                    <Link 
                        :href="route('customer.logout')"
                        method="post"
                        as="button"
                        class="text-sm text-gray-600 hover:text-[#231F20] transition-colors duration-200"
                    >
                        Sair
                    </Link>
                </div>
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
                            <Transition
                                enter-active-class="transform transition duration-300 ease-out"
                                enter-from-class="scale-50 opacity-0"
                                enter-to-class="scale-100 opacity-100"
                                leave-active-class="transform transition duration-200 ease-in"
                                leave-from-class="scale-100 opacity-100"
                                leave-to-class="scale-50 opacity-0"
                            >
                                <span v-if="cartItemCount > 0" 
                                    class="absolute -top-1 -right-1 bg-black text-white text-[10px] font-bold rounded-full min-w-[16px] h-4 px-1 flex items-center justify-center animate-bounce-subtle"
                                >
                                    {{ cartItemCount }}
                                </span>
                            </Transition>
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
                        <span class="text-[10px] font-medium mt-1 truncate max-w-[60px] text-center">{{ (auth.name ?? auth.customer.name) ? (auth.name ?? auth.customer.name) : 'Entrar'  }}</span>
                    </Link>
                </div>
            </div>
        </nav>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { 
    ShoppingCartIcon, 
    HomeIcon,
    UserIcon
} from '@heroicons/vue/24/outline'
import AppHeader from '@/Components/AppHeader.vue'
import axios from 'axios'

const page = usePage()
const currentRoute = computed(() => route().current())
const auth = computed(() => page.props.auth)
const cartItemCount = ref(0)

// Atualiza o contador do carrinho
const updateCartCount = async () => {
    try {
        const response = await axios.get(route('cart.count'))
        cartItemCount.value = response.data.count
    } catch (error) {
        console.error('Erro ao buscar contagem do carrinho:', error)
    }
}

// Define os emits
const emits = defineEmits(['toggle-filters'])

// Atualiza o contador ao montar o componente
onMounted(() => {
    updateCartCount()

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

<style>
@keyframes bounce-subtle {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-2px);
    }
}

.animate-bounce-subtle {
    animation: bounce-subtle 2s ease-in-out infinite;
}
</style>
