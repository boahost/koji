<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <!-- Cabeçalho -->
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-[#231F20]">Pagamento PIX</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Pedido #{{ order.id }}
                    </p>
                </div>

                <!-- Informações do Pedido -->
                <div class="mt-6">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm text-gray-600">Total do Pedido:</span>
                            <span class="text-lg font-bold text-[#231F20]">{{ formatCurrency(order.total) }}</span>
                        </div>
                        <div class="mt-2 flex justify-between items-baseline">
                            <span class="text-sm text-gray-600">Data do Pedido:</span>
                            <span class="text-sm text-gray-800">{{ order.created_at }}</span>
                        </div>
                    </div>
                </div>

                <!-- QR Code PIX -->
                <div class="mt-8">
                    <div class="bg-white rounded-lg p-6 border-2 border-dashed border-gray-200">
                        <div class="text-center">
                            <img v-if="pix.qr_code_base64" 
                                :src="'data:image/png;base64,' + pix.qr_code_base64" 
                                alt="QR Code PIX"
                                class="mx-auto w-48 h-48"
                            />
                            <div v-else class="w-48 h-48 mx-auto bg-gray-100 rounded-lg flex items-center justify-center">
                                <span class="text-gray-400">QR Code não disponível</span>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">Escaneie o QR Code com o app do seu banco</p>
                                <div v-if="pix.qr_code" class="mt-4">
                                    <p class="text-xs text-gray-500 mb-2">Ou copie o código PIX abaixo:</p>
                                    <div class="relative">
                                        <input type="text" 
                                            :value="pix.qr_code" 
                                            readonly
                                            class="w-full px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-[#231F20] focus:border-[#231F20]"
                                        />
                                        <button @click="copyPixCode(pix.qr_code)"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 text-xs font-medium text-[#231F20] hover:text-[#231F20]/70 transition-colors">
                                            Copiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instruções -->
                <div class="mt-8">
                    <h2 class="text-lg font-medium text-[#231F20]">Instruções</h2>
                    <ul class="mt-4 space-y-4">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-[#231F20]/10 text-[#231F20] text-sm font-medium">1</span>
                            <span class="ml-3 text-sm text-gray-600">Abra o aplicativo do seu banco</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-[#231F20]/10 text-[#231F20] text-sm font-medium">2</span>
                            <span class="ml-3 text-sm text-gray-600">Escolha pagar via PIX</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-[#231F20]/10 text-[#231F20] text-sm font-medium">3</span>
                            <span class="ml-3 text-sm text-gray-600">Escaneie o QR Code ou cole o código PIX</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-[#231F20]/10 text-[#231F20] text-sm font-medium">4</span>
                            <span class="ml-3 text-sm text-gray-600">Confirme as informações e finalize o pagamento</span>
                        </li>
                    </ul>
                </div>

                <!-- Tempo de Expiração -->
                <div v-if="pix.expiration_date" class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Este QR Code expira em: {{ formatExpirationDate(pix.expiration_date) }}
                    </p>
                </div>

                <!-- Botão de Voltar -->
                <div class="mt-8">
                    <Link :href="route('cart.index')"
                        class="block w-full text-center px-6 py-3 text-sm font-medium text-[#231F20] bg-[#231F20]/5 hover:bg-[#231F20]/10 rounded-lg transition-colors">
                        Voltar para o Carrinho
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    order: Object,
    pix: Object
})

// Formata o valor em reais
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}

// Formata a data de expiração
const formatExpirationDate = (date) => {
    return new Date(date).toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Copia o código PIX para a área de transferência
const copyPixCode = async (code) => {
    try {
        await navigator.clipboard.writeText(code)
        alert('Código PIX copiado!')
    } catch (err) {
        console.error('Erro ao copiar código PIX:', err)
        alert('Erro ao copiar código PIX')
    }
}
</script>
