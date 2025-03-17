<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Senha" />

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="rounded-2xl bg-gray-100/50 p-2 inline-block mb-4 backdrop-blur-sm ring-1 ring-gray-900/5 transition-transform duration-300 ease-in-out hover:scale-110">
                    <svg class="h-8 w-8 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold leading-9 tracking-tight text-gray-900">Recuperar Senha</h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Esqueceu sua senha? Sem problemas. Apenas informe seu endereço de e-mail
                    e enviaremos um link para você criar uma nova senha.
                </p>
            </div>
        </div>

        <div
            v-if="status"
            class="mb-4 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-600 ring-1 ring-inset ring-green-600/20"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="mt-10 space-y-6">
            <div>
                <InputLabel for="email" value="E-mail" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-0 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900 sm:text-sm sm:leading-6 transition-shadow duration-200 ease-in-out"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Digite seu e-mail"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="w-full bg-gradient-to-r from-black to-gray-900 hover:from-gray-800 hover:to-gray-700 text-white px-4 py-2.5 rounded-xl transition-all duration-200"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ form.processing ? 'Enviando...' : 'Enviar Link de Recuperação' }}</span>
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
