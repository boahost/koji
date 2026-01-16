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

    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 animate-fade-in-up">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <img :src="logo" alt="Logo" class="h-40 w-auto" />
                </div>
            </div>

            <form @submit.prevent="submit" class="mt-8 space-y-6 bg-white p-6 sm:p-8 rounded-lg shadow-sm border border-gray-100">
                <div class="space-y-6 relative">
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

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ml-2 text-sm text-gray-600">Lembrar-me</span>
                        </label>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        class="group relative flex w-full justify-center rounded-full bg-[#231F20] py-3 px-4 text-sm font-semibold text-white shadow-sm hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-[#231F20] focus:ring-offset-2 transition-colors duration-200"
                    >
                        <div v-if="form.processing" class="absolute inset-0 flex items-center justify-center bg-[#231F20]">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <span :class="{ 'opacity-0': form.processing }">Entrar</span>
                    </button>
                </div>

                <div class="flex items-center justify-between mt-4 text-sm">
                    <Link
                        :href="route('customer.register')"
                        class="font-medium text-[#231F20] hover:text-[#231F20]/80 transition-colors duration-200"
                    >
                        Criar conta
                    </Link>

                    <Link
                        :href="route('customer.password.request')"
                        class="font-medium text-[#231F20]/70 hover:text-[#231F20]/90 transition-colors duration-200"
                    >
                        Esqueceu a senha?
                    </Link>
                </div>
            </form>
        </div>
    </div>
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

