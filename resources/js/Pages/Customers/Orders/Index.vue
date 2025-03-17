&lt;script setup&gt;
import { Head } from '@inertiajs/vue3';
import CustomerDashboardLayout from '@/Layouts/CustomerDashboardLayout.vue';

defineProps({
    customer: Object,
    orders: Array,
});
&lt;/script&gt;

&lt;template&gt;
    &lt;Head title="Meus Pedidos" /&gt;

    &lt;CustomerDashboardLayout :customer="customer"&gt;
        &lt;div class="animate-fade-in-up"&gt;
            &lt;!-- Header --&gt;
            &lt;div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100"&gt;
                &lt;h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"&gt;
                    Meus Pedidos
                &lt;/h1&gt;
                &lt;p class="mt-2 text-gray-600"&gt;
                    Acompanhe seus pedidos e histórico de compras
                &lt;/p&gt;
            &lt;/div&gt;

            &lt;!-- Orders List --&gt;
            &lt;div class="mt-6"&gt;
                &lt;div v-if="orders.length === 0" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-center"&gt;
                    &lt;div class="flex justify-center"&gt;
                        &lt;div class="bg-indigo-50 rounded-full p-3"&gt;
                            &lt;svg class="h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"&gt;
                                &lt;path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /&gt;
                            &lt;/svg&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                    &lt;h3 class="mt-4 text-lg font-medium text-gray-900"&gt;Nenhum pedido encontrado&lt;/h3&gt;
                    &lt;p class="mt-2 text-gray-600"&gt;
                        Você ainda não fez nenhum pedido. Que tal começar agora?
                    &lt;/p&gt;
                    &lt;div class="mt-6"&gt;
                        &lt;Link
                            :href="route('products')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-black to-gray-900 hover:from-gray-800 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200"
                        &gt;
                            Ver produtos
                        &lt;/Link&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/CustomerDashboardLayout&gt;
&lt;/template&gt;

&lt;style scoped&gt;
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
&lt;/style&gt;
