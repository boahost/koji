<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    HomeIcon,
    ShoppingBagIcon,
    ShoppingCartIcon,
    UserIcon,
    ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline';
import AppHeader from '@/Components/AppHeader.vue';

const props = defineProps({
    customer: Object,
});

const navigation = [
    { name: 'Início', href: route('products'), icon: HomeIcon },
    { name: 'Pedidos', href: route('customer.dashboard'), icon: ShoppingBagIcon },
    { name: 'Carrinho', href: route('cart'), icon: ShoppingCartIcon },
    { name: 'Perfil', href: route('customer.profile'), icon: UserIcon },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50 pb-16">
        <AppHeader />
        
        <!-- Customer Welcome -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-600">
                    Olá, <span class="font-medium text-gray-900">{{ customer?.name?.split(' ')[0] }}</span>
                </span>
                <Link
                    :href="route('customer.logout')"
                    method="post"
                    as="button"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-full text-white bg-[#231F20] hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors duration-200"
                >
                    <ArrowRightOnRectangleIcon class="h-4 w-4 mr-1.5" />
                    Sair
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot />
        </main>

        <!-- Bottom Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-[#231F20] border-t border-white/10 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-around py-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="flex flex-col items-center group transition-all duration-200"
                        :class="{
                            'text-white': route().current(item.href),
                            'text-gray-400 hover:text-white': !route().current(item.href)
                        }"
                    >
                        <div class="p-2.5 rounded-full transition-all duration-200"
                            :class="{
                                'bg-white/20': route().current(item.href),
                                'group-hover:bg-white/10': !route().current(item.href)
                            }">
                            <component :is="item.icon" class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-medium mt-1">{{ item.name }}</span>
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
</style>
