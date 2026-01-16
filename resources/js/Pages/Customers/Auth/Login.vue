<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import logo from '../../../../css/logo.png';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('customer.login'), {
        onError: () => {
            form.errors.email = 'As credenciais fornecidas não correspondem aos nossos registros.';
        }
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen bg-[#F5F5F5] flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-pattern">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center animate-fade-in-down">
                <img :src="logo" alt="Logo" style="max-width:180px;" class=" w-auto drop-shadow-sm transition-transform duration-300 hover:scale-105" />
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md animate-fade-in-up">
            <div class="bg-white py-8 px-4 shadow-xl shadow-gray-200/50 sm:rounded-lg sm:px-10 border-t-4 border-[#C0A062] relative overflow-hidden">
                <!-- Decorative background element -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-[#C0A062]/10 rounded-full blur-xl pointer-events-none"></div>

                <form @submit.prevent="submit" class="space-y-6 relative">
                    <div>
                        <InputLabel for="email" value="Email" class="text-gray-700" />
                        <div class="mt-1">
                            <TextInput
                                id="email"
                                type="email"
                                v-model="form.email"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C0A062] focus:ring-[#C0A062] sm:text-sm transition-shadow duration-200"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="seu@email.com"
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="password" value="Senha" class="text-gray-700" />
                        <div class="mt-1">
                            <TextInput
                                id="password"
                                type="password"
                                v-model="form.password"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C0A062] focus:ring-[#C0A062] sm:text-sm transition-shadow duration-200"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" class="text-[#C0A062] focus:ring-[#C0A062]" />
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                Lembrar-me
                            </label>
                        </div>

                        <div class="text-sm">
                            <Link
                                :href="route('customer.password.request')"
                                class="font-medium text-[#C0A062] hover:text-[#A08042] transition-colors"
                            >
                                Esqueceu a senha?
                            </Link>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group relative flex w-full justify-center rounded-md border border-transparent bg-[#231F20] py-2.5 px-4 text-sm font-medium text-white shadow-sm hover:bg-[#332D2E] focus:outline-none focus:ring-2 focus:ring-[#C0A062] focus:ring-offset-2 transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed overflow-hidden"
                        >
                             <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                            <span class="flex items-center gap-2" :class="{ 'opacity-0': form.processing }">
                                Entrar
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-[#C0A062]">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </span>
                            
                            <div v-if="form.processing" class="absolute inset-0 flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-[#C0A062]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-2 text-gray-500">Novo por aqui?</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link
                            :href="route('customer.register')"
                            class="flex w-full justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#C0A062] focus:ring-offset-2 transition-colors"
                        >
                            Criar uma conta
                        </Link>
                    </div>
                </div>
            </div>
            
            <!-- Footer Text -->
            <p class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }} Consulta de Imóveis. Todos os direitos reservados.
            </p>
        </div>
    </div>
</template>

<style scoped>
.bg-pattern {
    background-image: radial-gradient(#C0A062 0.5px, transparent 0.5px), radial-gradient(#C0A062 0.5px, #F5F5F5 0.5px);
    background-size: 20px 20px;
    background-position: 0 0, 10px 10px;
    background-color: #F9FAFB;
    opacity: 1;
}

@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}

.animate-shimmer {
    animation: shimmer 1.5s infinite;
}
</style>

<style>
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
</style>

