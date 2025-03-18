<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    shippingMethod: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.shippingMethod.name,
    description: props.shippingMethod.description,
    value: props.shippingMethod.value,
    active: props.shippingMethod.active
});

const valueInput = ref(null);
const formattedValue = ref('');

// Função para formatar valor em real enquanto digita
const handleValueInput = (e) => {
    // Remove tudo que não é número
    let value = e.target.value.replace(/\D/g, '');
    
    // Se o valor for vazio, define como zero
    if (!value) {
        formattedValue.value = 'R$ 0,00';
        form.value = 0;
        return;
    }
    
    // Converte para centavos (divide por 100)
    const numValue = parseInt(value) / 100;
    if (isNaN(numValue)) {
        formattedValue.value = 'R$ 0,00';
        form.value = 0;
        return;
    }
    
    // Formata o valor com 2 casas decimais
    value = numValue.toFixed(2);
    
    // Formata para o padrão brasileiro
    value = value.replace('.', ',');
    value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    
    // Adiciona o prefixo R$
    formattedValue.value = `R$ ${value}`;
    
    // Atualiza o valor no formulário (como número)
    form.value = numValue;
};

// Inicializa com o valor do método de frete formatado
onMounted(() => {
    if (props.shippingMethod.value !== null && props.shippingMethod.value !== undefined) {
        // Garante que o valor seja um número
        const numValue = parseFloat(props.shippingMethod.value);
        if (!isNaN(numValue)) {
            const value = numValue.toFixed(2).replace('.', ',');
            formattedValue.value = `R$ ${value}`;
        } else {
            formattedValue.value = 'R$ 0,00';
        }
    } else {
        formattedValue.value = 'R$ 0,00';
    }
    
    // Certifique-se de que o valor no formulário seja um número
    if (typeof form.value !== 'number') {
        form.value = parseFloat(form.value) || 0;
    }
});

const submit = () => {
    // Certifique-se de que o valor seja enviado como número
    form.value = parseFloat(form.value) || 0;
    
    form.put(route('shipping-methods.update', props.shippingMethod.id), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Editar Frete" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Frete
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Atualize as informações do método de frete.</p>
                </div>
            </div>

            <!-- Formulário -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Nome" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Descrição" />
                        <TextArea
                            id="description"
                            class="mt-1 block w-full"
                            v-model="form.description"
                            required
                            rows="4"
                        />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div>
                        <InputLabel for="value" value="Valor (R$)" />
                        <input
                            id="value"
                            ref="valueInput"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :value="formattedValue"
                            @input="handleValueInput"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.value" />
                    </div>

                    <div class="flex items-center">
                        <input
                            id="active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                            v-model="form.active"
                        />
                        <label for="active" class="ml-2 block text-sm text-gray-900">
                            Ativo
                        </label>
                        <InputError class="mt-2" :message="form.errors.active" />
                    </div>

                    <div class="flex items-center justify-end">
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Atualizar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
