<template>
    <ResellerDashboardLayout>
        <div class="space-y-8">
            <TransitionGroup
                enter-active-class="transition-all duration-300"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <!-- Card de Valor Total -->
                <div :key="'total'" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 px-6 py-8 text-white shadow-lg transition-transform hover:scale-[1.02] duration-300 animate-fadeInUp">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-white/10 p-3">
                            <BanknotesIcon class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white/80">Total a Receber</p>
                            <p class="text-2xl font-semibold">{{ formatCurrency(receivable.total) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card de Valor Pendente -->
                <div :key="'pending'" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 px-6 py-8 text-white shadow-lg transition-transform hover:scale-[1.02] duration-300 animate-fadeInUp" style="animation-delay: 100ms;">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-white/10 p-3">
                            <ClockIcon class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white/80">Pendente</p>
                            <p class="text-2xl font-semibold">{{ formatCurrency(receivable.pending) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card de Valor Pago -->
                <div :key="'paid'" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500 to-green-600 px-6 py-8 text-white shadow-lg transition-transform hover:scale-[1.02] duration-300 animate-fadeInUp" style="animation-delay: 200ms;">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-white/10 p-3">
                            <CheckCircleIcon class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white/80">Já Recebido</p>
                            <p class="text-2xl font-semibold">{{ formatCurrency(receivable.paid) }}</p>
                        </div>
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </ResellerDashboardLayout>
</template>

<script setup>
import ResellerDashboardLayout from '@/Layouts/ResellerDashboardLayout.vue'
import { BanknotesIcon, ClockIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
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
