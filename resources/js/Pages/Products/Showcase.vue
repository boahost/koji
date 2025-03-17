<template>
    <ProductShowcaseLayout>
        <!-- Container Principal -->
        <div class="relative">
            <!-- Botão Toggle Filtros Mobile -->
            <button @click="toggleFilters" 
                class="fixed bottom-4 right-4 z-50 lg:hidden bg-indigo-600 text-white p-3 rounded-full shadow-lg hover:bg-indigo-700 transition-all duration-200 flex items-center justify-center">
                <FunnelIcon class="h-6 w-6" />
            </button>

            <!-- Overlay para Mobile -->
            <div v-if="showFilters" 
                class="fixed inset-0 bg-black/50 z-40 lg:hidden transition-opacity duration-300"
                @click="toggleFilters"></div>

            <!-- Container de Conteúdo -->
            <div class="flex gap-6">
                <!-- Filtros Laterais -->
                <div :class="[
                    'w-64 flex-shrink-0 transition-all duration-300 ease-in-out',
                    'fixed lg:static top-0 bottom-0 z-50 lg:z-0',
                    showFilters ? 'left-0' : '-left-64 lg:left-0',
                    'bg-white lg:bg-transparent shadow-lg lg:shadow-none'
                ]">
                    <div class="h-full overflow-y-auto p-4 bg-gradient-to-b from-gray-50 to-white rounded-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-indigo-900">Filtros</h2>
                        <button @click="toggleFilters" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                    </div>
                    
                    <!-- Departamentos -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-indigo-900 mb-2">Departamentos</h3>
                        <div class="space-y-2">
                            <label v-for="dept in departments" :key="dept.id" class="flex items-center">
                                <input
                                    type="radio"
                                    name="department"
                                    :value="dept.id"
                                    v-model="filters.department"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    @change="applyFilters"
                                >
                                <span class="ml-2 text-sm text-gray-600">{{ dept.name }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Categorias -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-indigo-900 mb-2">Categorias</h3>
                        <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                            <label v-for="cat in filteredCategories" :key="cat.id" class="flex items-center">
                                <input
                                    type="radio"
                                    name="category"
                                    :value="cat.id"
                                    v-model="filters.category"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    @change="applyFilters"
                                >
                                <span class="ml-2 text-sm text-gray-600">{{ cat.name }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Limpar Filtros -->
                    <button
                        @click="clearFilters"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-md hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105"
                    >
                        Limpar Filtros
                    </button>
                    </div>
                </div>

                <!-- Lista de Produtos -->
                <div class="flex-1 px-4 sm:px-0">
                    <!-- Resultados e Ordenação -->
                    <div class="flex items-center justify-between mb-6 animate-fade-in">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ products.total }} Produtos Encontrados
                    </h1>
                    <select
                        v-model="sortBy"
                        @change="applySort"
                        class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option value="newest">Mais Recentes</option>
                        <option value="price_asc">Menor Preço</option>
                        <option value="price_desc">Maior Preço</option>
                        <option value="name">Nome (A-Z)</option>
                    </select>
                </div>

                    <!-- Grid de Produtos -->
                    <TransitionGroup
                    tag="div"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-for="(product, index) in products.data"
                        :key="product.id"
                        class="group bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden animate-fade-in-up"
                        :style="{ animationDelay: (index * 100) + 'ms' }"
                    >
                        <!-- Imagem do Produto -->
                        <div class="relative aspect-w-4 aspect-h-3 bg-gray-200 group-hover:opacity-75 transition-opacity">
                            <img
                                :src="product.featured_image"
                                :alt="product.name"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-200"
                            >
                        </div>

                        <!-- Informações do Produto -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-500">{{ product.category.name }}</span>
                                <span class="text-xs text-blue-600 font-medium">{{ product.department.name }}</span>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ product.name }}
                            </h3>
                            <p class="mt-2 text-lg font-bold text-gray-900">
                                {{ formatCurrency(product.price) }}
                            </p>
                            
                            <!-- Botão Comprar -->
                            <button
                                class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium
                                hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                transform transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0"
                            >
                                Comprar
                            </button>
                        </div>
                    </div>
                </TransitionGroup>

                    <!-- Paginação -->
                    <div v-if="products.last_page > 1" class="mt-12">
                        <nav class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                v-if="products.prev_page_url"
                                :href="products.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Anterior
                            </Link>
                            <Link
                                v-if="products.next_page_url"
                                :href="products.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Próxima
                            </Link>
                        </div>
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
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link
                                        v-for="page in products.links"
                                        :key="page.label"
                                        :href="page.url"
                                        :class="[
                                            page.active ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
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
    </ProductShowcaseLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue'
import { FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import debounce from 'lodash/debounce'

const props = defineProps({
    products: Object,
    categories: Array,
    departments: Array,
    filters: Object
})

const filters = ref({
    department: props.filters.department || '',
    category: props.filters.category || '',
    search: props.filters.search || ''
})

const sortBy = ref('newest')
const showFilters = ref(window.innerWidth >= 1024) // Mostrar filtros por padrão em telas grandes
const toggleFilters = () => {
    showFilters.value = !showFilters.value
    if (showFilters.value) {
        document.body.style.overflow = 'hidden'
    } else {
        document.body.style.overflow = ''
    }
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
    router.get(
        route('products'),
        { ...filters.value, sort: sortBy.value },
        { preserveState: true }
    )
}

const clearFilters = () => {
    filters.value = {
        department: '',
        category: '',
        search: ''
    }
    applyFilters()
}
</script>

<style>
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
    background: theme('colors.indigo.200');
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: theme('colors.indigo.300');
}
</style>
