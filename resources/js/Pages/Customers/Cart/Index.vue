<script setup>
import { Head, Link } from '@inertiajs/vue3';
import CustomerDashboardLayout from '@/Layouts/CustomerDashboardLayout.vue';
import { ShoppingBagIcon } from '@heroicons/vue/24/outline';

defineProps({
    customer: Object,
    cartItems: Array
});
</script>

<template>
    <Head title="Carrinho" />

    <CustomerDashboardLayout :customer="customer">
        <div class="min-h-[80vh] px-4 sm:px-6 lg:px-8 animate-fade-in-up">
            <!-- Header -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold text-gray-900 bg-clip-text text-transparent bg-gradient-to-r from-black to-gray-900">
                    Meu Carrinho
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Gerencie seus itens
                </p>
            </div>

            <!-- Empty State -->
            <div v-if="!cartItems.length" class="mt-8 text-center py-12 px-4 bg-white rounded-2xl shadow-sm border border-gray-100">
                <ShoppingBagIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Carrinho vazio</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Comece a adicionar produtos ao seu carrinho
                </p>
                <div class="mt-6">
                    <Link
                        :href="route('products')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-black to-gray-900 hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 ease-in-out"
                    >
                        Ver produtos
                    </Link>
                </div>
            </div>

            <!-- Cart Items List -->
            <div v-else class="mt-8 space-y-4">
                <div
                    v-for="item in cartItems"
                    :key="item.id"
                    class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center space-x-4"
                >
                    <!-- Item Image -->
                    <div class="flex-shrink-0 w-24 h-24">
                        <img
                            :src="item.image"
                            :alt="item.name"
                            class="w-full h-full object-cover rounded-xl"
                        />
                    </div>

                    <!-- Item Details -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ item.name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ item.price }}
                        </p>
                        <div class="mt-2 flex items-center space-x-2">
                            <button
                                class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 ease-in-out"
                            >
                                -
                            </button>
                            <span class="text-sm font-medium text-gray-900">{{ item.quantity }}</span>
                            <button
                                class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 ease-in-out"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Remove Button -->
                    <button
                        class="flex-shrink-0 p-2 text-gray-400 hover:text-gray-500 transition-colors duration-200"
                    >
                        <span class="sr-only">Remover</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Cart Summary -->
                <div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between text-base font-medium text-gray-900">
                        <p>Subtotal</p>
                        <p>R$ 0,00</p>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-500">Frete e impostos calculados no checkout.</p>
                    <div class="mt-6">
                        <button
                            class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-xl shadow-sm text-base font-medium text-white bg-gradient-to-r from-black to-gray-900 hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 ease-in-out"
                        >
                            Finalizar Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </CustomerDashboardLayout>
</template>

<style>
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
