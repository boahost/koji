<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    HomeIcon,
    ShoppingBagIcon,
    HeartIcon,
    UserIcon,
    ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline';

const navigation = [
    { name: 'Início', href: route('products'), icon: HomeIcon },
    { name: 'Pedidos', href: route('customer.dashboard'), icon: ShoppingBagIcon },
    { name: 'Favoritos', href: route('favorites'), icon: HeartIcon },
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
                        <Link :href="route('products')" class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            DF Store
                        </Link>
                    </div>

                    <!-- Logout Button -->
                    <div class="flex items-center">
                        <Link
                            :href="route('customer.logout')"
                            method="post"
                            as="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 ease-in-out"
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
                            'text-indigo-600': route().current(item.href),
                            'text-gray-500 hover:text-indigo-600': !route().current(item.href)
                        }"
                    >
                        <div class="p-2 rounded-full transition-all duration-200 ease-in-out"
                            :class="{
                                'bg-indigo-50 scale-110': route().current(item.href),
                                'group-hover:bg-indigo-50 group-hover:scale-110': !route().current(item.href)
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
