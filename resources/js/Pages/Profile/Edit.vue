<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Perfil" />

    <DashboardLayout>
        <div class="py-8 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl space-y-6">
                <!-- Header -->
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-900 to-blue-950">
                            Meu Perfil
                        </h2>
                        <p class="mt-2 text-sm text-gray-500">Gerencie suas informações pessoais e preferências.</p>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <div class="p-6">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <!-- Password Update -->
                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <div class="p-6">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <div class="p-6">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
