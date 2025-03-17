<script setup>
import { Head } from '@inertiajs/vue3';
import CustomerDashboardLayout from '@/Layouts/CustomerDashboardLayout.vue';
import { HeartIcon } from '@heroicons/vue/24/outline';
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid';

defineProps({
    favorites: Array
});
</script>

<template>
    <Head title="Favoritos" />

    <CustomerDashboardLayout>
        <div class="animate-fade-in-up">
            <!-- Header -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Meus Favoritos
                </h1>
                <p class="mt-2 text-gray-600">
                    Produtos que você marcou como favorito
                </p>
            </div>

            <!-- Empty State -->
            <div v-if="!favorites.length" class="mt-8 text-center py-12 px-4 bg-white rounded-2xl shadow-sm border border-gray-100">
                <HeartIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum favorito</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Você ainda não adicionou nenhum produto aos favoritos
                </p>
                <div class="mt-6">
                    <Link
                        :href="route('products')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 ease-in-out"
                    >
                        Ver produtos
                    </Link>
                </div>
            </div>

            <!-- Products Grid -->
            <div v-else class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="product in favorites"
                    :key="product.id"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-all duration-200 ease-in-out"
                >
                    <!-- Product Image -->
                    <div class="aspect-square overflow-hidden">
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200 ease-in-out"
                        />
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <div class="text-xs text-indigo-600 font-medium">
                            {{ product.department }}
                        </div>
                        <h3 class="mt-1 text-sm font-medium text-gray-900 truncate">
                            {{ product.name }}
                        </h3>
                        <p class="mt-1 text-sm font-semibold text-indigo-600">
                            {{ product.price }}
                        </p>

                        <!-- Actions -->
                        <div class="mt-4 flex items-center justify-between">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 ease-in-out"
                            >
                                Adicionar ao carrinho
                            </button>
                            <button
                                class="p-2 rounded-full bg-gray-50 hover:bg-gray-100 transition-colors duration-200"
                            >
                                <HeartIconSolid class="h-5 w-5 text-red-500" />
                            </button>
                        </div>
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
