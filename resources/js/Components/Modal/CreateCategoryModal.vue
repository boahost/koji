<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
});

const closeModal = () => {
    form.reset();
    emit('close');
};

const submit = () => {
    form.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Nova Categoria
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Adicione uma nova categoria ao sistema.
            </p>

            <form @submit.prevent="submit" class="mt-6">
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
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Salvando...</span>
                        <span v-else>Salvar</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
