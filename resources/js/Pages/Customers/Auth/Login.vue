<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

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

    <ProductShowcaseLayout>
        <div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8 animate-fade-in-up">
                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Bem-vindo de volta!
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Faça login para acessar sua conta
                    </p>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="space-y-6 relative">
                        <!-- Email -->
                        <div class="space-y-1">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                v-model="form.email"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="seu@email.com"
                            />
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-1">
                            <InputLabel for="password" value="Senha" />
                            <TextInput
                                id="password"
                                type="password"
                                v-model="form.password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <InputError :message="form.errors.password" class="mt-1" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                <span class="ml-2 text-sm text-gray-600">Lembrar-me</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            class="group relative flex w-full justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-4 text-sm font-semibold text-white shadow-sm hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 ease-in-out overflow-hidden"
                        >
                            <!-- Loading Spinner -->
                            <div v-if="form.processing" class="absolute inset-0 flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            
                            <span :class="{ 'opacity-0': form.processing }">Entrar</span>
                        </button>
                    </div>

                    <!-- Links -->
                    <div class="flex items-center justify-between mt-4 text-sm">
                        <Link
                            :href="route('customer.register')"
                            class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
                        >
                            Criar conta
                        </Link>

                        <Link
                            :href="route('customer.password.request')"
                            class="font-medium text-gray-600 hover:text-gray-500 transition-colors duration-200"
                        >
                            Esqueceu a senha?
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </ProductShowcaseLayout>
</template>

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
