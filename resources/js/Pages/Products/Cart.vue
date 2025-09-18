<template>
    <ProductShowcaseLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-6">Carrinho de Compras</h1>
            
            <div v-if="cartItems.length === 0" class="text-center py-12">
                <p class="text-gray-500 mb-4">Seu carrinho está vazio</p>
                <Link
                    :href="route('customer.dashboard')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#231F20] hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20]"
                >
                    Continuar Comprando
                </Link>
            </div>

            <div v-else class="space-y-8">
                <!-- Lista de Produtos -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="item in cartItems" :key="item.id" class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center gap-4 sm:gap-6">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0">
                                    <img 
                                        :src="item.image" 
                                        :alt="item.name" 
                                        class="w-full h-full object-cover rounded-lg"
                                    >
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate">{{ item.name }}</h3>
                                            <p class="mt-1 text-xs text-indigo-600">{{ item.department }}</p>
                                        </div>
                                        <p class="text-sm sm:text-base font-semibold text-indigo-600">R$ {{ item.price }}</p>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center gap-3 bg-gray-100 rounded-full px-2 py-1">
                                            <button 
                                                class="p-1 rounded-full hover:bg-white transition-colors duration-200"
                                                @click="updateQuantity(item.id, item.quantity - 1)"
                                                :disabled="item.quantity <= 1"
                                                :class="{ 'opacity-50 cursor-not-allowed': item.quantity <= 1 }"
                                            >
                                                <MinusIcon class="w-4 h-4 text-gray-600" />
                                            </button>
                                            <span class="text-sm font-medium text-gray-900 min-w-[20px] text-center">{{ item.quantity }}</span>
                                            <button 
                                                class="p-1 rounded-full hover:bg-white transition-colors duration-200"
                                                @click="updateQuantity(item.id, item.quantity + 1)"
                                            >
                                                <PlusIcon class="w-4 h-4 text-gray-600" />
                                            </button>
                                        </div>
                                        <button 
                                            class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors duration-200"
                                            @click="removeItem(item.id)"
                                        >
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Resumo do Pedido -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-baseline">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-lg font-semibold text-gray-900">R$ {{ subtotal }}</span>
                            </div>
                            <div class="flex justify-between items-baseline">
                                <span class="text-gray-600">Frete</span>
                                <span class="text-sm text-gray-500">Calculado no checkout</span>
                            </div>
                            <div class="border-t border-gray-200 pt-4 flex justify-between items-baseline">
                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                <span class="text-xl font-semibold text-indigo-600">R$ {{ total }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-6">
                        <button 
                            class="w-full bg-[#231F20] text-white py-3 px-4 rounded-full font-medium hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors duration-200"
                        >
                            Finalizar Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'
import { MinusIcon, PlusIcon } from '@heroicons/vue/24/outline'

// Mock data - substitua por dados reais da sua API
const cartItems = ref([
    {
        id: 1,
        name: 'Produto Exemplo',
        department: 'Departamento Exemplo',
        price: '99,90',
        image: '/product-placeholder.jpg',
        quantity: 1
    }
])

const subtotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
        return total + (parseFloat(item.price.replace(',', '.')) * item.quantity)
    }, 0).toFixed(2).replace('.', ',')
})

const total = computed(() => subtotal.value)

const updateQuantity = (itemId, newQuantity) => {
    if (newQuantity < 1) return
    const item = cartItems.value.find(item => item.id === itemId)
    if (item) {
        item.quantity = newQuantity
    }
}

const removeItem = (itemId) => {
    cartItems.value = cartItems.value.filter(item => item.id !== itemId)
}
</script>
