<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category.name,
});

const submit = () => {
    form.put(route('categories.update', props.category.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Editar Categoria" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Categoria
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Atualize as informações da categoria.</p>
                </div>
                <div class="mt-4 flex sm:ml-4 sm:mt-0">
                    <Link
                        :href="route('categories')"
                        class="inline-flex items-center gap-x-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all duration-200 ease-in-out"
                    >
                        <ArrowLeftIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        Voltar
                    </Link>
                </div>
            </div>

            <!-- Formulário -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <form @submit.prevent="submit" class="p-6">
                    <div class="max-w-xl animate-fade-in-up">
                        <div>
                            <InputLabel for="name" value="Nome da Categoria" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                placeholder="Digite o nome da categoria"
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="mt-6 flex items-center gap-4">
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
                                    Categoria atualizada com sucesso.
                                </p>
                            </Transition>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
