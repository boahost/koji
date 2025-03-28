<template>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Cabeçalho -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Pagamento PIX</h1>
                <p class="text-gray-600 mt-2">Escaneie o QR Code abaixo para realizar o pagamento.</p>
            </div>
            
            <!-- Informações do Pedido -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Pedido #{{ order.id }}</h2>
                        <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="text-lg font-semibold text-indigo-600">{{ formatCurrency(order.total) }}</p>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <div class="text-sm text-gray-600">
                        <p>Método de envio: {{ order.shipping_method.name }}</p>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Mensagem de erro da sessão -->
                <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md">
                    <p>{{ $page.props.flash.error }}</p>
                </div>
                
                <!-- QR Code PIX -->
                <div class="mt-8">
                    <div class="bg-white rounded-lg p-6 border-2 border-dashed border-gray-200">
                        <div class="flex flex-col items-center">
                            <img v-if="pixData && pixData.qrcode_image" 
                                :src="pixData.qrcode_image" 
                                alt="QR Code PIX"
                                class="mx-auto w-48 h-48"
                                @error="handleImageError"
                            />
                            <div v-else-if="imageError || !pixData || !pixData.qrcode_image" class="w-48 h-48 mx-auto bg-gray-100 rounded-lg flex flex-col items-center justify-center p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="text-gray-500 text-sm text-center mb-2">{{ getErrorMessage() }}</span>
                                <div class="flex space-x-2">
                                    <button @click="reloadPage" class="px-3 py-1 bg-[#231F20] text-white text-xs rounded-md hover:bg-[#231F20]/80 transition-colors">
                                        Recarregar
                                    </button>
                                    <Link :href="route('cart.index')" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 transition-colors">
                                        Voltar ao Carrinho
                                    </Link>
                                </div>
                            </div>
                            
                            <div class="mt-6 w-full">
                                <div v-if="pixData && pixData.qrcode_text" class="mt-4">
                                    <p class="text-xs text-gray-500 mb-2">Ou copie o código PIX abaixo:</p>
                                    <div class="relative">
                                        <input type="text" 
                                            :value="pixData.qrcode_text" 
                                            readonly
                                            class="w-full px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-[#231F20] focus:border-[#231F20]"
                                        />
                                        <button @click="copyPixCode(pixData.qrcode_text)"
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
                    <h3 class="text-lg font-semibold mb-4">Como pagar com PIX</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                        <li>Abra o aplicativo do seu banco</li>
                        <li>Acesse a área do PIX</li>
                        <li>Escaneie o QR Code acima ou copie e cole o código</li>
                        <li>Confirme as informações e finalize o pagamento</li>
                        <li>Seu pedido será processado assim que o pagamento for confirmado</li>
                    </ol>
                </div>

                <!-- Tempo de Expiração -->
                <div v-if="pixData && pixData.expiration_date" class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Este QR Code expira em: {{ formatExpirationDate(pixData.expiration_date) }}
                    </p>
                </div>

                <!-- Botões de Ação -->
                <div class="mt-8 flex justify-center">
                    <Link :href="route('orders.index')" class="px-4 py-2 bg-[#231F20] text-white rounded-md hover:bg-[#231F20]/80 transition-colors">
                        Ver Meus Pedidos
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    order: Object,
    pixData: Object
})

const imageError = ref(false)
const paymentStatus = ref(props.order.payment?.status || 'pending')
const checkInterval = ref(null)

// Verifica o status do pagamento
const checkPaymentStatus = async () => {
    try {
        const response = await axios.get(route('orders.show', props.order.id))
        const order = response.data.order
        
        if (order.payment && order.payment.status === 'approved') {
            paymentStatus.value = 'approved'
            clearInterval(checkInterval.value)
            // Redireciona para a página de sucesso
            window.location.href = route('orders.success', props.order.id)
        }
    } catch (error) {
        console.error('Erro ao verificar status do pagamento:', error)
    }
}

// Inicia a verificação periódica do status
onMounted(() => {
    if (paymentStatus.value === 'pending') {
        checkInterval.value = setInterval(checkPaymentStatus, 5000) // Verifica a cada 5 segundos
    }
})

// Limpa o intervalo quando o componente é desmontado
onUnmounted(() => {
    if (checkInterval.value) {
        clearInterval(checkInterval.value)
    }
})

// Formata o valor em reais
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}

// Formata a data
const formatDate = (dateString) => {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date)
}

// Formata a data de expiração
const formatExpirationDate = (dateString) => {
    if (!dateString) return ''
    
    const expirationDate = new Date(dateString)
    const now = new Date()
    
    // Calcula a diferença em minutos
    const diffMs = expirationDate - now
    const diffMins = Math.round(diffMs / 60000)
    
    if (diffMins <= 0) {
        return 'Expirado'
    }
    
    const hours = Math.floor(diffMins / 60)
    const minutes = diffMins % 60
    
    if (hours > 0) {
        return `${hours}h ${minutes}min`
    }
    
    return `${minutes} minutos`
}

// Verifica se o QR Code expirou
const isQrCodeExpired = () => {
    if (!props.pixData || !props.pixData.expiration_date) return false
    
    const expirationDate = new Date(props.pixData.expiration_date)
    const now = new Date()
    
    return expirationDate < now
}

// Obtém a mensagem de erro
const getErrorMessage = () => {
    if (imageError.value) {
        return 'Não foi possível carregar o QR Code.'
    }
    
    if (isQrCodeExpired()) {
        return 'O QR Code expirou. Por favor, volte ao carrinho e tente novamente.'
    }
    
    if ($page.props.flash && $page.props.flash.error) {
        return $page.props.flash.error
    }
    
    return 'Erro ao carregar o QR Code. Por favor, tente novamente.'
}

// Copia o código PIX para a área de transferência
const copyPixCode = async (code) => {
    try {
        await navigator.clipboard.writeText(code)
        alert('Código PIX copiado com sucesso!')
    } catch (err) {
        alert('Erro ao copiar código PIX')
    }
}

// Recarrega a página
const reloadPage = () => {
    window.location.reload()
}

// Trata o erro ao carregar a imagem do QR Code
const handleImageError = () => {
    imageError.value = true
}
</script>
