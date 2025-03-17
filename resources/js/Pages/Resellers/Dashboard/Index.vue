<template>
    <ResellerDashboardLayout :reseller="props.reseller">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-gray-900 animate-fade-in-up">Dashboard</h1>
            </div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
                <div class="py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <TransitionGroup
                            enter-active-class="transition-all duration-700 ease-out"
                            enter-from-class="opacity-0 translate-y-8 scale-95"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition-all duration-300 ease-in"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 translate-y-8 scale-95"
                            tag="div"
                        >
                        <!-- Card de Valor Total -->
                        <div :key="'total'" class="group bg-white overflow-hidden rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 active:scale-95 cursor-pointer animate-fade-in-up m-2" style="animation-delay: 100ms;">
                            <div class="p-5 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-in-out"></div>
                                <div class="flex items-center relative z-10">
                                    <div class="flex-shrink-0 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                        <BanknotesIcon class="h-6 w-6 text-blue-600 group-hover:text-blue-700" aria-hidden="true" />
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate group-hover:text-blue-600 transition-colors duration-300">Total a Receber</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-300">{{ formatCurrency(receivable.total) }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card de Valor Pendente -->
                        <div :key="'pending'" class="group bg-white overflow-hidden rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 active:scale-95 cursor-pointer animate-fade-in-up m-2" style="animation-delay: 200ms;">
                            <div class="p-5 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-yellow-50 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-in-out"></div>
                                <div class="flex items-center relative z-10">
                                    <div class="flex-shrink-0 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                        <ClockIcon class="h-6 w-6 text-yellow-600 group-hover:text-yellow-700" aria-hidden="true" />
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate group-hover:text-yellow-600 transition-colors duration-300">Pendente</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900 group-hover:text-yellow-700 transition-colors duration-300">{{ formatCurrency(receivable.pending) }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card de Valor Pago -->
                        <div :key="'paid'" class="group bg-white overflow-hidden rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 active:scale-95 cursor-pointer animate-fade-in-up m-2" style="animation-delay: 300ms;">
                            <div class="p-5 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-green-50 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-in-out"></div>
                                <div class="flex items-center relative z-10">
                                    <div class="flex-shrink-0 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                        <CheckCircleIcon class="h-6 w-6 text-green-600 group-hover:text-green-700" aria-hidden="true" />
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate group-hover:text-green-600 transition-colors duration-300">Já Recebido</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900 group-hover:text-green-700 transition-colors duration-300">{{ formatCurrency(receivable.paid) }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </TransitionGroup>
                    </div>
                </div>
            </div>
        </div>
    </ResellerDashboardLayout>
</template>

<script setup>
import ResellerDashboardLayout from '@/Layouts/ResellerDashboardLayout.vue'
import { BanknotesIcon, ClockIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    reseller: {
        type: Object,
        required: true
    },
    receivable: {
        type: Object,
        required: true
    }
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}
</script>

<style>
@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
}

@keyframes pulse-shadow {
    0% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.2);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

.group:hover {
    animation: pulse-shadow 1.5s infinite;
}

/* Animações específicas para dispositivos touch */
@media (hover: none) {
    .group:active {
        transform: scale(0.98);
        transition: transform 0.2s;
    }
    
    .group:active .flex-shrink-0 {
        transform: scale(1.1) rotate(6deg);
        transition: transform 0.2s;
    }
}
</style>
