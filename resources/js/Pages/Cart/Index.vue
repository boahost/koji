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
                            <span class="font-medium text-[#231F20]">Grátis</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex justify-between items-baseline">
                                <span class="text-base font-medium text-[#231F20]">Total</span>
                                <span class="text-2xl font-bold text-[#231F20]">{{ formatCurrency(total) }}</span>
                            </div>
                        </div>
                    </div>

                    <button class="mt-6 w-full bg-[#231F20] text-white px-6 py-3 rounded-full text-sm font-medium
                        hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20]
                        transform transition-all duration-200 hover:scale-[1.02] active:scale-100
                        flex items-center justify-center gap-2"
                    >
                        <span>Finalizar Compra</span>
                        <ArrowRightIcon class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'
import { 
    ShoppingCartIcon,
    TrashIcon,
    MinusIcon,
    PlusIcon,
    ArrowRightIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
    auth: Object,
    cartItems: Array,
    total: Number
})

// Estados de loading
const updatingQuantity = ref({})
const removingItem = ref({})

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

// Formata o preço em reais
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}
</script>
