<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import BottomNavLayout from '@/Layouts/BottomNavLayout.vue';
import AppHeader from '@/Components/AppHeader.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { IMaskComponent } from 'vue-imask';

const props = defineProps({
    customer: Object,
});

// Formulário para atualização de endereço
const addressForm = useForm({
    name: props.customer.name,
    email: props.customer.email,
    document: props.customer.document,
    cep: props.customer.cep || '',
    street: props.customer.street || '',
    number: props.customer.number || '',
    neighborhood: props.customer.neighborhood || '',
    city: props.customer.city || '',
    state: props.customer.state || '',
    complement: props.customer.complement || '',
});

// Formulário para atualização de senha
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const loading = ref(false);
const showPasswordForm = ref(false);

const updateAddress = () => {
    loading.value = true;
    addressForm.put(route('customer.profile.update'), {
        onFinish: () => loading.value = false,
        preserveScroll: true,
    });
};

const updatePassword = () => {
    if (passwordForm.password !== passwordForm.password_confirmation) {
        passwordForm.errors.password_confirmation = 'A confirmação da senha não corresponde.';
        return;
    }

    loading.value = true;
    passwordForm.put(route('customer.profile.update'), {
        onSuccess: () => {
            loading.value = false;
            showPasswordForm.value = false;
            passwordForm.reset();
        },
        onError: () => {
            loading.value = false;
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Perfil" />

    <BottomNavLayout>
        <AppHeader @search-historico="$emit('search-historico', $event)" />
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="animate-fade-in-up">
            <!-- Header -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold text-[#231F20]">
                    Meu Perfil
                </h1>
                <p class="mt-2 text-gray-600">
                    Atualize suas informações pessoais
                </p>
            </div>

            <!-- Profile Form -->
            <!-- Formulário de Endereço -->
            <form @submit.prevent="updateAddress" class="mt-8 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 space-y-6">
                    <h2 class="text-lg font-medium text-[#231F20]">
                        Informações Pessoais
                    </h2>

                    <!-- Name -->
                    <div class="space-y-1">
                        <InputLabel for="name" value="Nome completo" />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="addressForm.name"
                            class="mt-1 block w-full bg-gray-50 cursor-not-allowed"
                            disabled
                        />
                        <p class="mt-1 text-sm text-gray-500">O nome não pode ser alterado</p>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="addressForm.email"
                            class="mt-1 block w-full bg-gray-50 cursor-not-allowed"
                            disabled
                        />
                        <p class="mt-1 text-sm text-gray-500">O e-mail não pode ser alterado</p>
                    </div>

                    <!-- Document -->
                    <div class="space-y-1">
                        <InputLabel for="document" value="CPF" />
                        <IMaskComponent
                            v-model="addressForm.document"
                            :mask="'000.000.000-00'"
                            :unmask="true"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm bg-gray-50 cursor-not-allowed"
                            disabled
                        />
                        <p class="mt-1 text-sm text-gray-500">O CPF não pode ser alterado</p>
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 space-y-6">
                    <h2 class="text-lg font-medium text-[#231F20]">
                        Endereço
                    </h2>

                    <!-- CEP -->
                    <div class="space-y-1">
                        <InputLabel for="cep" value="CEP" />
                        <IMaskComponent
                            v-model="addressForm.cep"
                            :mask="'00000-000'"
                            :unmask="true"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm"
                        />
                        <InputError :message="addressForm.errors.cep" class="mt-1" />
                    </div>

                    <!-- Street -->
                    <div class="space-y-1">
                        <InputLabel for="street" value="Rua" />
                        <TextInput
                            id="street"
                            type="text"
                            v-model="addressForm.street"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="addressForm.errors.street" class="mt-1" />
                    </div>

                    <!-- Number -->
                    <div class="space-y-1">
                        <InputLabel for="number" value="Número" />
                        <TextInput
                            id="number"
                            type="text"
                            v-model="addressForm.number"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="addressForm.errors.number" class="mt-1" />
                    </div>

                    <!-- Complement -->
                    <div class="space-y-1">
                        <InputLabel for="complement" value="Complemento" />
                        <TextInput
                            id="complement"
                            type="text"
                            v-model="addressForm.complement"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="addressForm.errors.complement" class="mt-1" />
                    </div>

                    <!-- Neighborhood -->
                    <div class="space-y-1">
                        <InputLabel for="neighborhood" value="Bairro" />
                        <TextInput
                            id="neighborhood"
                            type="text"
                            v-model="addressForm.neighborhood"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="addressForm.errors.neighborhood" class="mt-1" />
                    </div>

                    <!-- City -->
                    <div class="space-y-1">
                        <InputLabel for="city" value="Cidade" />
                        <TextInput
                            id="city"
                            type="text"
                            v-model="addressForm.city"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="addressForm.errors.city" class="mt-1" />
                    </div>

                    <!-- State -->
                    <div class="space-y-1">
                        <InputLabel for="state" value="Estado" />
                        <TextInput
                            id="state"
                            type="text"
                            v-model="addressForm.state"
                            class="mt-1 block w-full"
                            maxlength="2"
                            style="text-transform: uppercase;"
                            required
                        />
                        <InputError :message="addressForm.errors.state" class="mt-1" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4 mt-6">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="addressForm.processing"
                        >
                            <span v-if="addressForm.processing">Atualizando...</span>
                            <span v-else>Atualizar Endereço</span>
                        </button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="addressForm.recentlySuccessful" class="text-sm text-gray-600">
                                Endereço atualizado com sucesso!
                            </p>
                        </Transition>
                    </div>
                </div>
            </form>

            <!-- Botão para exibir formulário de senha -->
            <div class="mt-8" v-if="!showPasswordForm">
                <button
                    type="button"
                    class="px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors duration-200"
                    @click="showPasswordForm = true"
                >
                    Alterar Senha
                </button>
            </div>

            <!-- Formulário de Senha -->
            <form @submit.prevent="updatePassword" class="mt-8 space-y-6" v-if="showPasswordForm">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            Alterar Senha
                        </h2>
                        <button
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-[#231F20]/70 hover:text-[#231F20]/90 transition-colors duration-200 bg-gray-100 hover:bg-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20]"
                            @click="showPasswordForm = false"
                        >
                            Cancelar
                        </button>
                    </div>

                    <div class="mt-6 space-y-6">
                        <!-- Current Password -->
                        <div class="space-y-1">
                            <InputLabel for="current_password" value="Senha atual" />
                            <TextInput
                                id="current_password"
                                type="password"
                                v-model="passwordForm.current_password"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="passwordForm.errors.current_password" class="mt-1" />
                        </div>

                        <!-- New Password -->
                        <div class="space-y-1">
                            <InputLabel for="password" value="Nova senha" />
                            <TextInput
                                id="password"
                                type="password"
                                v-model="passwordForm.password"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="passwordForm.errors.password" class="mt-1" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1">
                            <InputLabel for="password_confirmation" value="Confirme a nova senha" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                v-model="passwordForm.password_confirmation"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="passwordForm.errors.password_confirmation" class="mt-1" />
                        </div>

                        <div class="flex items-center gap-4">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-[#231F20] text-white text-sm font-medium rounded-full hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#231F20] transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="passwordForm.processing"
                            >
                                <span v-if="passwordForm.processing">Alterando...</span>
                                <span v-else>Alterar Senha</span>
                            </button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-if="passwordForm.recentlySuccessful" class="text-sm text-gray-600">
                                    Senha alterada com sucesso!
                                </p>
                            </Transition>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        </main>
    </BottomNavLayout>
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
