<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import CustomerDashboardLayout from '@/Layouts/CustomerDashboardLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { IMaskComponent } from 'vue-imask';

const props = defineProps({
    customer: Object,
});

const form = useForm({
    name: props.customer.name,
    email: props.customer.email,
    document: props.customer.document,
    cep: props.customer.cep,
    street: props.customer.street,
    number: props.customer.number,
    complement: props.customer.complement,
    current_password: '',
    password: '',
    password_confirmation: '',
});

const loading = ref(false);
const showPasswordForm = ref(false);

const submit = () => {
    loading.value = true;
    form.put(route('customer.profile.update'), {
        onFinish: () => loading.value = false,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Perfil" />

    <CustomerDashboardLayout>
        <div class="animate-fade-in-up">
            <!-- Header -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Meu Perfil
                </h1>
                <p class="mt-2 text-gray-600">
                    Atualize suas informações pessoais
                </p>
            </div>

            <!-- Profile Form -->
            <form @submit.prevent="submit" class="mt-8 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Informações Pessoais
                    </h2>

                    <!-- Name -->
                    <div class="space-y-1">
                        <InputLabel for="name" value="Nome completo" />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="form.name"
                            class="mt-1 block w-full"
                            required
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
                            disabled
                        />
                        <InputError :message="form.errors.document" class="mt-1" />
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Endereço
                    </h2>

                    <!-- CEP -->
                    <div class="space-y-1">
                        <InputLabel for="cep" value="CEP" />
                        <IMaskComponent
                            v-model="form.cep"
                            :mask="'00000-000'"
                            :unmask="true"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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
                        />
                        <InputError :message="form.errors.complement" class="mt-1" />
                    </div>
                </div>

                <!-- Password -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            Alterar Senha
                        </h2>
                        <button
                            type="button"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
                            @click="showPasswordForm = !showPasswordForm"
                        >
                            {{ showPasswordForm ? 'Cancelar' : 'Alterar' }}
                        </button>
                    </div>

                    <div v-if="showPasswordForm" class="mt-6 space-y-6">
                        <!-- Current Password -->
                        <div class="space-y-1">
                            <InputLabel for="current_password" value="Senha atual" />
                            <TextInput
                                id="current_password"
                                type="password"
                                v-model="form.current_password"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.current_password" class="mt-1" />
                        </div>

                        <!-- New Password -->
                        <div class="space-y-1">
                            <InputLabel for="password" value="Nova senha" />
                            <TextInput
                                id="password"
                                type="password"
                                v-model="form.password"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.password" class="mt-1" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1">
                            <InputLabel for="password_confirmation" value="Confirme a nova senha" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.password_confirmation" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        class="inline-flex justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-8 text-sm font-semibold text-white shadow-sm hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 ease-in-out overflow-hidden relative"
                    >
                        <!-- Loading Spinner -->
                        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <span :class="{ 'opacity-0': loading }">Salvar alterações</span>
                    </button>
                </div>
            </form>
        </div>
    </CustomerDashboardLayout>
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
