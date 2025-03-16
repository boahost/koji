<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowLeftIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import VMasker from 'vanilla-masker';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    departments: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: '',
    description: '',
    price: '',
    category_id: '',
    department_id: '',
    images: [],
    featured_image: 0
});

const imagePreview = ref([]);
const fileInput = ref(null);

const formatPrice = (event) => {
    let value = event.target.value.replace(/[^0-9]/g, '');
    value = (parseFloat(value) / 100).toFixed(2);
    form.price = VMasker.toMoney(value, {
        prefix: 'R$ ',
        separator: ',',
        delimiter: '.',
        precision: 2
    });
};

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    form.images = e.target.files;
    
    imagePreview.value = [];
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    imagePreview.value.splice(index, 1);
    const dt = new DataTransfer();
    const files = Array.from(form.images);
    files.splice(index, 1);
    files.forEach(file => dt.items.add(file));
    form.images = dt.files;
    if (form.featured_image === index) {
        form.featured_image = 0;
    } else if (form.featured_image > index) {
        form.featured_image--;
    }
    fileInput.value.files = dt.files;
};

const setFeaturedImage = (index) => {
    form.featured_image = index;
};

const submit = () => {
    form.post(route('products.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Novo Produto" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Novo Produto
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Crie um novo produto no sistema.</p>
                </div>
                <div class="mt-4 flex sm:ml-4 sm:mt-0">
                    <Link
                        :href="route('products.index')"
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up">
                        <!-- Coluna da Esquerda -->
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nome do Produto" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    placeholder="Digite o nome do produto"
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Descrição" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-xl border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 transition-shadow duration-200 ease-in-out"
                                    placeholder="Digite a descrição do produto"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="price" value="Preço" />
                                <TextInput
                                    id="price"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    required
                                    placeholder="R$ 0,00"
                                    @input="formatPrice"
                                />
                                <InputError :message="form.errors.price" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="category_id" value="Categoria" />
                                <select
                                    id="category_id"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full rounded-xl border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 transition-shadow duration-200 ease-in-out"
                                    required
                                >
                                    <option value="" disabled>Selecione uma categoria</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.category_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="department_id" value="Departamento" />
                                <select
                                    id="department_id"
                                    v-model="form.department_id"
                                    class="mt-1 block w-full rounded-xl border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 transition-shadow duration-200 ease-in-out"
                                    required
                                >
                                    <option value="" disabled>Selecione um departamento</option>
                                    <option v-for="department in departments" :key="department.id" :value="department.id">
                                        {{ department.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.department_id" class="mt-2" />
                            </div>
                        </div>

                        <!-- Coluna da Direita - Upload de Imagens -->
                        <div class="space-y-6">
                            <div>
                                <InputLabel value="Imagens do Produto" />
                                <div class="space-y-4">
                                    <!-- Área de Upload -->
                                    <div class="flex justify-center rounded-xl border border-dashed border-gray-900/25 px-6 py-10">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                            </svg>
                                            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                                <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-primary-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-primary-600 focus-within:ring-offset-2 hover:text-primary-500">
                                                    <span>Fazer upload de imagens</span>
                                                    <input 
                                                        ref="fileInput"
                                                        id="file-upload" 
                                                        name="file-upload" 
                                                        type="file" 
                                                        class="sr-only" 
                                                        multiple
                                                        accept="image/*"
                                                        @change="handleImageUpload"
                                                    >
                                                </label>
                                                <p class="pl-1">ou arraste e solte</p>
                                            </div>
                                            <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF até 10MB</p>
                                        </div>
                                    </div>

                                    <!-- Preview das Imagens -->
                                    <div v-if="imagePreview.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4 animate-fade-in-up">
                                        <div 
                                            v-for="(preview, index) in imagePreview" 
                                            :key="index"
                                            class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 hover:bg-gray-200 transition-all duration-200 ease-in-out"
                                        >
                                            <img 
                                                :src="preview" 
                                                class="h-full w-full object-cover"
                                                :class="{ 'ring-2 ring-primary-500 ring-offset-2': form.featured_image === index }"
                                            >
                                            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2">
                                                <button 
                                                    @click="setFeaturedImage(index)"
                                                    type="button"
                                                    class="p-1.5 text-white hover:text-yellow-400 transition-colors duration-200"
                                                    :class="{ 'text-yellow-400': form.featured_image === index }"
                                                    title="Definir como imagem principal"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                    </svg>
                                                </button>
                                                <button 
                                                    @click="removeImage(index)"
                                                    type="button"
                                                    class="p-1.5 text-white hover:text-red-400 transition-colors duration-200"
                                                    title="Remover imagem"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.images" class="mt-2" />
                                    <InputError :message="form.errors.featured_image" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                class="bg-primary-600 hover:bg-primary-500"
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
                                    Produto criado com sucesso.
                                </p>
                            </Transition>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
