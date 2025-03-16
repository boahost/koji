<template>
    <Head title="Revendedores" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Revendedores
                    </h2>
                    <p class="mt-1 text-base text-gray-500">
                        Gerencie os revendedores do sistema
                    </p>
                </div>
                <div class="mt-4 flex sm:ml-4 sm:mt-0">
                    <Link
                        :href="route('resellers.create')"
                        class="flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors"
                    >
                        <PlusIcon class="h-5 w-5" />
                        Novo Revendedor
                    </Link>
                </div>
            </div>

            <!-- Lista de Revendedores -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <TransitionGroup
                    tag="ul"
                    :enter-active-class="fadeInUp.enterActiveClass"
                    :enter-from-class="fadeInUp.enterFromClass"
                    :enter-to-class="fadeInUp.enterToClass"
                    :leave-active-class="fadeInUp.leaveActiveClass"
                    :leave-from-class="fadeInUp.leaveFromClass"
                    :leave-to-class="fadeInUp.leaveToClass"
                    class="divide-y divide-gray-100"
                >
                    <li v-for="reseller in resellers.data" :key="reseller.id" class="relative">
                        <div class="flex items-center justify-between gap-x-6 p-5">
                            <div class="min-w-0">
                                <div class="flex items-start gap-x-3">
                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                        {{ reseller.name }}
                                    </p>
                                    <p class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-green-700 bg-green-50 ring-green-600/20">
                                        {{ reseller.commission_rate }}
                                    </p>
                                </div>
                                <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                    <p class="whitespace-nowrap">
                                        {{ reseller.document }}
                                    </p>
                                    <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current">
                                        <circle cx="1" cy="1" r="1" />
                                    </svg>
                                    <p class="truncate">{{ reseller.email }}</p>
                                </div>
                            </div>
                            <div class="flex flex-none items-center gap-x-4">
                                <Link
                                    :href="route('resellers.edit', reseller.id)"
                                    class="rounded-lg p-2 text-gray-600 hover:text-gray-900 transition-colors"
                                >
                                    <PencilIcon class="h-5 w-5" />
                                </Link>
                                <button
                                    @click="confirmDelete(reseller)"
                                    class="rounded-lg p-2 text-gray-600 hover:text-red-600 transition-colors"
                                >
                                    <TrashIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </li>
                </TransitionGroup>

                <!-- Empty State -->
                <div
                    v-if="!resellers.data.length"
                    class="text-center px-6 py-10 border-t border-gray-100"
                >
                    <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhum revendedor</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Comece cadastrando um novo revendedor.
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="route('resellers.create')"
                            class="inline-flex items-center gap-x-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors"
                        >
                            <PlusIcon class="-ml-0.5 h-5 w-5" aria-hidden="true" />
                            Novo Revendedor
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Paginação -->
            <div v-if="resellers.data.length" class="mt-4 flex items-center justify-between gap-4">
                <p class="text-sm text-gray-700">
                    Mostrando
                    <span class="font-medium">{{ resellers.from }}</span>
                    a
                    <span class="font-medium">{{ resellers.to }}</span>
                    de
                    <span class="font-medium">{{ resellers.total }}</span>
                    revendedores
                </p>
                <nav class="flex items-center gap-2">
                    <template v-for="(link, index) in resellers.links" :key="index">
                        <div 
                            v-if="!link.url"
                            class="relative inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-gray-400 ring-1 ring-inset ring-gray-300 opacity-50 cursor-not-allowed"
                        >
                            <template v-if="link.label.includes('Previous')">← Anterior</template>
                            <template v-else-if="link.label.includes('Next')">Próximo →</template>
                            <template v-else>{{ link.label }}</template>
                        </div>
                        <Link
                            v-else
                            :href="link.url"
                            class="relative inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold ring-1 ring-inset transition-colors"
                            :class="[
                                link.active 
                                    ? 'z-10 bg-primary-600 text-white hover:bg-primary-500 ring-primary-600' 
                                    : 'text-gray-900 ring-gray-300 hover:bg-gray-50'
                            ]"
                        >
                            <template v-if="link.label.includes('Previous')">← Anterior</template>
                            <template v-else-if="link.label.includes('Next')">Próximo →</template>
                            <template v-else>{{ link.label }}</template>
                        </Link>
                    </template>
                </nav>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { PlusIcon, PencilIcon, TrashIcon, UserGroupIcon } from '@heroicons/vue/24/outline';
import { fadeInUp } from '@/Utils/animations';
import Swal from 'sweetalert2';

const props = defineProps({
    resellers: {
        type: Object,
        required: true
    }
});

const confirmDelete = async (reseller) => {
    const result = await Swal.fire({
        title: 'Remover revendedor?',
        text: `Você tem certeza que deseja remover ${reseller.name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, remover',
        cancelButtonText: 'Cancelar',
        showClass: {
            popup: fadeInUp.enterActiveClass
        },
        hideClass: {
            popup: fadeInUp.leaveActiveClass
        }
    });

    if (result.isConfirmed) {
        router.delete(route('resellers.destroy', reseller.id), {
            onSuccess: () => {
                Swal.fire({
                    title: 'Revendedor removido!',
                    text: 'O revendedor foi removido com sucesso.',
                    icon: 'success',
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    showClass: {
                        popup: fadeInUp.enterActiveClass
                    },
                    hideClass: {
                        popup: fadeInUp.leaveActiveClass
                    }
                });
            }
        });
    }
};
</script>
