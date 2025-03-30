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
                            <img v-if="qrCodeImage" 
                                :src="qrCodeImage" 
                                alt="QR Code PIX"
                                class="mx-auto w-48 h-48"
                                @error="handleImageError"
                            />
                            <div v-else-if="imageError || !qrCodeImage" class="w-48 h-48 mx-auto bg-gray-100 rounded-lg flex flex-col items-center justify-center p-2">
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
                                <div v-if="qrCodeText" class="mt-4">
                                    <p class="text-xs text-gray-500 mb-2">Ou copie o código PIX abaixo:</p>
                                    <div class="relative">
                                        <input type="text" 
                                            :value="qrCodeText" 
                                            readonly
                                            class="w-full px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-[#231F20] focus:border-[#231F20]"
                                        />
                                        <button @click="copyPixCode(qrCodeText)"
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
                <div v-if="expirationDate" class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Este QR Code expira em: {{ formatExpirationDate(expirationDate) }}
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
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    payment: {
        type: Object,
        required: true
    },
    qrCodeData: {
        type: Object,
        required: true
    }
})

const page = usePage()
const imageError = ref(false)
const qrCodeText = ref(props.qrCodeData.qrcode_text)
const qrCodeImage = ref(props.qrCodeData.qrcode_image)
const expirationDate = ref(props.qrCodeData.expiration_date)
const transactionId = ref(props.qrCodeData.transaction_id)
const links = ref(props.qrCodeData.links || [])

// Função para verificar o status do pagamento
const checkPaymentStatus = async () => {
    try {
        if (!links.value || !links.value.length) {
            console.error('Links não encontrados para verificação do status')
            return
        }

        const statusLink = links.value.find(link => link.rel === 'SELF')
        if (!statusLink) {
            console.error('Link de status não encontrado')
            return
        }

        const response = await axios.get(statusLink.href, {
            headers: {
                'Authorization': 'Bearer ' + process.env.PAGSEGURO_TOKEN
            }
        })

        if (response.data.charges && response.data.charges.length > 0) {
            const charge = response.data.charges[0]
            if (charge.status === 'PAID') {
                // Atualiza o status do pagamento
                await axios.post(`/api/orders/${props.order.id}/payments/${props.payment.id}/status`, {
                    status: 'approved'
                })

                // Redireciona para a página de sucesso
                router.visit(route('orders.show', props.order.id))
            }
        }
    } catch (error) {
        console.error('Erro ao verificar status do pagamento:', error)
    }
}

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
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date)
}

// Verifica se o QR Code expirou
const isQrCodeExpired = () => {
    if (!expirationDate.value) return false
    
    const expirationDate = new Date(expirationDate.value)
    const now = new Date()
    
    return expirationDate < now
}

// Retorna a mensagem de erro apropriada
const getErrorMessage = () => {
    if (imageError.value) {
        return 'Erro ao carregar o QR Code. Tente recarregar a página.'
    }
    if (!qrCodeImage.value) {
        return 'Erro ao gerar o QR Code. Tente novamente.'
    }
    return 'Erro desconhecido. Tente novamente.'
}

// Recarrega a página
const reloadPage = () => {
    window.location.reload()
}

// Copia o código PIX para a área de transferência
const copyPixCode = (code) => {
    navigator.clipboard.writeText(code).then(() => {
        // Aqui você pode adicionar uma notificação de sucesso se desejar
    }).catch(err => {
        console.error('Erro ao copiar código:', err)
    })
}

// Trata o erro ao carregar a imagem do QR Code
const handleImageError = () => {
    imageError.value = true
}
</script>
