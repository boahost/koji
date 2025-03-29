<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <Link :href="route('products.index')" class="flex items-center">
                            <img class="h-8 w-auto" src="/images/logo.png" alt="Logo">
                        </Link>
                    </div>

                    <!-- Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <Link 
                            :href="route('products.index')"
                            class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                            :class="{ 'text-gray-900': route().current('products.index') }"
                        >
                            Produtos
                        </Link>
                        <Link 
                            :href="route('cart.index')"
                            class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                            :class="{ 'text-gray-900': route().current('cart.index') }"
                        >
                            Carrinho
                        </Link>
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center">
                        <Link 
                            v-if="$page.props.auth.user"
                            :href="route('orders.index')"
                            class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                            :class="{ 'text-gray-900': route().current('orders.index') }"
                        >
                            Meus Pedidos
                        </Link>
                        <Link 
                            v-else
                            :href="route('login')"
                            class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Entrar
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    &copy; {{ new Date().getFullYear() }} Todos os direitos reservados.
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
