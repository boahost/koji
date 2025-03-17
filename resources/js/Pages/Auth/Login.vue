<script setup>
import { ref, onMounted } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline';


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const showPassword = ref(false);
</script>

<template>
    <GuestLayout>
        <Head title="Entrar" />

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <TransitionGroup
                enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                move-class="transition-transform duration-300"
                appear
            >
                <div class="text-center" :style="{ '--delay': '100ms' }">
                    <div class="rounded-2xl bg-gray-100/50 p-2 inline-block mb-4 backdrop-blur-sm ring-1 ring-gray-900/5 transition-transform duration-300 ease-in-out hover:scale-110">
                        <svg class="h-8 w-8 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold leading-9 tracking-tight text-gray-900">Bem-vindo de volta!</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500">Entre com suas credenciais para acessar o sistema.</p>
                </div>
            </TransitionGroup>
        </div>

        <div
            v-if="status"
            class="mt-4 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-600 ring-1 ring-inset ring-green-600/20"
        >
            {{ status }}
        </div>

        <TransitionGroup
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            move-class="transition-transform duration-300"
            appear
        >
            <form @submit.prevent="submit" class="mt-10 space-y-8">
                <div class="space-y-6">
                    <div>
                        <InputLabel for="email" value="E-mail" class="text-sm font-medium leading-6 text-gray-900" />
                        <div class="relative mt-2 rounded-xl shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full rounded-xl border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900 sm:text-sm sm:leading-6 transition-shadow duration-200 ease-in-out"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Digite seu e-mail"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Senha" class="text-sm font-medium leading-6 text-gray-900" />
                        <div class="relative mt-2 rounded-xl shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <LockClosedIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </div>
                            <TextInput
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full rounded-xl border-0 py-2.5 pl-10 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900 sm:text-sm sm:leading-6 transition-shadow duration-200 ease-in-out"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="Digite sua senha"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500 transition-colors duration-200 ease-in-out"
                                @click="showPassword = !showPassword"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                                class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 transition-colors duration-200 ease-in-out"
                            />
                            <span class="ml-3 text-sm text-gray-500">Lembrar-me</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200 ease-in-out"
                        >
                            Esqueceu sua senha?
                        </Link>
                    </div>

                    <div>
                        <PrimaryButton
                            class="flex w-full justify-center rounded-xl bg-gradient-to-r from-black to-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:from-gray-800 hover:to-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-all duration-200 ease-in-out"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            <span class="flex items-center gap-2">
                                <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Entrando...' : 'Entrar' }}</span>
                            </span>
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </TransitionGroup>
    </GuestLayout>
</template>
