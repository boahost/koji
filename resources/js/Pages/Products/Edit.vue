<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowLeftIcon, XMarkIcon, CheckIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import VMasker from 'vanilla-masker';
import { fadeIn, fadeInUp } from '@/Utils/animations';
import Swal from 'sweetalert2';




const props = defineProps({
    product: {
        type: Object,
        required: true
    },
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
    name: props.product.name || '',
    description: props.product.description || '',
    price: props.product.price || '',
    category_id: props.product.category_id || '',
    department_id: props.product.department_id || '',
    images: [],
    featured_image: props.product.featured_image || 0,
    _method: 'PUT'
});

const imagePreview = ref([]);
const fileInput = ref(null);
const isDragging = ref(false);
const isLoadingInitialImages = ref(true);

onMounted(async () => {
    if (props.product.images && props.product.images.length > 0) {
        try {
            const loadingToast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                showClass: {
                    popup: fadeInUp.enterActiveClass
                },
                hideClass: {
                    popup: fadeInUp.leaveActiveClass
                }
            });

            await loadingToast.fire({
                title: 'Carregando imagens...',
                icon: 'info',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            for (const [index, img] of props.product.images.entries()) {
                await new Promise(resolve => setTimeout(resolve, index * 100));
                imagePreview.value.push(`/storage/${img}`);
            }

            await loadingToast.fire({
                title: 'Imagens carregadas com sucesso!',
                icon: 'success'
            });
        } catch (error) {
            console.error('Erro ao carregar imagens:', error);
            Swal.fire({
                title: 'Erro!',
                text: 'Ocorreu um erro ao carregar as imagens do produto.',
                icon: 'error'
            });
        }
    }
    isLoadingInitialImages.value = false;
});

const formatPrice = (event) => {
    // Remove tudo que não é número
    let value = event.target.value.replace(/[^0-9]/g, '');
    
    // Se não houver valor, retorna vazio
    if (!value) {
        form.price = '';
        return;
    }

    // Converte para número e divide por 100 para ter os centavos
    value = (parseInt(value) / 100).toFixed(2);
    
    // Formata o valor com a máscara
    form.price = VMasker.toMoney(value, {
        prefix: 'R$ ',
        separator: ',',
        delimiter: '.',
        precision: 2
    });
};

const isValidImage = (file) => {
    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const maxSize = 5 * 1024 * 1024; // 5MB

    if (!validTypes.includes(file.type)) {
        Swal.fire({
            title: 'Erro!',
            text: 'Apenas imagens nos formatos JPG, PNG e GIF são permitidas.',
            icon: 'error'
        });
        return false;
    }

    if (file.size > maxSize) {
        Swal.fire({
            title: 'Erro!',
            text: 'O tamanho máximo permitido por imagem é 5MB.',
            icon: 'error'
        });
        return false;
    }

    return true;
};

const handleImageUpload = async (e) => {
    let files;
    if (e.type === 'drop') {
        e.preventDefault();
        isDragging.value = false;
        files = Array.from(e.dataTransfer.files).filter(isValidImage);
    } else {
        files = Array.from(e.target.files).filter(isValidImage);
    }

    if (files.length === 0) {
        if (e.target) e.target.value = '';
        return;
    }

    const existingFiles = Array.from(form.images || []);
    const dt = new DataTransfer();
    existingFiles.forEach(file => dt.items.add(file));
    
    // Mostrar loading com animação
    const loadingToast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        showClass: {
            popup: fadeInUp.enterActiveClass
        },
        hideClass: {
            popup: fadeInUp.leaveActiveClass
        }
    });

    await loadingToast.fire({
        title: 'Processando imagens...',
        icon: 'info',
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        for (const file of files) {
            dt.items.add(file);
            await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.value.push(e.target.result);
                    resolve();
                };
                reader.readAsDataURL(file);
            });
        }

        form.images = dt.files;

        await loadingToast.fire({
            title: 'Imagens adicionadas com sucesso!',
            icon: 'success'
        });
    } catch (error) {
        await Swal.fire({
            title: 'Erro!',
            text: 'Ocorreu um erro ao processar as imagens.',
            icon: 'error'
        });
    }

    e.target.value = '';
};

