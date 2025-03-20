<template>
    <ResellerDashboardLayout :reseller="props.reseller">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-gray-900 animate-fade-in-up">Dashboard</h1>
            </div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
                <!-- Seção da URL de Referência -->
                <div class="mb-8 animate-fade-in-up" style="animation-delay: 50ms;">
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Sua URL de Referência</h2>
                            <p class="text-sm text-gray-600 mb-4">
                                Compartilhe esta URL com seus clientes. Quando eles comprarem produtos através deste link, 
                                você receberá uma comissão de {{ props.reseller.commission_rate }}% sobre o valor da venda.
                            </p>
                            
                            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                                <div class="flex-1 w-full bg-gray-50 p-3 rounded-lg border border-gray-200 text-gray-800 break-all">
                                    {{ referralUrl }}
                                </div>
                                <button 
                                    @click="copyToClipboard" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                                    :class="{ 'bg-green-600 hover:bg-green-700 focus:ring-green-500': copied }"
                                >
                                    <component :is="copied ? CheckIcon : ClipboardIcon" class="h-5 w-5 mr-2" />
                                    {{ copied ? 'Copiado!' : 'Copiar URL' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção de Itens Pendentes no Carrinho -->
                <div class="mt-8 animate-fade-in-up" style="animation-delay: 400ms;">
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Vendas em Andamento</h2>
                            <p class="text-sm text-gray-600 mb-4">
                                Produtos no carrinho de clientes que acessaram através do seu link de referência.
                            </p>

                            <!-- Lista de Itens -->
                            <div v-if="props.pendingCartItems.length > 0" class="space-y-4">
                                <div v-for="item in props.pendingCartItems" :key="item.id" 
                                    class="group bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-500 transition-all duration-200">
                                    <div class="flex items-start gap-4">
                                        <!-- Imagem do Produto -->
                                        <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                            <img :src="`/storage/${item.product.featured_image}`" :alt="item.product.name"
                                                class="w-full h-full object-cover">
                                        </div>

                                        <!-- Informações do Produto e Cliente -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900 line-clamp-1">
                                                        {{ item.product.name }}
                                                    </h3>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        Cliente: {{ item.customer.name }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ formatCurrency(item.total) }}
                                                    </p>
                                                    <p class="mt-1 text-xs text-indigo-600">
                                                        Comissão Potencial: {{ formatCurrency(item.potential_commission) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <ShoppingCartIcon class="h-4 w-4 mr-1" />
                                                <span>{{ item.quantity }} {{ item.quantity === 1 ? 'unidade' : 'unidades' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado vazio -->
                            <div v-else class="text-center py-8">
                                <ShoppingCartIcon class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma venda em andamento</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Compartilhe seu link de referência para começar a vender.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

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
import { ref } from 'vue'
import ResellerDashboardLayout from '@/Layouts/ResellerDashboardLayout.vue'
import { 
    BanknotesIcon, 
    ClockIcon, 
    CheckCircleIcon, 
    ClipboardIcon, 
    CheckIcon,
    ShoppingCartIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    reseller: {
        type: Object,
        required: true
    },
    receivable: {
        type: Object,
        required: true
    },
    referralUrl: {
        type: String,
        required: true
    },
    pendingCartItems: {
        type: Array,
        required: true
    }
})

const copied = ref(false)

const copyToClipboard = () => {
    navigator.clipboard.writeText(props.referralUrl).then(() => {
        copied.value = true
        setTimeout(() => {
            copied.value = false
        }, 2000)
    })
}

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
