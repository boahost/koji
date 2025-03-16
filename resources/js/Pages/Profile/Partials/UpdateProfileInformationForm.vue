<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { UserIcon } from '@heroicons/vue/24/solid';


defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;
const avatarInput = ref(null);
const previewUrl = ref(user.avatar || null);

const form = useForm({
    name: user.name,
    email: user.email,
    avatar: null,
});

function onAvatarChange(e) {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        previewUrl.value = URL.createObjectURL(file);
    }
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Informações do Perfil
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Atualize suas informações de perfil e endereço de e-mail.
            </p>
        </header>

        <form
            @submit.prevent="form.post(route('profile.update'), {
                preserveScroll: true,
                onSuccess: () => form.reset('password', 'password_confirmation'),
            })"
            class="mt-6 space-y-6"
        >
            <!-- Avatar Upload -->
            <div class="flex flex-col items-center space-y-4 sm:flex-row sm:space-x-6 sm:space-y-0 animate-fade-in-up">
                <div class="relative group cursor-pointer" @click="() => avatarInput.click()">
                    <div v-if="previewUrl" class="relative h-24 w-24 rounded-full overflow-hidden ring-4 ring-white shadow-xl transition-transform duration-300 ease-in-out group-hover:scale-105">
                        <img :src="previewUrl" alt="Avatar" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <span class="text-white text-sm font-medium">Alterar</span>
                        </div>
                    </div>
                    <div v-else class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center ring-4 ring-white shadow-xl transition-transform duration-300 ease-in-out group-hover:scale-105 group-hover:bg-gray-200">
                        <UserIcon class="h-12 w-12 text-gray-400" />
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <span class="text-gray-600 text-sm font-medium">Adicionar</span>
                        </div>
                    </div>
                    <input
                        ref="avatarInput"
                        type="file"
                        class="hidden"
                        accept="image/*"
                        @change="onAvatarChange"
                    >
                </div>
                <div class="space-y-4 flex-1 animate-fade-in-up" style="animation-delay: 100ms">
                    <div>
                        <InputLabel for="name" value="Nome" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="E-mail" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="animate-fade-in-up" style="animation-delay: 200ms">
                <p class="mt-2 text-sm text-gray-800">
                    Seu endereço de e-mail não foi verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Clique aqui para reenviar o e-mail de verificação.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Um novo link de verificação foi enviado para seu e-mail.
                </div>
            </div>

            <div class="flex items-center gap-4 animate-fade-in-up" style="animation-delay: 300ms">
                <PrimaryButton :disabled="form.processing" class="bg-blue-900 hover:bg-blue-800">
                    <span v-if="form.processing">Salvando...</span>
                    <span v-else>Salvar Alterações</span>
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
                        Salvo com sucesso.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
