<template>
    <ProductShowcaseLayout :auth="auth">
        <div class="animate-fade-in-up max-w-3xl mx-auto">
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-100 text-center">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                    <CheckIcon class="w-8 h-8 text-green-600" />
                </div>
                
                <h1 class="mt-4 text-2xl font-bold text-[#231F20]">
                    Pedido Confirmado!
                </h1>
                
                <p class="mt-2 text-gray-600">
                    Seu pedido #{{ order.id }} foi processado com sucesso.
                </p>
                
                <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-100">
                    <h2 class="text-lg font-medium text-[#231F20] mb-4">Resumo do Pedido</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-[#231F20]">{{ formatCurrency(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Frete</span>
                            <span class="font-medium text-[#231F20]">{{ formatCurrency(order.shipping_cost) }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-baseline">
                                <span class="text-base font-medium text-[#231F20]">Total</span>
                                <span class="text-xl font-bold text-[#231F20]">
                                    {{ formatCurrency(order.total) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 space-y-4">
                    <p class="text-sm text-gray-600">
                        Você receberá um e-mail com os detalhes do seu pedido em breve.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                        <Link :href="route('orders.show', order.id)"
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                        >
                            Ver Detalhes do Pedido
                        </Link>
                        
                        <Link :href="route('products')"
                            class="inline-flex items-center justify-center px-4 py-2 bg-white text-[#231F20] text-sm font-medium rounded-full border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                        >
                            Continuar Comprando
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'
import { CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    auth: Object,
    order: Object
})

// Formata o preço em reais
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}
</script>

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
