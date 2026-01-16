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
            { name: 'Início', href: route('customer.dashboard'), icon: HomeIcon },
    { name: 'Pedidos', href: route('customer.dashboard'), icon: ShoppingBagIcon },
    { name: 'Carrinho', href: route('cart.index'), icon: ShoppingCartIcon },
    { name: 'Perfil', href: route('customer.profile'), icon: UserIcon },
];
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-50 to-gray-100 pb-16">
        <AppHeader @search-historico="$emit('search-historico', $event)" />
        
        <!-- Customer Welcome -->
        <div v-if="customer" class="bg-white border-b border-gray-200">
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
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
            <slot />
        </main>
    </div>
</template>

<style>
.scale-110 {
    transform: scale(1.1);
}
</style>
