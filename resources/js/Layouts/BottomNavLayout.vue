<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import {
    HomeIcon,
    ShoppingBagIcon,
    ShoppingCartIcon,
    UserIcon,
} from '@heroicons/vue/24/outline';
import axios from 'axios';

const navigation = [
    { name: 'Início', href: route('customer.dashboard'), icon: HomeIcon },
    { name: 'Pedidos', href: route('orders.index'), icon: ShoppingBagIcon },
    { name: 'Carrinho', href: route('cart.index'), icon: ShoppingCartIcon },
    { name: 'Perfil', href: route('customer.profile'), icon: UserIcon },
];

// Contadores
const cartItemCount = ref(0);
const ordersCount = ref(0);

// Atualiza o contador do carrinho
const updateCartCount = async () => {
    try {
        const response = await axios.get(route('cart.count'));
        cartItemCount.value = response.data.count;
    } catch (error) {
        console.error('Erro ao buscar contagem do carrinho:', error);
    }
};

// Atualiza o contador de pedidos
const updateOrdersCount = async () => {
    try {
        const response = await axios.get(route('customer.orders.count'));
        ordersCount.value = response.data.count;
    } catch (error) {
        console.error('Erro ao buscar contagem de pedidos:', error);
    }
};

// Atualiza os contadores ao montar o componente
onMounted(() => {
    updateCartCount();
    updateOrdersCount();

    // Escuta eventos de atualização do carrinho
    window.addEventListener('cart-updated', (event) => {
        if (event.detail && typeof event.detail.count === 'number') {
            cartItemCount.value = event.detail.count;
        } else {
            updateCartCount();
        }
    });
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 pb-16 md:pb-0">
        <slot />
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <slot name="content" />
        </div>
        
        <!-- Menu Mobile -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-[#231F20] border-t border-white/10 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-around py-2">
                    <!-- Início -->
                    <Link
                        :href="navigation[0].href"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': route().current(navigation[0].href),
                            'text-gray-400 hover:text-white': !route().current(navigation[0].href)
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200"
                            :class="{
                                'bg-white/20': route().current(navigation[0].href),
                                'group-hover:bg-white/10': !route().current(navigation[0].href)
                            }">
                            <component :is="navigation[0].icon" class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-medium mt-1">{{ navigation[0].name }}</span>
                    </Link>

                    <!-- Pedidos -->
                    <Link
                        :href="navigation[1].href"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': route().current(navigation[1].href),
                            'text-gray-400 hover:text-white': !route().current(navigation[1].href)
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200 relative"
                            :class="{
                                'bg-white/20': route().current(navigation[1].href),
                                'group-hover:bg-white/10': !route().current(navigation[1].href)
                            }">
                            <component :is="navigation[1].icon" class="w-5 h-5" />
                            <span v-if="ordersCount > 0" 
                                class="absolute -top-1 -right-1 bg-black text-white text-[10px] font-bold rounded-full min-w-[16px] h-4 px-1 flex items-center justify-center animate-bounce-subtle"
                            >
                                {{ ordersCount }}
                            </span>
                        </div>
                        <span class="text-[10px] font-medium mt-1">{{ navigation[1].name }}</span>
                    </Link>

                    <!-- Carrinho -->
                    <Link
                        :href="navigation[2].href"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': route().current(navigation[2].href),
                            'text-gray-400 hover:text-white': !route().current(navigation[2].href)
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200 relative"
                            :class="{
                                'bg-white/20': route().current(navigation[2].href),
                                'group-hover:bg-white/10': !route().current(navigation[2].href)
                            }">
                            <component :is="navigation[2].icon" class="w-5 h-5" />
                            <span v-if="cartItemCount > 0" 
                                class="absolute -top-1 -right-1 bg-black text-white text-[10px] font-bold rounded-full min-w-[16px] h-4 px-1 flex items-center justify-center animate-bounce-subtle"
                            >
                                {{ cartItemCount }}
                            </span>
                        </div>
                        <span class="text-[10px] font-medium mt-1">{{ navigation[2].name }}</span>
                    </Link>

                    <!-- Perfil -->
                    <Link
                        :href="navigation[3].href"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': route().current(navigation[3].href),
                            'text-gray-400 hover:text-white': !route().current(navigation[3].href)
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200"
                            :class="{
                                'bg-white/20': route().current(navigation[3].href),
                                'group-hover:bg-white/10': !route().current(navigation[3].href)
                            }">
                            <component :is="navigation[3].icon" class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-medium mt-1">{{ navigation[3].name }}</span>
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