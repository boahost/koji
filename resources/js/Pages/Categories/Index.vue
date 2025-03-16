<script setup>
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import { MagnifyingGlassIcon, FunnelIcon, ArrowDownTrayIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

const searchQuery = ref('');

const filteredCategories = computed(() => {
    if (!searchQuery.value) return props.categories;
    const query = searchQuery.value.toLowerCase();
    return props.categories.filter(category => 
        category.name.toLowerCase().includes(query)
    );
});

const confirmDeletion = (category) => {
    Swal.fire({
        title: 'Tem certeza?',
        text: `Você está prestes a excluir a categoria "${category.name}". Esta ação não pode ser desfeita.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1e3a8a',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('categories.destroy', category.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Excluída!',
                        text: 'A categoria foi excluída com sucesso.',
                        icon: 'success',
                        confirmButtonColor: '#1e3a8a',
                    });
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Categorias" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Categorias
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Gerencie suas categorias de produtos de forma simples e eficiente.</p>
                </div>
                <div class="mt-4 flex sm:ml-4 sm:mt-0">
                    <Link
                        :href="route('categories.create')"
                        class="inline-flex items-center gap-x-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                    >
                        <PlusIcon class="h-5 w-5" aria-hidden="true" />
                        Adicionar Categoria
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
                        placeholder="Buscar categorias..."
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

            <!-- Tabela de Categorias -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="min-w-full divide-y divide-gray-200">
                    <div class="bg-gray-50/50">
                        <div class="grid grid-cols-2 gap-4 px-6 py-4 text-left text-sm font-semibold text-gray-900">
                            <div>Nome</div>
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
                            v-for="(category, index) in filteredCategories"
                            :key="category.id"
                            :style="{ '--delay': `${index * 100}ms` }"
                            class="group hover:bg-gray-50/50 transition-colors duration-200 ease-in-out"
                        >
                            <div class="grid grid-cols-2 gap-4 px-6 py-4 text-sm items-center">
                                <div class="font-medium text-gray-900">
                                    {{ category.name }}
                                </div>
                                <div class="text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <Link 
                                            :href="route('categories.edit', category.id)"
                                            class="inline-flex items-center text-gray-700 hover:text-primary-600 transition-colors duration-200 ease-in-out"
                                            title="Editar"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                        <button 
                                            @click="confirmDeletion(category)"
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
