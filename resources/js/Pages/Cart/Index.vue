<template>
    <ProductShowcaseLayout :auth="auth">
        <div class="animate-fade-in-up">
            <!-- Cabeçalho -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold text-[#231F20]">
                    Meu Carrinho
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    {{ cartItems.length }} {{ cartItems.length === 1 ? 'item' : 'itens' }} no carrinho
                </p>
            </div>

            <div class="mt-6 space-y-6">
                <!-- Lista de Produtos -->
                <div v-if="cartItems.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-100 divide-y divide-gray-100">
                    <TransitionGroup
                        enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="opacity-0 -translate-x-4"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition-all duration-200 ease-in"
                        leave-from-class="opacity-100 translate-x-0"
                        leave-to-class="opacity-0 translate-x-4"
                    >
                        <div v-for="item in cartItems" :key="item.id" class="p-4 sm:p-6">
                            <div class="flex gap-4">
                                <!-- Imagem do Produto -->
                                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                    <img :src="`/storage/${item.product.featured_image}`" :alt="item.product.name"
                                        class="w-full h-full object-cover">
                                </div>

                                <!-- Informações do Produto -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-sm sm:text-base font-medium text-[#231F20] line-clamp-2">
                                                {{ item.product.name }}
                                            </h3>
                                            <p class="mt-1 text-xs text-[#231F20]/70">
                                                {{ item.product.department.name }}
                                            </p>
                                        </div>
                                        <button @click="removeItem(item)" 
                                            class="text-red-600 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50"
                                            :disabled="removingItem[item.id]"
                                        >
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>

                                    <!-- Preço e Controles -->
                                    <div class="mt-4 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="inline-flex items-center rounded-full border border-gray-200 bg-white">
                                                <button @click="updateQuantity(item, item.quantity - 1)"
                                                    :disabled="item.quantity <= 1 || updatingQuantity[item.id]"
                                                    class="p-2 text-[#231F20] hover:text-[#231F20]/70 disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                                                >
                                                    <MinusIcon class="w-4 h-4" />
                                                </button>
                                                <span class="w-8 text-center text-sm">{{ item.quantity }}</span>
                                                <button @click="updateQuantity(item, item.quantity + 1)"
                                                    :disabled="updatingQuantity[item.id]"
                                                    class="p-2 text-[#231F20] hover:text-[#231F20]/70 disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                                                >
                                                    <PlusIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                × {{ formatCurrency(item.price) }}
                                            </span>
                                        </div>
                                        <p class="text-lg font-semibold text-[#231F20]">
                                            {{ formatCurrency(item.quantity * item.price) }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Informação do Revendedor (se aplicável) -->
                                <div v-if="item.reseller" class="mt-2 text-xs text-indigo-600 flex items-center">
                                    <span class="inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        Vendido por: {{ item.reseller.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>

                <!-- Carrinho Vazio -->
                <div v-else class="bg-white rounded-lg p-8 shadow-sm border border-gray-100 text-center">
                    <ShoppingCartIcon class="w-16 h-16 mx-auto text-gray-400" />
                    <h2 class="mt-4 text-lg font-medium text-[#231F20]">Seu carrinho está vazio</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Adicione produtos ao seu carrinho para continuar comprando.
                    </p>
                    <Link :href="route('products')"
                        class="mt-6 inline-flex items-center justify-center px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors"
                    >
                        Continuar Comprando
                    </Link>
                </div>

                <!-- Opções de Frete -->
                <div v-if="cartItems.length > 0" class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-medium text-[#231F20]">Opções de Frete</h2>
                    
                    <div class="mt-4 space-y-3">
                        <div v-for="(method, index) in shippingMethods" :key="method.id" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-gray-300">
                            <input
                                type="radio"
                                :id="`shipping-${method.id}`"
                                :value="method"
                                v-model="selectedShipping"
                                class="h-4 w-4 text-[#231F20] focus:ring-[#231F20] border-gray-300"
                                :checked="index === 0"
                            />
                            <label :for="`shipping-${method.id}`" class="ml-3 flex flex-1 justify-between cursor-pointer">
                                <div>
                                    <p class="text-sm font-medium text-[#231F20]">{{ method.name }}</p>
                                    <p class="text-xs text-gray-500">{{ method.description }}</p>
                                </div>
                                <p class="text-sm font-semibold text-[#231F20]">
                                    {{ formatCurrency(method.value) }}
                                </p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Opções de Pagamento -->
                <div v-if="cartItems.length > 0" class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-medium text-[#231F20]">Forma de Pagamento</h2>
                    
                    <div class="mt-4 space-y-4">
                        <!-- Seleção do método de pagamento -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div 
                                class="flex-1 p-4 rounded-lg border cursor-pointer transition-all duration-200"
                                :class="selectedPaymentMethod === 'credit-card' ? 'border-[#231F20] bg-[#231F20]/5' : 'border-gray-200 hover:border-gray-300'"
                                @click="togglePaymentMethod('credit-card')"
                            >
                                <div class="flex items-center">
                                    <input 
                                        type="radio" 
                                        id="payment-credit-card" 
                                        :checked="selectedPaymentMethod === 'credit-card'"
                                        class="h-4 w-4 text-[#231F20] focus:ring-[#231F20] border-gray-300"
                                    />
                                    <label for="payment-credit-card" class="ml-3 cursor-pointer">
                                        <div class="flex items-center gap-2">
                                            <CreditCardIcon class="w-5 h-5 text-[#231F20]" />
                                            <span class="text-sm font-medium text-[#231F20]">Cartão de Crédito</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div 
                                class="flex-1 p-4 rounded-lg border cursor-pointer transition-all duration-200"
                                :class="selectedPaymentMethod === 'pix' ? 'border-[#231F20] bg-[#231F20]/5' : 'border-gray-200 hover:border-gray-300'"
                                @click="togglePaymentMethod('pix')"
                            >
                                <div class="flex items-center">
                                    <input 
                                        type="radio" 
                                        id="payment-pix" 
                                        :checked="selectedPaymentMethod === 'pix'"
                                        class="h-4 w-4 text-[#231F20] focus:ring-[#231F20] border-gray-300"
                                    />
                                    <label for="payment-pix" class="ml-3 cursor-pointer">
                                        <div class="flex items-center gap-2">
                                            <QrCodeIcon class="w-5 h-5 text-[#231F20]" />
                                            <span class="text-sm font-medium text-[#231F20]">PIX</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulário de Cartão de Crédito -->
                        <div v-if="selectedPaymentMethod === 'credit-card'" class="mt-4 animate-fade-in">
                            <div class="space-y-4">
                                <!-- Cartão de Crédito Estilizado -->
                                <div class="relative bg-gradient-to-r from-gray-900 to-gray-800 p-5 rounded-xl shadow-lg mb-4 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                                    <div class="absolute top-0 right-0 w-full h-full opacity-10">
                                        <div class="absolute top-0 left-0 w-20 h-20 bg-white rounded-full -ml-10 -mt-10"></div>
                                        <div class="absolute bottom-0 right-0 w-20 h-20 bg-white rounded-full -mr-10 -mb-10"></div>
                                    </div>
                                    
                                    <div class="relative z-10">
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="text-white text-xs uppercase tracking-wider">Cartão de Crédito</div>
                                            <div class="flex space-x-2">
                                                <div class="h-6 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded opacity-80"></div>
                                                <div class="h-6 w-10 bg-gradient-to-br from-yellow-400 to-red-600 rounded opacity-80"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-white font-mono text-lg tracking-wider mb-4 flex justify-center">
                                            {{ formattedCardNumber }}
                                        </div>
                                        
                                        <div class="flex justify-between">
                                            <div class="text-white font-mono uppercase text-sm tracking-wider">
                                                {{ formattedCardHolder }}
                                            </div>
                                            <div class="text-white font-mono text-sm tracking-wider">
                                                {{ formattedCardExpiry }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="card-number" class="block text-sm font-medium text-gray-700">Número do Cartão</label>
                                    <input 
                                        type="text" 
                                        id="card-number" 
                                        v-model="cardNumber"
                                        @input="formatCardNumberInput"
                                        placeholder="0000 0000 0000 0000"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#231F20] focus:ring-[#231F20] sm:text-sm transition-all duration-200"
                                    />
                                </div>
                                
                                <div>
                                    <label for="card-holder" class="block text-sm font-medium text-gray-700">Nome no Cartão</label>
                                    <input 
                                        type="text" 
                                        id="card-holder" 
                                        v-model="cardHolder"
                                        placeholder="Nome como está no cartão"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#231F20] focus:ring-[#231F20] sm:text-sm transition-all duration-200"
                                    />
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="card-expiry" class="block text-sm font-medium text-gray-700">Validade (MM/AA)</label>
                                        <div class="flex gap-2">
                                            <input 
                                                type="text" 
                                                id="card-expiry-month" 
                                                v-model="cardExpiryMonth"
                                                placeholder="MM"
                                                maxlength="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#231F20] focus:ring-[#231F20] sm:text-sm transition-all duration-200"
                                            />
                                            <span class="mt-1 text-gray-500">/</span>
                                            <input 
                                                type="text" 
                                                id="card-expiry-year" 
                                                v-model="cardExpiryYear"
                                                placeholder="AA"
                                                maxlength="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#231F20] focus:ring-[#231F20] sm:text-sm transition-all duration-200"
                                            />
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="card-cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                                        <input 
                                            type="text" 
                                            id="card-cvv" 
                                            v-model="cardCvv"
                                            placeholder="123"
                                            maxlength="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#231F20] focus:ring-[#231F20] sm:text-sm transition-all duration-200"
                                        />
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="card-installments" class="block text-sm font-medium text-gray-700">Parcelas</label>
                                    <select 
                                        id="card-installments" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#231F20] focus:ring-[#231F20] sm:text-sm transition-all duration-200"
                                    >
                                        <option v-for="i in 12" :key="i" :value="i">
                                            {{ i }}x {{ i === 1 ? 'de ' + formatCurrency(totalWithShipping) + ' sem juros' : 'de ' + formatCurrency(totalWithShipping / i) }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulário de PIX -->
                        <div v-if="selectedPaymentMethod === 'pix'" class="mt-4 animate-fade-in">
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-48 h-48 bg-white p-2 rounded-lg shadow-sm border border-gray-300 flex items-center justify-center mb-4">
                                            <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                                <QrCodeIcon class="w-24 h-24 text-gray-500" />
                                            </div>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 text-center mb-4">
                                            Ao finalizar a compra, você receberá um QR Code PIX para pagamento.
                                        </p>
                                        
                                        <div class="w-full bg-white p-3 rounded-lg border border-gray-300 flex items-center justify-between">
                                            <span class="text-sm text-gray-500 truncate">Código PIX será gerado após finalizar</span>
                                            <button class="text-xs text-indigo-600 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                                Copiar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                    <div class="flex">
                                        <ExclamationTriangleIcon class="w-5 h-5 text-yellow-500 mt-0.5 flex-shrink-0" />
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Informações sobre o PIX</h3>
                                            <div class="mt-2 text-sm text-yellow-700 space-y-1">
                                                <p>• O pagamento via PIX é processado instantaneamente.</p>
                                                <p>• Você terá 30 minutos para realizar o pagamento.</p>
                                                <p>• Após o pagamento, seu pedido será processado automaticamente.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Endereço de Entrega -->
                <div v-if="cartItems.length > 0" class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-medium text-[#231F20]">Endereço de Entrega</h2>
                        <Link 
                            href="/minha-conta" 
                            class="text-xs text-indigo-600 hover:text-indigo-800 transition-colors"
                        >
                            Alterar
                        </Link>
                    </div>
                    
                    <div v-if="auth.customer" class="mt-3 bg-gray-50 p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 text-sm">
                                <p class="font-medium text-gray-800">{{ props.auth.customer.name }}</p>
                                <p class="mt-1 text-gray-600">{{ props.auth.customer.street }}, {{ props.auth.customer.number }}</p>
                                <p v-if="props.auth.customer.complement" class="text-gray-600">{{ props.auth.customer.complement }}</p>
                                <p class="text-gray-600">{{ props.auth.customer.neighborhood }}</p>
                                <p class="text-gray-600">{{ props.auth.customer.city }} - {{ props.auth.customer.state }}</p>
                                <p class="text-gray-600">CEP: {{ props.auth.customer.cep }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="mt-3 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-start">
                            <ExclamationTriangleIcon class="w-5 h-5 text-yellow-500 mt-0.5 flex-shrink-0" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Endereço não encontrado</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Para finalizar sua compra, é necessário cadastrar um endereço de entrega.</p>
                                    <Link 
                                        href="/minha-conta" 
                                        class="mt-2 inline-block text-yellow-800 font-medium hover:text-yellow-900 underline"
                                    >
                                        Cadastrar meu endereço
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumo do Pedido -->
                <div v-if="cartItems.length > 0" class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-medium text-[#231F20]">Resumo do Pedido</h2>
                    <div class="mt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-[#231F20]">{{ formatCurrency(total) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Frete</span>
                            <span class="font-medium text-[#231F20]">{{ selectedShipping ? formatCurrency(selectedShipping.value) : 'Grátis' }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex justify-between items-baseline">
                                <span class="text-base font-medium text-[#231F20]">Total</span>
                                <span class="text-2xl font-bold text-[#231F20]">{{ formatCurrency(totalWithShipping) }}</span>
                            </div>
                        </div>
                        
                        <!-- Informação do Revendedor (se aplicável) -->
                        <div v-if="resellerInfo" class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span>Compra através de: {{ resellerInfo.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button 
                        class="mt-6 w-full px-6 py-3 rounded-full text-sm font-medium
                        transform transition-all duration-200 hover:scale-[1.02] active:scale-100
                        flex items-center justify-center gap-2"
                        :class="[
                            selectedShipping && auth.customer && auth.customer.street ? 
                                'bg-[#231F20] text-white hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20]' : 
                                'bg-gray-300 text-gray-500 cursor-not-allowed'
                        ]"
                        :disabled="!selectedShipping || !auth.customer || !auth.customer.street"
                        @click="processPayment"
                    >
                        <span v-if="!auth.customer || !auth.customer.street">Cadastre um endereço de entrega</span>
                        <span v-else-if="!selectedShipping">Selecione um método de frete</span>
                        <span v-else>Finalizar Compra com {{ selectedPaymentMethod === 'credit-card' ? 'Cartão de Crédito' : 'PIX' }}</span>
                        <ArrowRightIcon class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'
import { 
    ShoppingCartIcon,
    TrashIcon,
    MinusIcon,
    PlusIcon,
    ArrowRightIcon,
    CreditCardIcon,
    QrCodeIcon,
    HomeIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
    auth: Object,
    cartItems: Array,
    total: Number,
    shippingMethods: Array,
    selectedShippingMethod: Object,
    resellerInfo: Object
})

// Estados de loading
const updatingQuantity = ref({})
const removingItem = ref({})

// Estados do cartão de crédito
const cardNumber = ref('')
const cardHolder = ref('')
const cardExpiryMonth = ref('')
const cardExpiryYear = ref('')
const cardCvv = ref('')
const selectedPaymentMethod = ref('credit-card') // 'credit-card' ou 'pix'
const selectedShipping = ref(props.selectedShippingMethod || props.shippingMethods[0])

// Calcula o valor total com frete
const totalWithShipping = computed(() => {
    let shippingValue = 0
    if (selectedShipping.value) {
        shippingValue = parseFloat(selectedShipping.value.value)
    }
    return props.total + shippingValue
})

// Seleciona um método de frete
const selectShippingMethod = (method) => {
    selectedShipping.value = method
}

// Formata o número do cartão para exibição
const formattedCardNumber = computed(() => {
    if (!cardNumber.value) return '•••• •••• •••• ••••'
    
    // Remove todos os espaços e caracteres não numéricos
    const digitsOnly = cardNumber.value.replace(/\D/g, '')
    
    // Adiciona espaços a cada 4 dígitos
    let formatted = ''
    for (let i = 0; i < 16; i += 4) {
        const chunk = digitsOnly.slice(i, i + 4)
        if (chunk) {
            formatted += chunk + ' '
        } else {
            formatted += '•••• '
        }
    }
    
    return formatted.trim()
})

// Formata o nome do titular para exibição
const formattedCardHolder = computed(() => {
    return cardHolder.value ? cardHolder.value.toUpperCase() : 'NOME DO TITULAR'
})

// Formata a data de expiração para exibição
const formattedCardExpiry = computed(() => {
    if (cardExpiryMonth.value && cardExpiryYear.value) {
        return `${cardExpiryMonth.value}/${cardExpiryYear.value}`
    }
    return 'MM/AA'
})

// Formata o número do cartão enquanto o usuário digita
const formatCardNumberInput = (e) => {
    let value = e.target.value.replace(/\D/g, '')
    if (value.length > 16) value = value.slice(0, 16)
    
    // Adiciona espaços a cada 4 dígitos para melhor visualização
    let formatted = ''
    for (let i = 0; i < value.length; i += 4) {
        formatted += value.slice(i, i + 4) + ' '
    }
    
    cardNumber.value = formatted.trim()
}

// Alterna entre métodos de pagamento
const togglePaymentMethod = (method) => {
    selectedPaymentMethod.value = method
}

// Atualiza a quantidade de um item
const updateQuantity = async (item, newQuantity) => {
    if (newQuantity < 1 || updatingQuantity.value[item.id]) return
    
    updatingQuantity.value[item.id] = true
    
    try {
        await axios.put(route('cart.update.quantity', { cartItem: item.id }), {
            quantity: newQuantity
        })
        
        // Recarrega a página para atualizar os totais
        window.location.reload()
    } catch (error) {
        console.error('Erro ao atualizar quantidade:', error)
    } finally {
        updatingQuantity.value[item.id] = false
    }
}

// Remove um item do carrinho
const removeItem = async (item) => {
    if (removingItem.value[item.id]) return
    
    removingItem.value[item.id] = true
    
    try {
        await axios.delete(route('cart.remove', { cartItem: item.id }))
        
        // Recarrega a página para atualizar os totais
        window.location.reload()
    } catch (error) {
        console.error('Erro ao remover item:', error)
    } finally {
        removingItem.value[item.id] = false
    }
}

// Processa o pagamento
const processPayment = async () => {
    try {
        // Verifica se todos os campos necessários estão preenchidos para cartão de crédito
        if (selectedPaymentMethod.value === 'credit-card') {
            if (!cardNumber.value || !cardHolder.value || !cardExpiryMonth.value || !cardExpiryYear.value || !cardCvv.value) {
                alert('Por favor, preencha todos os campos do cartão de crédito');
                return;
            }
        }

        // Verifica se o método de frete foi selecionado
        if (!selectedShipping.value || !selectedShipping.value.id) {
            alert('Por favor, selecione um método de frete');
            return;
        }

        // Verifica se tem endereço completo
        const customer = props.auth.customer;
        if (!customer || 
            !customer.street || 
            !customer.number || 
            !customer.city || 
            !customer.state || 
            !customer.cep) {
            console.log('Campos faltando:', {
                street: !customer.street,
                number: !customer.number,
                city: !customer.city,
                state: !customer.state,
                cep: !customer.cep
            });
            alert('Por favor, complete seu endereço antes de continuar');
            return;
        }
        
        // Prepara os dados do pagamento
        const paymentData = {
            shipping_method_id: selectedShipping.value.id,
            address: customer.street + ', ' + customer.number + (customer.complement ? ' - ' + customer.complement : ''),
            city: customer.city,
            state: customer.state,
            zip_code: customer.cep.replace(/\D/g, '') // Enviando como zip_code para o backend
        };

        console.log('Dados do pagamento:', paymentData);
        
        // Adiciona os dados do cartão se for pagamento com cartão de crédito
        if (selectedPaymentMethod.value === 'credit-card') {
            Object.assign(paymentData, {
                card_number: cardNumber.value.replace(/\s/g, ''),
                card_holder_name: cardHolder.value,
                card_expiration_month: cardExpiryMonth.value,
                card_expiration_year: cardExpiryYear.value,
                card_security_code: cardCvv.value,
                installments: 1 // Por padrão, pagamento à vista
            });
        }
        
        // Envia a requisição para o endpoint apropriado
        const endpoint = selectedPaymentMethod.value === 'credit-card' 
            ? '/orders/process-credit-card' 
            : '/orders/process-pix';
            
        try {
            const response = await axios.post(endpoint, paymentData);
            
            // Verifica se a resposta foi bem sucedida
            if (response.data.success) {
                // Se houver URL de redirecionamento, redireciona
                if (response.data.redirect) {
                    window.location.href = response.data.redirect;
                } else {
                    // Se não houver URL de redirecionamento, redireciona para a lista de pedidos
                    window.location.href = route('orders.index');
                }
            } else {
                // Se houver URL de redirecionamento para página de erro, redireciona
                if (response.data.redirect) {
                    window.location.href = response.data.redirect;
                } else {
                    // Se não houver URL de redirecionamento, exibe a mensagem de erro
                    if (response.data.message) {
                        alert(response.data.message);
                    } else {
                        alert('Erro ao processar o pagamento. Por favor, tente novamente.');
                    }
                }
            }
        } catch (error) {
            console.error('Erro ao processar pagamento:', error);
            alert('Erro ao processar o pagamento. Por favor, tente novamente.');
        }
    } catch (error) {
        console.error('Erro ao processar pagamento:', error);
        alert('Erro ao processar o pagamento. Por favor, tente novamente.');
    }
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
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
