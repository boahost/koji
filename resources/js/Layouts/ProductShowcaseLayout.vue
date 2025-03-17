<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('products')" class="text-xl font-bold text-gray-800">
                                DF Store
                            </Link>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="flex-1 flex items-center justify-center px-2 lg:px-6">
                        <div class="w-full max-w-lg lg:max-w-xs">
                            <label for="search" class="sr-only">Buscar produtos</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                </div>
                                <input
                                    id="search"
                                    name="search"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Buscar produtos..."
                                    type="search"
                                    v-model="search"
                                    @input="debouncedSearch"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Cart -->
                    <div class="flex items-center">
                        <button class="p-2 rounded-full hover:bg-gray-100 relative">
                            <ShoppingCartIcon class="h-6 w-6 text-gray-400 hover:text-gray-500" />
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 px-2 py-0.5 text-xs font-medium bg-blue-500 text-white rounded-full">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <p class="text-base text-gray-400 text-center">&copy; 2025 DF Store. Todos os direitos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { MagnifyingGlassIcon, ShoppingCartIcon } from '@heroicons/vue/24/outline'
import debounce from 'lodash/debounce'
import { router } from '@inertiajs/vue3'

const search = ref('')

const debouncedSearch = debounce(() => {
    router.get(
        route('products'),
        { search: search.value },
        { preserveState: true, preserveScroll: true }
    )
}, 300)
</script>
