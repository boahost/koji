<template>
    <ResellerDashboardLayout>
        <div class="space-y-10 divide-y divide-gray-900/10">
            <div class="grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
                <div class="px-4 sm:px-0">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Perfil</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">
                        Suas informações pessoais.
                    </p>
                </div>

                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl md:col-span-2 animate-fadeInUp">
                    <div class="px-4 py-6 sm:p-8">
                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500">Nome</h4>
                                    <p class="text-sm text-gray-900">{{ reseller.name }}</p>
                                </div>
                            </div>

                            <div class="sm:col-span-4">
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500">E-mail</h4>
                                    <p class="text-sm text-gray-900">{{ reseller.email }}</p>
                                </div>
                            </div>

                            <div class="sm:col-span-4">
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500">CPF/CNPJ</h4>
                                    <p class="text-sm text-gray-900">{{ formatDocument(reseller.document) }}</p>
                                </div>
                            </div>

                            <div class="sm:col-span-4">
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500">Taxa de Comissão</h4>
                                    <p class="text-sm text-gray-900">{{ reseller.commission_rate }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ResellerDashboardLayout>
</template>

<script setup>
import ResellerDashboardLayout from '@/Layouts/ResellerDashboardLayout.vue'

const props = defineProps({
    reseller: {
        type: Object,
        required: true
    }
})

const formatDocument = (doc) => {
    if (doc.length === 11) {
        return doc.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
    }
    return doc.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}
</script>
