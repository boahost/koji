<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
                <TransitionChild
                    enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <TransitionChild
                        enter="transition ease-in-out duration-300 transform"
                        enter-from="-translate-x-full"
                        enter-to="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leave-from="translate-x-0"
                        leave-to="-translate-x-full"
                    >
                        <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
                            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-br from-blue-900 to-blue-950 px-6 pb-4 shadow-xl">
                                <div class="flex h-16 shrink-0 items-center justify-center mt-4">
                                    <Link href="/" class="flex items-center space-x-2">
                                        <div class="rounded-xl bg-white/10 p-2.5">
                                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-white text-xl font-bold">Projeto-DF</span>
                                    </Link>
                                </div>
                                <nav class="flex flex-1 flex-col pt-8">
                                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                        <li>
                                            <ul role="list" class="space-y-2">
                                                <li v-for="item in navigation" :key="item.name">
                                                    <template v-if="item.submenu">
                                                        <button
                                                            type="button"
                                                            @click="toggleSubmenu(item.name)"
                                                            :class="[
                                                                item.current
                                                                    ? 'bg-white/10 text-white'
                                                                    : 'text-white/70 hover:text-white hover:bg-white/10',
                                                                'group flex items-center justify-between w-full rounded-xl px-4 py-3 text-sm font-semibold leading-6 transition-all duration-150 ease-in-out'
                                                            ]"
                                                        >
                                                            <div class="flex items-center gap-x-3">
                                                                <component
                                                                    :is="item.icon"
                                                                    :class="[
                                                                        item.current ? 'text-white' : 'text-white/70 group-hover:text-white',
                                                                        'h-5 w-5 shrink-0 transition-colors duration-150 ease-in-out'
                                                                    ]"
                                                                    aria-hidden="true"
                                                                />
                                                                {{ item.name }}
                                                            </div>
                                                            <svg
                                                                class="h-5 w-5 shrink-0 text-white/70 group-hover:text-white transition-all duration-200 ease-in-out"
                                                                :class="{ 'rotate-180': isSubmenuOpen(item.name) }"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <Transition
                                                            enter-active-class="transition ease-out duration-200"
                                                            enter-from-class="opacity-0 -translate-y-1"
                                                            enter-to-class="opacity-100 translate-y-0"
                                                            leave-active-class="transition ease-in duration-150"
                                                            leave-from-class="opacity-100 translate-y-0"
                                                            leave-to-class="opacity-0 -translate-y-1"
                                                        >
                                                            <div v-if="isSubmenuOpen(item.name)" class="ml-8 mt-2 space-y-1">
                                                                <Link
                                                                    v-for="subitem in item.submenu"
                                                                    :key="subitem.name"
                                                                    :href="subitem.href"
                                                                    :class="[
                                                                        subitem.current
                                                                            ? 'bg-white/10 text-white'
                                                                            : 'text-white/70 hover:text-white hover:bg-white/10',
                                                                        'group flex items-center gap-x-3 rounded-xl px-4 py-2 text-sm font-medium leading-6 transition-all duration-150 ease-in-out'
                                                                    ]"
                                                                >
                                                                    {{ subitem.name }}
                                                                </Link>
                                                            </div>
                                                        </Transition>
                                                    </template>
                                                    <Link
                                                        v-else
                                                        :href="item.href"
                                                        :class="[
                                                            item.current
                                                                ? 'bg-white/10 text-white'
                                                                : 'text-white/70 hover:text-white hover:bg-white/10',
                                                            'group flex items-center gap-x-3 rounded-xl px-4 py-3 text-sm font-semibold leading-6 transition-all duration-150 ease-in-out'
                                                        ]"
                                                    >
                                                        <component
                                                            :is="item.icon"
                                                            :class="[
                                                                item.current ? 'text-white' : 'text-white/70 group-hover:text-white',
                                                                'h-5 w-5 shrink-0 transition-colors duration-150 ease-in-out'
                                                            ]"
                                                            aria-hidden="true"
                                                        />
                                                        {{ item.name }}
                                                    </Link>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="mt-auto pb-8">
                                            <Link
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                                class="group flex w-full items-center gap-x-3 rounded-xl px-4 py-3 text-sm font-semibold leading-6 text-white/70 hover:bg-white/10 hover:text-white transition-all duration-150 ease-in-out"
                                            >
                                                <ArrowLeftOnRectangleIcon class="h-5 w-5 shrink-0 text-white/70 group-hover:text-white transition-colors duration-150 ease-in-out" aria-hidden="true" />
                                                Sair
                                            </Link>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-br from-blue-900 to-blue-950 px-6 pb-4 shadow-xl">
                <div class="flex h-16 shrink-0 items-center justify-center mt-4">
                    <Link href="/" class="flex items-center space-x-2">
                        <div class="rounded-xl bg-white/10 p-2.5">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-white text-xl font-bold">Projeto-DF</span>
                    </Link>
                </div>
                <nav class="flex flex-1 flex-col pt-8">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="space-y-2">
                                <li v-for="item in navigation" :key="item.name">
                                    <template v-if="item.submenu">
                                        <button
                                            type="button"
                                            @click="toggleSubmenu(item.name)"
                                            :class="[
                                                item.current
                                                    ? 'bg-white/20 text-white shadow-lg'
                                                    : 'text-white/80 hover:text-white hover:bg-white/10',
                                                'group flex items-center justify-between w-full rounded-xl px-4 py-3 text-sm font-semibold leading-6 transition-all duration-300 ease-in-out hover:shadow-md'
                                            ]"
                                        >
                                            <div class="flex items-center gap-x-3">
                                                <component
                                                    :is="item.icon"
                                                    :class="[
                                                        item.current ? 'text-white scale-110' : 'text-white/80 group-hover:text-white group-hover:scale-110',
                                                        'h-5 w-5 shrink-0 transition-all duration-300 ease-in-out'
                                                    ]"
                                                    aria-hidden="true"
                                                />
                                                {{ item.name }}
                                            </div>
                                            <svg
                                                class="h-5 w-5 shrink-0 text-white/80 group-hover:text-white transition-all duration-300 ease-in-out"
                                                :class="{ 'rotate-180': isSubmenuOpen(item.name) }"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                        <Transition
                                            enter-active-class="transition ease-out duration-200"
                                            enter-from-class="opacity-0 -translate-y-1"
                                            enter-to-class="opacity-100 translate-y-0"
                                            leave-active-class="transition ease-in duration-150"
                                            leave-from-class="opacity-100 translate-y-0"
                                            leave-to-class="opacity-0 -translate-y-1"
                                        >
                                            <div v-if="isSubmenuOpen(item.name)" class="ml-8 mt-2 space-y-1">
                                                <Link
                                                    v-for="subitem in item.submenu"
                                                    :key="subitem.name"
                                                    :href="subitem.href"
                                                    :class="[
                                                        subitem.current
                                                            ? 'bg-white/20 text-white shadow-lg'
                                                            : 'text-white/80 hover:text-white hover:bg-white/10',
                                                        'group flex items-center gap-x-3 rounded-xl px-4 py-2 text-sm font-medium leading-6 transition-all duration-300 ease-in-out hover:shadow-md'
                                                    ]"
                                                >
                                                    {{ subitem.name }}
                                                </Link>
                                            </div>
                                        </Transition>
                                    </template>
                                    <Link
                                        v-else
                                        :href="item.href"
                                        :class="[
                                            item.current
                                                ? 'bg-white/20 text-white shadow-lg'
                                                : 'text-white/80 hover:text-white hover:bg-white/10',
                                            'group flex items-center gap-x-3 rounded-xl px-4 py-3 text-sm font-semibold leading-6 transition-all duration-300 ease-in-out hover:shadow-md'
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                item.current ? 'text-white scale-110' : 'text-white/80 group-hover:text-white group-hover:scale-110',
                                                'h-5 w-5 shrink-0 transition-all duration-300 ease-in-out'
                                            ]"
                                            aria-hidden="true"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        <li class="mt-auto pb-8">
                            <Link
                                :href="route('customer.logout')"
                                method="post"
                                as="button"
                                class="group flex w-full items-center gap-x-3 rounded-xl px-4 py-3 text-sm font-semibold leading-6 text-white/80 hover:bg-white/10 hover:text-white transition-all duration-300 ease-in-out hover:shadow-md"
                            >
                                <ArrowLeftOnRectangleIcon class="h-5 w-5 shrink-0 text-white/80 group-hover:text-white group-hover:scale-110 transition-all duration-300 ease-in-out" aria-hidden="true" />
                                Sair
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-72">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white/80 backdrop-blur-sm px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Abrir menu</span>
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1">
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <span class="text-sm font-medium text-gray-900">
                                {{ new Date().toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true" />
                        <div class="flex items-center gap-x-2">
                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-primary-700">{{ $page.props.auth.user.name.charAt(0) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import {
    Dialog,
    DialogPanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import {
    Bars3Icon,
    HomeIcon,
    ShoppingBagIcon,
    ArrowLeftOnRectangleIcon,
    UserIcon,
    TagIcon,
    BuildingOfficeIcon,
    UsersIcon,
    UserGroupIcon,
    TruckIcon
} from '@heroicons/vue/24/outline'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = ref(false)

const openMenus = ref([]);

const toggleSubmenu = (itemName) => {
    const index = openMenus.value.indexOf(itemName);
    if (index === -1) {
        openMenus.value.push(itemName);
    } else {
        openMenus.value.splice(index, 1);
    }
};

const isSubmenuOpen = (itemName) => {
    return openMenus.value.includes(itemName);
};

const navigation = [
    { name: 'Início', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
    {
        name: 'Produtos',
        icon: ShoppingBagIcon,
        current: route().current('products.*'),
        submenu: [
            { name: 'Listar', href: route('products.index'), current: route().current('products.index') },
            { name: 'Cadastrar', href: route('products.create'), current: route().current('products.create') },
        ]
    },
    {
        name: 'Categorias',
        icon: TagIcon,
        current: route().current('categories.*'),
        submenu: [
            { name: 'Listar', href: route('categories'), current: route().current('categories') },
            { name: 'Cadastrar', href: route('categories.create'), current: route().current('categories.create') },
        ]
    },
    {
        name: 'Departamentos',
        icon: BuildingOfficeIcon,
        current: route().current('departments.*'),
        submenu: [
            { name: 'Listar', href: route('departments'), current: route().current('departments') },
            { name: 'Cadastrar', href: route('departments.create'), current: route().current('departments.create') },
        ]
    },
    {
        name: 'Fretes',
        icon: TruckIcon,
        current: route().current('shipping-methods.*'),
        submenu: [
            { name: 'Listar', href: route('shipping-methods.index'), current: route().current('shipping-methods.index') },
            { name: 'Cadastrar', href: route('shipping-methods.create'), current: route().current('shipping-methods.create') },
        ]
    },
    {
        name: 'Revendedores',
        icon: UsersIcon,
        current: route().current('resellers.*'),
        submenu: [
            { name: 'Listar', href: route('resellers.index'), current: route().current('resellers.index') },
            { name: 'Cadastrar', href: route('resellers.create'), current: route().current('resellers.create') },
        ]
    },
    {
        name: 'Clientes',
        icon: UserGroupIcon,
        current: route().current('customers.*'),
        submenu: [
            { name: 'Listar', href: route('customers.index'), current: route().current('customers.index') },
            { name: 'Cadastrar', href: route('customers.create'), current: route().current('customers.create') },
        ]
    },
    { name: 'Perfil', href: route('profile.edit'), icon: UserIcon, current: route().current('profile.*') },
]
</script>
