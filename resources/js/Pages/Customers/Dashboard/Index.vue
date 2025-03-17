<script setup>
import { Head, Link } from '@inertiajs/vue3';
import CustomerDashboardLayout from '@/Layouts/CustomerDashboardLayout.vue';

defineProps({
    customer: Object,
    ordersCount: Number,
    cartItemsCount: Number,
});
</script>

<template>
    <Head title="Dashboard" />

    <CustomerDashboardLayout :customer="customer">
        <div class="animate-fade-in-up">
            <!-- Welcome Section -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-black to-gray-900 bg-clip-text text-transparent">
                    Bem-vindo(a) ao seu Dashboard
                </h1>
                <p class="mt-2 text-gray-600">
                    Gerencie seus pedidos e informações pessoais
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2">
                <!-- Pedidos -->
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-gray-100 transition-all duration-200 hover:shadow-md">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gray-100 rounded-xl p-3">
                                <svg class="h-6 w-6 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total de Pedidos
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ ordersCount }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <Link
                                :href="route('customer.orders')"
                                class="font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200"
                            >
                                Meus Pedidos
                            </Link>
                        </div>
                    </div>
                </div>



                <!-- Carrinho -->
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-gray-100 transition-all duration-200 hover:shadow-md">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gray-100 rounded-xl p-3">
                                <svg class="h-6 w-6 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Itens no Carrinho
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ cartItemsCount }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <Link
                                :href="route('cart')"
                                class="font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200"
                            >
                                Ver Carrinho
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerDashboardLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
