<template>
    <ProductShowcaseLayout :auth="auth" @toggle-filters="toggleFilters">
        <template #default>
        <!-- Container Principal -->
        <div class="relative">
            <!-- Overlay para Mobile -->
            <Transition
                enter-active-class="transition-opacity duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div 
                    v-if="showFilters" 
                    class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"
                    @click="toggleFilters"
                ></div>
            </Transition>

            <!-- Container de Conteúdo -->
            <div class="flex gap-6">
                <!-- Filtros Laterais -->
                <Transition
                    enter-active-class="transform transition-all duration-300 ease-out"
                    enter-from-class="-translate-x-full opacity-0"
                    enter-to-class="translate-x-0 opacity-100"
                    leave-active-class="transform transition-all duration-300 ease-in"
                    leave-from-class="translate-x-0 opacity-100"
                    leave-to-class="-translate-x-full opacity-0"
                >
                    <div
                        v-show="showFilters || !screen.mobile"
                        :class="[
                            'w-72 flex-shrink-0',
                            'fixed lg:static top-0 left-0 bottom-0 z-50 lg:z-0',
                            'bg-white lg:bg-transparent shadow-lg lg:shadow-none',
                            'transition-transform duration-300 ease-in-out',
                            showFilters ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
                        ]"
                    >
                        <div class="h-full overflow-y-auto bg-gradient-to-b from-gray-50 to-white lg:rounded-lg lg:border lg:border-gray-100 lg:shadow-sm">
                            <div class="sticky top-0 bg-white border-b border-gray-100 px-4 py-3 lg:hidden">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-semibold text-[#231F20]">Filtros</h2>
                                    <button 
                                        @click="toggleFilters"
                                        class="p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200"
                                    >
                                        <XMarkIcon class="w-5 h-5 text-gray-500" />
                                    </button>
                                </div>
                            </div>


                            <!-- Conteúdo dos Filtros -->
                            <div class="px-4 py-6 space-y-8">
                                <!-- Título dos Filtros (Desktop) -->
                                <div class="hidden lg:block mb-6">
                                    <h2 class="text-lg font-semibold text-[#231F20]">Filtros</h2>
                                </div>
                                
                                <!-- Separador Desktop -->
                                <div class="hidden lg:block border-t border-gray-200 mb-6"></div>
                        
                                <!-- Departamentos -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-medium text-[#231F20]">Departamentos</h3>
                                    <div class="space-y-2.5">
                                        <label v-for="dept in departments" :key="dept.id" class="flex items-center">
                                            <input
                                                type="radio"
                                                name="department"
                                                :value="dept.id"
                                                v-model="filters.department"
                                                class="h-4 w-4 text-[#231F20] focus:ring-[#231F20] border-gray-300 rounded"
                                                @change="(e: Event) => applyFilters()"
                                            >
                                            <span class="ml-2 text-sm text-gray-600 hover:text-[#231F20] transition-colors">{{ dept.name }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Categorias -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-medium text-[#231F20]">Categorias</h3>
                                    <div class="space-y-2.5 max-h-48 overflow-y-auto custom-scrollbar pr-2">
                                        <label v-for="cat in filteredCategories" :key="cat.id" class="flex items-center">
                                            <input
                                                type="radio"
                                                name="category"
                                                :value="cat.id"
                                                v-model="filters.category"
                                                class="h-4 w-4 text-[#231F20] focus:ring-[#231F20] border-gray-300 rounded"
                                                @change="(e: Event) => applyFilters()"
                                            >
                                            <span class="ml-2 text-sm text-gray-600 hover:text-[#231F20] transition-colors">{{ cat.name }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Ordenação -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-medium text-[#231F20]">Ordenar por</h3>
                                    <select
                                        v-model="sortBy"
                                        @change="applySort"
                                        class="block w-full px-3 py-2 text-sm border-gray-300 focus:outline-none focus:ring-[#231F20] focus:border-[#231F20] rounded-lg transition-colors"
                                    >
                                        <option value="newest">Mais Recentes</option>
                                        <option value="price_asc">Menor Preço</option>
                                        <option value="price_desc">Maior Preço</option>
                                        <option value="name">Nome (A-Z)</option>
                                    </select>
                                </div>

                                <!-- Separador -->
                                <div class="border-t border-gray-200"></div>

                                <!-- Limpar Filtros -->
                                <button
                                    @click="clearFilters"
                                    class="w-full px-4 py-2.5 text-sm font-medium text-white bg-[#231F20] rounded-lg hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-[#231F20]/20 transition-all duration-200"
                                >
                                    Limpar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Lista de Produtos -->
                <div class="flex-1 px-4 sm:px-0">
                    <!-- Grid de Produtos -->
                    <div 
                        class="grid gap-2 sm:gap-4 lg:gap-6"
                        :class="[
                            'grid-cols-2',
                            'sm:grid-cols-2',
                            'lg:grid-cols-3',
                            'xl:grid-cols-4'
                        ]"
                    >
                        <div
                            v-for="product in products.data"
                            :key="product.id"
                            class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden animate-fade-in-up relative flex flex-col h-full"
                        >
                            <!-- Tag de Departamento -->
                            <div class="absolute top-3 left-3 z-10">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-white/90 text-[#231F20] backdrop-blur-sm tracking-wide uppercase truncate">
                                    {{ product.department.name }}
                                </span>
                            </div>

                            <!-- Imagem do Produto -->
                            <div class="relative aspect-square bg-gray-100 group-hover:bg-gray-50 transition-all duration-300 overflow-hidden">
                                <img
                                    :src="`storage/` + product.featured_image"
                                    :alt="product.name"
                                    class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500 ease-out"
                                    loading="lazy"
                                />
                                <!-- Overlay com efeito de gradiente -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>

                            <!-- Informações do Produto -->
                            <div class="p-2 sm:p-4 flex-1 flex flex-col">
                                <div class="flex-1 space-y-1 sm:space-y-1.5">
                                    <!-- Nome do Produto -->
                                    <h3 class="text-xs sm:text-sm font-medium text-gray-900 hover:text-[#231F20] transition-colors line-clamp-2">
                                        {{ product.name }}
                                    </h3>

                                    <!-- Preço -->
                                    <p class="text-sm sm:text-lg font-semibold text-[#231F20]">
                                        {{ formatCurrency(product.price) }}
                                    </p>
                                </div>

                                <!-- Botões de Ação -->
                                <div class="mt-2 sm:mt-3">
                                    <!-- Botão Adicionar ao Carrinho -->
                                    <button
                                        @click="addToCart(product)"
                                        class="w-full px-2 sm:px-4 py-1.5 sm:py-2 bg-[#231F20] text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-[#231F20]/20 transition-all duration-200 transform hover:scale-[1.02] active:scale-100 disabled:opacity-75 disabled:cursor-not-allowed"
                                        :disabled="addingToCart[product.id]"
                                    >
                                        <div class="flex items-center justify-center gap-2">
                                            <ShoppingCartIcon class="w-4 h-4" :class="{ 'opacity-0': addingToCart[product.id] }" />
                                            <span :class="{ 'opacity-0': addingToCart[product.id] }">Adicionar</span>
                                            <svg 
                                                v-if="addingToCart[product.id]"
                                                class="absolute animate-spin h-5 w-5" 
                                                xmlns="http://www.w3.org/2000/svg" 
                                                fill="none" 
                                                viewBox="0 0 24 24"
                                            >
                                                <circle 
                                                    class="opacity-25" 
                                                    cx="12" 
                                                    cy="12" 
                                                    r="10" 
                                                    stroke="currentColor" 
                                                    stroke-width="4"
                                                />
                                                <path 
                                                    class="opacity-75" 
                                                    fill="currentColor" 
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                />
                                            </svg>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paginação -->
                    <div v-if="products.last_page > 1" class="mt-8 sm:mt-12">
                        <nav class="flex items-center justify-between">
                            <!-- Paginação Mobile -->
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="products.prev_page_url"
                                    :href="products.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-[#231F20] bg-white border border-gray-200 hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <ChevronLeftIcon class="w-5 h-5 mr-1" />
                                    Anterior
                                </Link>
                                <Link
                                    v-if="products.next_page_url"
                                    :href="products.next_page_url"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-[#231F20] bg-white border border-gray-200 hover:bg-gray-50 transition-colors duration-200"
                                >
                                    Próxima
                                    <ChevronRightIcon class="w-5 h-5 ml-1" />
                                </Link>
                            </div>

                            <!-- Paginação Desktop -->
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Mostrando
                                        <span class="font-medium">{{ products.from }}</span>
                                        até
                                        <span class="font-medium">{{ products.to }}</span>
                                        de
                                        <span class="font-medium">{{ products.total }}</span>
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px overflow-hidden">
                                        <Link
                                            v-for="page in products.links"
                                            :key="page.label"
                                            :href="page.url"
                                            :class="[
                                                page.active ? 'z-10 bg-[#231F20] border-[#231F20] text-white' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50',
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200'
                                            ]"
                                            v-html="page.label"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Messages -->
        <div class="fixed inset-x-0 bottom-20 z-50 pointer-events-none">
            <div class="flex flex-col items-center gap-2">
                <div v-for="(toast, productId) in showToast" :key="productId">
                    <!-- Toast de produto adicionado -->
                    <div v-if="toast === 'added'" 
                        class="bg-[#231F20] text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg flex items-center gap-2"
                    >
                        <CheckCircleIcon class="w-5 h-5 text-green-400" />
                        <span>Produto adicionado ao carrinho!</span>
                    </div>
                    <!-- Toast de quantidade atualizada -->
                    <div v-else-if="toast === 'updated'" 
                        class="bg-[#231F20] text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg flex items-center gap-2"
                    >
                        <CheckCircleIcon class="w-5 h-5 text-green-400" />
                        <span>Quantidade atualizada no carrinho!</span>
                    </div>
                    <!-- Toast de erro -->
                    <div v-else-if="toast === 'error'"
                        class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg flex items-center gap-2"
                    >
                        <XCircleIcon class="w-5 h-5" />
                        <span>Erro ao adicionar ao carrinho</span>
                    </div>
                    <!-- Toast de autenticação -->
                    <div v-else-if="toast === 'auth'"
                        class="bg-[#231F20] text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg flex items-center gap-2"
                    >
                        <InformationCircleIcon class="w-5 h-5 text-indigo-400" />
                        <span>Faça login para adicionar ao carrinho</span>
                    </div>
                </div>
            </div>
        </div>
        </template>
    </ProductShowcaseLayout>
</template>

<script setup lang="ts">
import { ref, computed, watchEffect } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { useBreakpoints, breakpointsTailwind } from '@vueuse/core'
import debounce from 'lodash/debounce'
import axios from 'axios'
import { 
    ShoppingCartIcon, 
    XMarkIcon, 
    CheckCircleIcon, 
    XCircleIcon, 
    InformationCircleIcon,
    ChevronLeftIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'

interface Customer {
    id: number
    name: string
    email: string
}

interface Auth {
    customer: Customer | null
    name: string
}

interface Department {
    id: number
    name: string
    categories: number[]
}

interface Category {
    id: number
    name: string
}

interface Product {
    id: number
    name: string
    department: Department
    featured_image: string
    price: number
}

interface PaginatedProducts {
    data: Product[]
    last_page: number
    current_page: number
    from: number
    to: number
    total: number
    prev_page_url: string | null
    next_page_url: string | null
    links: Array<{
        url: string | null
        label: string
        active: boolean
    }>
}

// Configuração dos breakpoints usando Tailwind
const breakpoints = useBreakpoints(breakpointsTailwind)

const props = defineProps<{
    products: PaginatedProducts
    categories: Category[]
    departments: Department[]
    filters: {
        department?: string
        category?: string
        sort?: string
    }
    auth: Auth
}>()

const filters = ref({
    department: props.filters?.department || '',
    category: props.filters?.category || '',
    sort: props.filters?.sort || 'newest'
})

const sortBy = ref(props.filters?.sort || 'newest')
const showFilters = ref(false)

const toggleFilters = () => {
    showFilters.value = !showFilters.value
}

const filteredCategories = computed(() => {
    if (!filters.value.department) return props.categories
    return props.categories.filter(cat => 
        props.departments.find(d => d.id === parseInt(filters.value.department))?.categories?.includes(cat.id)
    )
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
}

const applyFilters = debounce(() => {
    router.get(
        route('products'),
        { ...filters.value },
        { preserveState: true }
    )
}, 300)

const applySort = () => {
    filters.value.sort = sortBy.value
    applyFilters()
}

const clearFilters = () => {
    filters.value = {
        department: '',
        category: '',
        sort: 'newest'
    }
    sortBy.value = 'newest'
    applyFilters()
}

// Breakpoints para responsividade
const screen = {
    mobile: computed(() => !breakpoints.greater('sm')),
    tablet: computed(() => breakpoints.greater('sm') && !breakpoints.greater('lg')),
    desktop: computed(() => breakpoints.greater('lg'))
}

// Estado do carrinho
// Estado inicial do loading e toast para cada produto
const addingToCart = ref({})
const showToast = ref({})

// Inicializa o estado de loading como falso para cada produto
const initializeProductStates = (products) => {
    products.forEach(product => {
        addingToCart.value[product.id] = false
        showToast.value[product.id] = false
    })
}

// Inicializa os estados quando os produtos são carregados
watchEffect(() => {
    if (props.products?.data) {
        initializeProductStates(props.products.data)
    }
})

const addToCart = async (product) => {
    // Verifica se o usuário está autenticado
    if (!props.auth) {
        // Mostra mensagem informando que precisa estar logado
        showToast.value[product.id] = 'auth'
        setTimeout(() => {
            showToast.value[product.id] = false
            // Redireciona para o login após a mensagem
            window.location.href = route('customer.login')
        }, 2000)
        return
    }

    // Previne múltiplos cliques
    if (addingToCart.value[product.id]) return
    
    // Ativa o estado de loading
    addingToCart.value[product.id] = true
    
    try {
        const response = await axios.post(route('cart.add', product.id), {
            quantity: 1
        })
        
        if (response.data.status === 'success') {
            // Atualiza o contador do carrinho
            window.dispatchEvent(new CustomEvent('cart-updated', {
                detail: { count: response.data.cartCount }
            }))
            
            // Mostra o toast apropriado baseado na mensagem do servidor
            showToast.value[product.id] = response.data.message.includes('atualizada') ? 'updated' : 'added'
            setTimeout(() => {
                showToast.value[product.id] = false
            }, 2000)

            // Remove o estado de loading imediatamente
            addingToCart.value[product.id] = false
        }
    } catch (error) {
        // Trata erro de autenticação
        if (error.response?.status === 401) {
            showToast.value[product.id] = 'auth'
            setTimeout(() => {
                showToast.value[product.id] = false
                // Redireciona para o login após a mensagem
                window.location.href = route('customer.login')
            }, 2000)
        } else {
            // Mostra erro genérico
            showToast.value[product.id] = 'error'
            setTimeout(() => {
                showToast.value[product.id] = false
            }, 2000)
        }
        
        // Remove o estado de loading em caso de erro
        addingToCart.value[product.id] = false
    }
}
</script>

<style>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #231F20 #f3f4f6;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #231F20;
    border-radius: 3px;
    border: 2px solid #f3f4f6;
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(1rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.5s ease-out forwards;
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 20px;
}

@keyframes slide-in-left {
    0% {
        transform: translateX(-20px);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in-left {
    animation: slide-in-left 0.3s ease-out forwards;
}

@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.5s ease-out forwards;
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
}

@keyframes fade-in {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

/* Estilização da scrollbar para webkit */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(35, 31, 32, 0.2);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(35, 31, 32, 0.3);
}
</style>
