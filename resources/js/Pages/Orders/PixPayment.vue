<template>
    <ProductShowcaseLayout :auth="auth">
        <div class="animate-fade-in-up max-w-3xl mx-auto">
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-100">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-[#231F20]">
                        Pagamento via PIX
                    </h1>
                    
                    <p class="mt-2 text-gray-600">
                        Escaneie o QR Code abaixo para finalizar seu pagamento
                    </p>
                    
                    <div class="mt-6 flex justify-center">
                        <div class="p-4 bg-white border-2 border-gray-200 rounded-lg">
                            <img :src="pixData.qrcode_image" alt="QR Code PIX" class="w-64 h-64" />
                        </div>
                    </div>
                    
                    <div class="mt-6 max-w-md mx-auto">
                        <div class="bg-blue-50 p-4 rounded-lg text-left">
                            <h3 class="font-medium text-blue-800">Instruções:</h3>
                            <ol class="mt-2 text-sm text-blue-700 space-y-2 list-decimal list-inside">
                                <li>Abra o aplicativo do seu banco</li>
                                <li>Acesse a área de pagamentos via PIX</li>
                                <li>Escaneie o QR Code acima</li>
                                <li>Confirme as informações e finalize o pagamento</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h3 class="font-medium text-[#231F20]">Ou copie o código PIX:</h3>
                        <div class="mt-2 relative">
                            <input
                                type="text"
                                readonly
                                :value="pixData.qrcode_text"
                                class="block w-full rounded-md border-gray-300 bg-gray-50 pr-10 focus:border-[#231F20] focus:ring-[#231F20] sm:text-sm"
                            />
                            <button
                                @click="copyPixCode"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-[#231F20]"
                            >
                                <ClipboardIcon class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="copied" class="mt-1 text-sm text-green-600">Código copiado!</p>
                    </div>
                </div>
                
                <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-100">
                    <h2 class="text-lg font-medium text-[#231F20] mb-4">Resumo do Pedido #{{ order.id }}</h2>
                    
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
                
                <div class="mt-6 text-center">
                    <div class="text-sm text-gray-600 mb-4">
                        <p>O QR Code tem validade de <span class="font-medium">30 minutos</span>.</p>
                        <p class="mt-1">Após o pagamento, seu pedido será processado automaticamente.</p>
                    </div>
                    
                    <div class="flex justify-center mt-6">
                        <Link :href="route('orders.show', order.id)"
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                        >
                            Ver Detalhes do Pedido
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'
import { ClipboardIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    auth: Object,
    order: Object,
    pixData: Object
})

const copied = ref(false)

// Função para copiar o código PIX
const copyPixCode = () => {
    navigator.clipboard.writeText(props.pixData.qrcode_text)
    copied.value = true
    
    // Esconder a mensagem após 3 segundos
    setTimeout(() => {
        copied.value = false
    }, 3000)
}

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
