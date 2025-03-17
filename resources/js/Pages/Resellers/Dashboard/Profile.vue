<template>
    <ResellerDashboardLayout :reseller="props.reseller">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-gray-900">Perfil</h1>
            </div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
                <div class="py-4">
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl animate-fadeInUp">
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
