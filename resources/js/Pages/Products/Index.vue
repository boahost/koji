<script setup>
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { fadeInUp, fadeIn } from '@/Utils/animations.js';
import Swal from 'sweetalert2';
import VMasker from 'vanilla-masker';

import { MagnifyingGlassIcon, FunnelIcon, ArrowDownTrayIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    products: {
        type: Array,
        required: true
    }
});

const searchQuery = ref('');

const formatPrice = (price) => {
    return VMasker.toMoney(price.toString(), {
        prefix: 'R$ ',
        separator: ',',
        delimiter: '.',
        precision: 2
    });
};

const filteredProducts = computed(() => {
    if (!searchQuery.value) return props.products;
    const query = searchQuery.value.toLowerCase();
    return props.products.filter(product => 
        product.name.toLowerCase().includes(query) ||
        product.category.name.toLowerCase().includes(query) ||
        product.department.name.toLowerCase().includes(query) ||
        formatPrice(product.price).toLowerCase().includes(query)
    );
});

const confirmDeletion = (product) => {
    Swal.fire({
        title: 'Tem certeza?',
        text: `Você está prestes a excluir o produto "${product.name}". Esta ação não pode ser desfeita.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1e3a8a',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('products.destroy', product.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Excluído!',
                        text: 'O produto foi excluído com sucesso.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'swal2-popup-custom',
                            confirmButton: 'bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors duration-200'
                        },
                        buttonsStyling: false,
                        iconColor: '#4F46E5',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },
                        didOpen: (popup) => {
                            fadeInUp.enter(popup);
                            fadeIn.enter(popup);
                        }
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Erro ao excluir produto. Tente novamente.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'swal2-popup-custom',
                            confirmButton: 'bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200'
                        },
                        buttonsStyling: false,
                        iconColor: '#DC2626',
                        showClass: {
                            popup: 'animate__animated animate__shakeX'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut'
                        },
                        didOpen: (popup) => {
                            fadeIn.enter(popup);
                        }
                    });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Produtos" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Produtos
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Gerencie seus produtos de forma simples e eficiente.</p>
                </div>
                <div class="mt-4 flex sm:ml-4 sm:mt-0">
                    <Link
                        :href="route('products.create')"
                        class="inline-flex items-center gap-x-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                    >
                        <PlusIcon class="h-5 w-5" aria-hidden="true" />
                        Adicionar Produto
                    </Link>
                </div>
            </div>

            <!-- Filtro -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </div>
                    <input
                        type="text"
                        v-model="searchQuery"
                        class="block w-full rounded-xl border-0 py-3 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 transition-shadow duration-200 ease-in-out"
                        placeholder="Buscar produtos..."
                    />
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-x-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all duration-200 ease-in-out"
                    >
                        <FunnelIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        Filtrar
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-x-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all duration-200 ease-in-out"
                    >
                        <ArrowDownTrayIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        Exportar
                    </button>
                </div>
            </div>

            <!-- Tabela de Produtos -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="min-w-full divide-y divide-gray-200">
                    <div class="bg-gray-50/50">
                        <div class="grid grid-cols-6 gap-4 px-6 py-4 text-left text-sm font-semibold text-gray-900">
                            <div>Imagem</div>
                            <div>Nome</div>
                            <div>Preço</div>
                            <div>Categoria</div>
                            <div>Departamento</div>
                            <div class="text-right">Ações</div>
                        </div>
                    </div>
                    <TransitionGroup
                        enter-active-class="transition-all duration-300 delay-[var(--delay)]"
                        enter-from-class="opacity-0 -translate-y-4"
                        enter-to-class="opacity-100 translate-y-0"
                        move-class="transition-transform duration-300"
                        appear
                    >
                        <div
                            v-for="(product, index) in filteredProducts"
                            :key="product.id"
                            :style="{ '--delay': `${index * 100}ms` }"
                            class="group hover:bg-gray-50/50 transition-colors duration-200 ease-in-out"
                        >
                            <div class="grid grid-cols-6 gap-4 px-6 py-4 text-sm items-center">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                        <img 
                                            v-if="product.featured_image" 
                                            :src="`/storage/${product.featured_image}`" 
                                            :alt="product.name"
                                            class="h-full w-full object-cover object-center"
                                        />
                                        <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="font-medium text-gray-900">
                                    {{ product.name }}
                                </div>
                                <div class="text-gray-900 font-medium">
                                    {{ formatPrice(product.price) }}
                                </div>
                                <div class="text-gray-500">{{ product.category.name }}</div>
                                <div class="text-gray-500">{{ product.department.name }}</div>
                                <div class="text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <Link 
                                            :href="route('products.edit', product.id)"
                                            class="inline-flex items-center text-gray-700 hover:text-primary-600 transition-colors duration-200 ease-in-out"
                                            title="Editar"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                        <button 
                                            @click="confirmDeletion(product)"
                                            class="inline-flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200 ease-in-out"
                                            title="Excluir"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