const removeImage = async (index) => {
    const result = await Swal.fire({
        title: 'Remover imagem?',
        text: 'Você tem certeza que deseja remover esta imagem?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, remover',
        cancelButtonText: 'Cancelar',
        showClass: {
            popup: fadeInUp.enterActiveClass
        },
        hideClass: {
            popup: fadeInUp.leaveActiveClass
        }
    });

    if (result.isConfirmed) {
        try {
            // Remove do preview
            imagePreview.value.splice(index, 1);

            // Atualiza o featured_image se necessário
            if (form.featured_image === index) {
                form.featured_image = imagePreview.value.length > 0 ? 0 : null;
            } else if (form.featured_image > index) {
                form.featured_image--;
            }

            // Atualiza as imagens no formulário
            const dt = new DataTransfer();
            const files = Array.from(form.images || []);
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            form.images = dt.files;

            // Atualiza o input de arquivo
            if (fileInput.value) {
                fileInput.value.value = '';
            }

            await Swal.fire({
                title: 'Imagem removida!',
                text: 'A imagem foi removida com sucesso.',
                icon: 'success',
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                showClass: {
                    popup: fadeIn.enterActiveClass
                },
                hideClass: {
                    popup: fadeIn.leaveActiveClass
                }
            });
        } catch (error) {
            console.error('Erro ao remover imagem:', error);
            await Swal.fire({
                title: 'Erro!',
                text: 'Ocorreu um erro ao remover a imagem.',
                icon: 'error',
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    }
};

const setFeaturedImage = (index) => {
    form.featured_image = index;
    Swal.fire({
        title: 'Imagem Principal',
        text: 'Imagem definida como principal com sucesso!',
        icon: 'success',
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        showClass: {
            popup: fadeIn.enterActiveClass
        },
        hideClass: {
            popup: fadeIn.leaveActiveClass
        }
    });
};

const submit = () => {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('description', form.description);
    formData.append('price', form.price);
    formData.append('category_id', form.category_id);
    formData.append('department_id', form.department_id);
    formData.append('featured_image', form.featured_image);
    formData.append('_method', 'PUT');

    // Adiciona as imagens ao FormData
    if (form.images.length > 0) {
        Array.from(form.images).forEach(file => {
            formData.append('new_images[]', file);
        });
    }

    form.post(route('products.update', props.product.id), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Produto atualizado!',
                text: 'O produto foi atualizado com sucesso.',
                icon: 'success',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                showClass: {
                    popup: fadeInUp.enterActiveClass
                },
                hideClass: {
                    popup: fadeInUp.leaveActiveClass
                }
            });
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join('\n');
            Swal.fire({
                title: 'Erro!',
                text: errorMessages || 'Ocorreu um erro ao atualizar o produto.',
                icon: 'error',
                confirmButtonText: 'OK',
                showClass: {
                    popup: fadeInUp.enterActiveClass
                },
                hideClass: {
                    popup: fadeInUp.leaveActiveClass
                }
            });
        }
    }, {
        forceFormData: true,
        _method: 'PUT'
    });
};
</script>

<template>
    <Head title="Editar Produto" />

    <DashboardLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Produto
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Atualize as informações do produto.</p>
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
                    <div class="form-content">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Coluna da Esquerda -->
                            <div class="space-y-6">
                                <div class="space-y-2">
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

                                <div class="space-y-2">
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

                                <div class="space-y-2">
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

                                <div class="space-y-2">
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
                                    <div 
                                        class="flex justify-center rounded-xl border border-dashed border-gray-900/25 px-6 py-10 transition-all duration-200"
                                        :class="{ 'border-primary-500 bg-primary-50': isDragging }"
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="handleImageUpload"
                                    >
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                            </svg>
                                            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                                <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-primary-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-primary-600 focus-within:ring-offset-2 hover:text-primary-500">
                                                    <span>Enviar imagens</span>
                                                    <input
                                                        ref="fileInput"
                                                        id="file-upload"
                                                        name="file-upload"
                                                        type="file"
                                                        multiple
                                                        accept="image/jpeg,image/png,image/gif"
                                                        class="sr-only"
                                                        @change="handleImageUpload"
                                                    />
                                                </label>
                                                <p class="pl-1">ou arraste e solte</p>
                                            </div>
                                            <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF até 5MB</p>
                                        </div>
                                    </div>

                                    <!-- Preview das Imagens -->
                                    <!-- Preview das Imagens -->
                                    <div class="relative mt-4">
                                        <div v-if="isLoadingInitialImages" class="flex items-center justify-center py-8">
                                            <div class="text-center">
                                                <svg class="mx-auto animate-spin h-8 w-8 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-500">Carregando imagens do produto...</p>
                                            </div>
                                        </div>
                                        <TransitionGroup
                                            v-else-if="imagePreview.length > 0"
                                            :enter-active-class="fadeInUp.enterActiveClass"
                                            :enter-from-class="fadeInUp.enterFromClass"
                                            :enter-to-class="fadeInUp.enterToClass"
                                            :leave-active-class="fadeInUp.leaveActiveClass"
                                            :leave-from-class="fadeInUp.leaveFromClass"
                                            :leave-to-class="fadeInUp.leaveToClass"
                                            tag="div"
                                            class="grid grid-cols-2 sm:grid-cols-3 gap-4"
                                        >
                                                <div 
                                                    v-for="(preview, index) in imagePreview" 
                                                    :key="preview"
                                                    class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 hover:bg-gray-200 transition-all duration-200 ease-in-out cursor-pointer"
                                                    :style="{ transitionDelay: `${index * 100}ms` }"
                                                    :class="{ 'ring-2 ring-primary-500 ring-offset-2': form.featured_image === index }"
                                                    @click="setFeaturedImage(index)"
                                                >
                                                    <img 
                                                        :src="preview" 
                                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                                    />
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300">
                                                        <span class="text-white text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                            {{ form.featured_image === index ? 'Imagem Principal' : 'Definir como Principal' }}
                                                        </span>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        @click.stop="removeImage(index)"
                                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-all duration-300 transform opacity-0 group-hover:opacity-100"
                                                    >
                                                        <XMarkIcon class="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </TransitionGroup>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Link
                            :href="route('products.index')"
                            class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-300 flex items-center gap-2"
                            :disabled="form.processing"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>