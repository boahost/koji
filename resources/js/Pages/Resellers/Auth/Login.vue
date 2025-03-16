<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-2xl shadow-lg animate-fadeInUp">
            <div>
                <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                    Área do Revendedor
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Digite suas credenciais para acessar
                </p>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <TransitionGroup
                    enter-active-class="transition-all duration-300"
                    enter-from-class="opacity-0 -translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-300"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                    class="space-y-4 rounded-md"
                >
                    <div :key="'email'">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                        <div class="mt-1">
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm transition-colors"
                                :class="{ 'border-red-500': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>
                    </div>

                    <div :key="'password'">
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <div class="mt-1">
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm transition-colors"
                                :class="{ 'border-red-500': form.errors.password }"
                            />
                            <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                        </div>
                    </div>
                </TransitionGroup>

                <div>
                    <button
                        type="submit"
                        class="group relative flex w-full justify-center rounded-xl bg-primary-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-300 disabled:opacity-75 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Entrando...' : 'Entrar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { TransitionRoot, TransitionChild } from '@headlessui/vue'

const form = useForm({
    email: '',
    password: ''
})

const submit = () => {
    form.post(route('reseller.login'))
}
</script>
