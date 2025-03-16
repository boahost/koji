<template>
    <Head title="Editar Revendedor" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Revendedor
                    </h2>
                    <p class="mt-1 text-base text-gray-500">
                        Atualize os dados do revendedor
                    </p>
                </div>
                <div class="mt-4 flex sm:ml-4 sm:mt-0">
                    <Link
                        :href="route('resellers.index')"
                        class="flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                        Voltar
                    </Link>
                </div>
            </div>

            <!-- Formulário -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <form @submit.prevent="submit" class="p-6">
                    <div class="space-y-6">
                        <!-- Nome -->
                        <div>
                            <InputLabel for="name" value="Nome" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-2 block w-full"
                                required
                                autofocus
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- CPF/CNPJ -->
                        <div>
                            <InputLabel for="document" value="CPF/CNPJ" />
                            <TextInput
                                id="document"
                                v-model="form.document"
                                type="text"
                                class="mt-2 block w-full"
                                required
                                @input="formatDocument"
                            />
                            <InputError :message="form.errors.document" class="mt-2" />
                        </div>

                        <!-- E-mail -->
                        <div>
                            <InputLabel for="email" value="E-mail" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-2 block w-full"
                                required
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <!-- Senha -->
                        <div>
                            <InputLabel for="password" value="Nova Senha (opcional)" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-2 block w-full"
                            />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <!-- Comissão -->
                        <div>
                            <InputLabel for="commission_rate" value="Comissão (%)" />
                            <TextInput
                                id="commission_rate"
                                v-model="form.commission_rate"
                                type="text"
                                class="mt-2 block w-full"
                                required
                                @input="formatCommission"
                            />
                            <InputError :message="form.errors.commission_rate" class="mt-2" />
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Link
                            :href="route('resellers.index')"
                            class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-300 flex items-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import VMasker from 'vanilla-masker';

const props = defineProps({
    reseller: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.reseller.name,
    document: props.reseller.document,
    email: props.reseller.email,
    password: '',
    commission_rate: props.reseller.commission_rate
});

const formatDocument = (e) => {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        // CPF
        form.document = VMasker.toPattern(value, '999.999.999-99');
    } else {
        // CNPJ
        form.document = VMasker.toPattern(value, '99.999.999/9999-99');
    }
};

const formatCommission = (e) => {
    let value = e.target.value.replace(/[^0-9]/g, '');
    
    if (!value) {
        form.commission_rate = '';
        return;
    }

    value = (parseInt(value) / 100).toFixed(2);
    if (parseFloat(value) > 100) {
        value = '100.00';
    }
    
    form.commission_rate = value;
};

const submit = () => {
    form.put(route('resellers.update', props.reseller.id));
};
</script>
