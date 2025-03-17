<script setup>
import { ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

import { 
    UserGroupIcon,
    ShoppingBagIcon,
    UserIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    statistics: Object
});

const cards = ref([
    { 
        title: 'Total de Clientes', 
        value: props.statistics.customers, 
        icon: UserGroupIcon,
        color: 'text-blue-600',
        bgColor: 'bg-blue-50'
    },
    { 
        title: 'Total de Produtos', 
        value: props.statistics.products,
        icon: ShoppingBagIcon,
        color: 'text-indigo-600',
        bgColor: 'bg-indigo-50'
    },
    { 
        title: 'Total de Revendedores', 
        value: props.statistics.resellers,
        icon: UserIcon,
        color: 'text-purple-600',
        bgColor: 'bg-purple-50'
    },
]);


</script>

<template>
    <Head title="Painel de Controle" />

    <DashboardLayout>
        <div class="space-y-8">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100 transition-all duration-300 ease-in-out hover:shadow-md">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-primary-500">
                        Painel de Controle
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Acompanhe as estatísticas do seu negócio.</p>
                </div>
            </div>

            <!-- Cards Grid -->
            <TransitionGroup
                enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                move-class="transition-transform duration-300"
                appear
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="(card, index) in cards"
                        :key="card.title"
                        class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 ease-in-out group hover:scale-[1.02] hover:bg-gray-50/50"
                    >
                        <dt>
                            <div :class="[card.bgColor, 'absolute rounded-xl p-3 transition-transform duration-300 ease-in-out group-hover:scale-110']">
                                <component :is="card.icon" :class="[card.color, 'h-6 w-6']" aria-hidden="true" />
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">{{ card.title }}</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-6">
                            <p class="text-2xl font-semibold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-700">{{ card.value }}</p>

                        </dd>
                    </div>
                </div>
            </TransitionGroup>


        </div>
    </DashboardLayout>
</template>
