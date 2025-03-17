<script setup>
import { ref } from 'vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import VMasker from 'vanilla-masker'

const props = defineProps({
    customer: Object
})

const loadingCep = ref(false)

const formatDocument = (e) => {
    let value = e.target.value.replace(/\D/g, '')
    
    if (value.length <= 11) {
        // CPF
        form.document = VMasker.toPattern(value, '999.999.999-99')
    } else {
        // CNPJ
        form.document = VMasker.toPattern(value, '99.999.999/9999-99')
    }
}

const formatCep = (e) => {
    let value = e.target.value.replace(/\D/g, '')
    form.cep = VMasker.toPattern(value, '99999-999')
}

const form = useForm({
    name: props.customer.name,
    email: props.customer.email,
    document: props.customer.document,
    cep: props.customer.cep,
    street: props.customer.street,
    number: props.customer.number,
    complement: props.customer.complement,
    neighborhood: props.customer.neighborhood,
    city: props.customer.city,
    state: props.customer.state
})

async function searchCep() {
    const unmaskedCep = form.cep.replace(/\D/g, '')
    
    if (unmaskedCep.length === 8) {
        loadingCep.value = true
        try {
            const response = await fetch(`https://viacep.com.br/ws/${unmaskedCep}/json/`)
            const data = await response.json()
            
            if (!data.erro) {
                form.street = data.logradouro
                form.neighborhood = data.bairro
                form.city = data.localidade
                form.state = data.uf
            }
        } catch (error) {
            console.error('Erro ao buscar CEP:', error)
        } finally {
            loadingCep.value = false
        }
    }
}

function submit() {
    form.put(route('customers.update', props.customer.id))
}
</script>

<template>
    <Head title="Editar Cliente" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Cliente
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Atualize os dados do cliente no sistema.</p>
                </div>
                <div class="mt-4 flex sm:ml-4 sm:mt-0">
                    <Link
                        :href="route('customers.index')"
                        class="inline-flex items-center gap-x-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all duration-200 ease-in-out"
                    >
                        <ArrowLeftIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        Voltar
                    </Link>
                </div>
            </div>

            <!-- Formulário -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <form @submit.prevent="submit" class="animate-fade-in-up">
                        <div class="space-y-8">
                            <!-- Informações Pessoais -->
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-3">Informações Pessoais</h3>
                                <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                                    <div class="sm:col-span-1">
                                        <InputLabel for="name" value="Nome" />
                                        <TextInput
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="mt-2 block w-full"
                                            required
                                            autofocus
                                            placeholder="Nome completo"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-1">
                                        <InputLabel for="document" value="CPF/CNPJ" />
                                        <TextInput
                                            id="document"
                                            v-model="form.document"
                                            type="text"
                                            class="mt-2 block w-full"
                                            required
                                            @input="formatDocument"
                                            placeholder="000.000.000-00 ou 00.000.000/0000-00"
                                        />
                                        <InputError :message="form.errors.document" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-1">
                                        <InputLabel for="email" value="Email" />
                                        <TextInput
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="mt-2 block w-full"
                                            required
                                            placeholder="exemplo@email.com"
                                        />
                                        <InputError :message="form.errors.email" class="mt-2" />
                                    </div>

                                </div>
                            </div>

                            <!-- Endereço -->
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-3">Endereço</h3>
                                <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                                    <div class="sm:col-span-2">
                                        <InputLabel for="cep" value="CEP" />
                                        <div class="relative">
                                            <TextInput
                                                id="cep"
                                                v-model="form.cep"
                                                type="text"
                                                class="mt-2 block w-full"
                                                required
                                                @input="formatCep"
                                                @keyup="searchCep"
                                                placeholder="00000-000"
                                                :class="{ 'pr-10': loadingCep }"
                                            />
                                            <div v-if="loadingCep" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                                <svg class="animate-spin h-5 w-5 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <InputError :message="form.errors.cep" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-4">
                                        <InputLabel for="street" value="Rua" />
                                        <div class="relative">
                                            <TextInput
                                                id="street"
                                                v-model="form.street"
                                                type="text"
                                                class="mt-2 block w-full bg-gray-50"
                                                required
                                                readonly
                                            />
                                            <div v-if="loadingCep" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                                <svg class="animate-spin h-5 w-5 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <InputError :message="form.errors.street" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <InputLabel for="number" value="Número" />
                                        <TextInput
                                            id="number"
                                            v-model="form.number"
                                            type="text"
                                            class="mt-2 block w-full"
                                            required
                                        />
                                        <InputError :message="form.errors.number" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-4">
                                        <InputLabel for="complement" value="Complemento" />
                                        <TextInput
                                            id="complement"
                                            v-model="form.complement"
                                            type="text"
                                            class="mt-2 block w-full"
                                            placeholder="Opcional"
                                        />
                                        <InputError :message="form.errors.complement" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-3">
                                        <InputLabel for="neighborhood" value="Bairro" />
                                        <TextInput
                                            id="neighborhood"
                                            v-model="form.neighborhood"
                                            type="text"
                                            class="mt-2 block w-full bg-gray-50"
                                            required
                                            readonly
                                        />
                                        <InputError :message="form.errors.neighborhood" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <InputLabel for="city" value="Cidade" />
                                        <TextInput
                                            id="city"
                                            v-model="form.city"
                                            type="text"
                                            class="mt-2 block w-full bg-gray-50"
                                            required
                                            readonly
                                        />
                                        <InputError :message="form.errors.city" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-1">
                                        <InputLabel for="state" value="UF" />
                                        <TextInput
                                            id="state"
                                            v-model="form.state"
                                            type="text"
                                            class="mt-2 block w-full uppercase bg-gray-50"
                                            required
                                            readonly
                                            maxlength="2"
                                        />
                                        <InputError :message="form.errors.state" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-4 pt-5 border-t">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                class="bg-blue-900 hover:bg-blue-800"
                            >
                                <span v-if="form.processing">Salvando...</span>
                                <span v-else>Salvar</span>
                            </PrimaryButton>

                            <Transition
                                enter-active-class="transition ease-out duration-300"
                                enter-from-class="transform opacity-0 translate-y-4"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-200"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-4"
                            >
                                <p
                                    v-if="form.recentlySuccessful"
                                    class="text-sm text-gray-600 flex items-center gap-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Cliente atualizado com sucesso.
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
