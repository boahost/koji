<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ProductShowcaseLayout from '@/Layouts/ProductShowcaseLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { IMaskComponent } from 'vue-imask';

const form = useForm({
    name: '',
    email: '',
    document: '',
    password: '',
    password_confirmation: '',
    cep: '',
    street: '',
    number: '',
    complement: '',
});

const loading = ref(false);

const submit = () => {
    loading.value = true;
    form.post(route('customer.register'), {
        onFinish: () => loading.value = false
    });
};
</script>

<template>
    <Head title="Registro" />

    <ProductShowcaseLayout>
        <div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-md space-y-8 animate-fade-in-up">
                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Crie sua conta
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Registre-se para começar a comprar
                    </p>
                </div>

                <!-- Register Form -->
                <form @submit.prevent="submit" class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="space-y-6">
                        <!-- Name -->
                        <div class="space-y-1">
                            <InputLabel for="name" value="Nome completo" />
                            <TextInput
                                id="name"
                                type="text"
                                v-model="form.name"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                placeholder="João da Silva"
                            />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                v-model="form.email"
                                class="mt-1 block w-full"
                                required
                                placeholder="seu@email.com"
                            />
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>

                        <!-- Document -->
                        <div class="space-y-1">
                            <InputLabel for="document" value="CPF" />
                            <IMaskComponent
                                v-model="form.document"
                                :mask="'000.000.000-00'"
                                :unmask="true"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="000.000.000-00"
                            />
                            <InputError :message="form.errors.document" class="mt-1" />
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
                                placeholder="••••••••"
                            />
                            <InputError :message="form.errors.password" class="mt-1" />
                        </div>

                        <!-- Password Confirmation -->
                        <div class="space-y-1">
                            <InputLabel for="password_confirmation" value="Confirme a senha" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                class="mt-1 block w-full"
                                required
                                placeholder="••••••••"
                            />
                            <InputError :message="form.errors.password_confirmation" class="mt-1" />
                        </div>

                        <!-- CEP -->
                        <div class="space-y-1">
                            <InputLabel for="cep" value="CEP" />
                            <IMaskComponent
                                v-model="form.cep"
                                :mask="'00000-000'"
                                :unmask="true"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="00000-000"
                            />
                            <InputError :message="form.errors.cep" class="mt-1" />
                        </div>

                        <!-- Street -->
                        <div class="space-y-1">
                            <InputLabel for="street" value="Rua" />
                            <TextInput
                                id="street"
                                type="text"
                                v-model="form.street"
                                class="mt-1 block w-full"
                                required
                                placeholder="Rua das Flores"
                            />
                            <InputError :message="form.errors.street" class="mt-1" />
                        </div>

                        <!-- Number -->
                        <div class="space-y-1">
                            <InputLabel for="number" value="Número" />
                            <TextInput
                                id="number"
                                type="text"
                                v-model="form.number"
                                class="mt-1 block w-full"
                                required
                                placeholder="123"
                            />
                            <InputError :message="form.errors.number" class="mt-1" />
                        </div>

                        <!-- Complement -->
                        <div class="space-y-1">
                            <InputLabel for="complement" value="Complemento" />
                            <TextInput
                                id="complement"
                                type="text"
                                v-model="form.complement"
                                class="mt-1 block w-full"
                                placeholder="Apto 101"
                            />
                            <InputError :message="form.errors.complement" class="mt-1" />
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
                            <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            
                            <span :class="{ 'opacity-0': loading }">Criar conta</span>
                        </button>
                    </div>

                    <!-- Links -->
                    <div class="flex items-center justify-center mt-4 text-sm">
                        <span class="text-gray-500">Já tem uma conta?</span>
                        <Link
                            :href="route('customer.login')"
                            class="ml-1 font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
                        >
                            Faça login
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
