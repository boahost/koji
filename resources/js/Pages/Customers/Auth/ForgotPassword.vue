<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage();
const form = useForm({
    email: '',
});

const loading = ref(false);

const submit = () => {
    loading.value = true;
    form.post(route('customer.password.email'), {
        onSuccess: (page) => {
            status.value = page.props.status;
            form.reset();
        },
        onFinish: () => loading.value = false
    });
};
</script>

<template>
    <Head title="Esqueci minha senha" />

    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 animate-fade-in-up">
            <!-- Logo -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <span class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#231F20] drop-shadow" style="letter-spacing: 0.1em;">consultadeimoveis.com</span>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-[#231F20]">
                    Esqueceu sua senha?
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Digite seu e-mail para receber um link de redefinição
                </p>
            </div>

            <!-- Success Message -->
            <div v-if="$page.props.flash && $page.props.flash.status" class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ $page.props.flash.status }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="space-y-6">
                    <!-- Email -->
                    <div class="space-y-1">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            placeholder="seu@email.com"
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        class="group relative flex w-full justify-center rounded-full bg-[#231F20] py-3 px-4 text-sm font-semibold text-white shadow-sm hover:bg-[#231F20]/90 focus:outline-none focus:ring-2 focus:ring-[#231F20] focus:ring-offset-2 transition-colors duration-200"
                    >
                                                    <!-- Loading Spinner -->
                            <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-[#231F20]">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <span :class="{ 'opacity-0': loading }">Enviar link de redefinição</span>
                    </button>
                </div>

                <!-- Links -->
                <div class="flex items-center justify-center mt-4 text-sm">
                                            <Link
                            :href="route('customer.login')"
                            class="font-medium text-[#231F20] hover:text-[#231F20]/80 transition-colors duration-200"
                        >
                            Voltar para o login
                        </Link>
                </div>
            </form>
        </div>
    </div>
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
