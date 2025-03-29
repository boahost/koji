<template>
    <ProductShowcaseLayout :auth="auth">
        <div class="animate-fade-in-up max-w-3xl mx-auto">
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-100 text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                    <XMarkIcon class="w-8 h-8 text-red-600" />
                </div>
                
                <h1 class="mt-4 text-2xl font-bold text-[#231F20]">
                    Pagamento Não Aprovado
                </h1>
                
                <p class="mt-2 text-gray-600">
                    {{ errorMessage }}
                </p>
                
                <div class="mt-6 space-y-4">
                    <p class="text-sm text-gray-600">
                        Por favor, tente novamente com outro cartão ou método de pagamento.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                        <Link :href="route('orders.show', order.id)"
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                        >
                            Ver Detalhes do Pedido
                        </Link>
                        
                        <Link :href="route('cart.index')"
                            class="inline-flex items-center justify-center px-4 py-2 bg-white text-[#231F20] text-sm font-medium rounded-full border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                        >
                            Voltar ao Carrinho
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
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    auth: Object,
    order: Object,
    errorMessage: String
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