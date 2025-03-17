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
        <!-- Top Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link :href="route('products')" class="text-xl font-bold bg-gradient-to-r from-black to-gray-900 bg-clip-text text-transparent">
                            DF Store
                        </Link>
                    </div>

                    <!-- Welcome and Logout Button -->
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">
                            Olá, {{ customer?.name?.split(' ')[0] }}
                        </span>
                        <Link
                            :href="route('customer.logout')"
                            method="post"
                            as="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gradient-to-r from-black to-gray-900 hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 ease-in-out"
                        >
                            <ArrowRightOnRectangleIcon class="h-5 w-5 mr-1" />
                            Sair
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot />
        </main>

        <!-- Bottom Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-around py-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="flex flex-col items-center group transition-all duration-200 ease-in-out"
                        :class="{
                            'text-black': route().current(item.href),
                            'text-gray-500 hover:text-black': !route().current(item.href)
                        }"
                    >
                        <div class="p-2 rounded-full transition-all duration-200 ease-in-out"
                            :class="{
                                'bg-gray-100 scale-110': route().current(item.href),
                                'group-hover:bg-gray-100 group-hover:scale-110': !route().current(item.href)
                            }">
                            <component :is="item.icon" class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-medium mt-1">{{ item.name }}</span>
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
