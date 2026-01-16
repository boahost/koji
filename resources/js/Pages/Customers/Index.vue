<script setup>
import { ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { MagnifyingGlassIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline'
import VMasker from 'vanilla-masker'

const props = defineProps({
    customers: Object,
    filters: Object
})

const search = ref(props.filters.search)

const form = useForm({
    search: props.filters.search
})

const balanceForm = useForm({
    amount: ''
})

const confirmingBalanceAddition = ref(false)
const selectedCustomer = ref(null)

function submit() {
    form.get(route('customers.index'), {
        preserveState: true,
        preserveScroll: true,
    })
}

const confirmAddBalance = (customer) => {
    selectedCustomer.value = customer
    confirmingBalanceAddition.value = true
    balanceForm.reset()
}

const handleAmountInput = (e) => {
    // Removido em favor do watch
}

watch(() => balanceForm.amount, (newValue) => {
    if (!newValue && newValue !== 0) return
    
    const masked = VMasker.toMoney(newValue.toString(), {
        precision: 2,
        separator: ',',
        delimiter: '.',
        unit: 'R$',
        suffixUnit: ''
    })
    
    if (balanceForm.amount !== masked) {
        balanceForm.amount = masked
    }
})

const closeBalanceModal = () => {
    confirmingBalanceAddition.value = false
    balanceForm.reset()
    selectedCustomer.value = null
}

const submitBalance = () => {
    // Limpa a formatação para enviar apenas o número float
    const cleanAmount = balanceForm.amount
        .toString()
        .replace(/[^\d,]/g, '') // Remove tudo exceto dígitos e vírgula
        .replace(',', '.')      // Troca vírgula por ponto

    balanceForm.transform((data) => ({
        ...data,
        amount: parseFloat(cleanAmount) || 0
    })).post(route('customers.add-balance', selectedCustomer.value.id), {
        preserveScroll: true,
        onSuccess: () => closeBalanceModal(),
    })
}
</script>

<template>
    <Head title="Clientes" />

    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Clientes</h2>
                <Link
                    :href="route('customers.create')"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Novo Cliente
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <form @submit.prevent="submit" class="flex gap-4">
                                <div class="flex-1">
                                    <TextInput
                                        v-model="form.search"
                                        type="text"
                                        class="w-full"
                                        placeholder="Buscar por nome, email ou documento..."
                                    />
                                </div>
                                <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <MagnifyingGlassIcon class="w-4 h-4 mr-2" />
                                    Buscar
                                </PrimaryButton>
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Documento</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Saldo</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cidade/UF</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="customer in customers.data" :key="customer.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ customer.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ customer.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ customer.document }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">
                                            {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(customer.wallets_sum_valor || 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ customer.city }}/{{ customer.state }}</td>
                                        <td class="px-6 py-4 text-sm text-right space-x-2">
                                            <button
                                                @click="confirmAddBalance(customer)"
                                                class="text-green-600 hover:text-green-900 inline-flex items-center"
                                                title="Adicionar Saldo"
                                            >
                                                <CurrencyDollarIcon class="w-5 h-5" />
                                            </button>
                                            <Link
                                                :href="route('customers.edit', customer.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Editar
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="customers.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum cliente encontrado.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="customers.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Adicionar Saldo -->
        <Modal :show="confirmingBalanceAddition" @close="closeBalanceModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Adicionar Saldo para {{ selectedCustomer?.name }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Informe o valor a ser adicionado à carteira do cliente.
                </p>

                <div class="mt-6">
                    <InputLabel for="amount" value="Valor (R$)" />

                    <TextInput
                        id="amount"
                        ref="amountInput"
                        v-model="balanceForm.amount"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="R$ 0,00"
                        @input="handleAmountInput"
                        @keyup.enter="submitBalance"
                    />

                    <InputError :message="balanceForm.errors.amount" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeBalanceModal"> Cancelar </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': balanceForm.processing }"
                        :disabled="balanceForm.processing"
                        @click="submitBalance"
                    >
                        Adicionar Saldo
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>
