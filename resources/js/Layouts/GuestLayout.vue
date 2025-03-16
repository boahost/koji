<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="relative isolate overflow-hidden">
            <!-- Fundo decorativo -->
            <div class="absolute inset-0 -z-10 overflow-hidden">
                <div class="absolute h-full w-full bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-30"></div>
                <div
                    class="absolute left-[calc(50%-4rem)] top-10 -z-10 transform-gpu blur-3xl sm:left-[calc(50%-18rem)] lg:left-48 lg:top-[calc(50%-10rem)] xl:left-[calc(50%-24rem)]"
                    aria-hidden="true"
                >
                    <div
                        class="aspect-[1108/632] w-[69.25rem] bg-gradient-to-r from-primary-500/20 via-primary-300/20 to-primary-200/20"
                        style="clip-path: polygon(73.6% 51.7%, 91.7% 11.8%, 100% 46.4%, 97.4% 82.2%, 92.5% 84.9%, 75.7% 64%, 55.3% 47.5%, 46.5% 49.4%, 45% 62.9%, 50.3% 87.2%, 21.3% 64.1%, 0.1% 100%, 5.4% 51.1%, 21.4% 63.9%, 58.9% 0.2%, 73.6% 51.7%)"
                    ></div>
                </div>
            </div>

            <!-- Conteúdo principal -->
            <div class="flex min-h-screen flex-col items-center justify-center py-12 sm:px-6 lg:px-8">
                <div class="w-full sm:mx-auto sm:max-w-md">
                    <TransitionGroup
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="transform opacity-0 translate-y-4"
                        enter-to-class="opacity-100 translate-y-0"
                        move-class="transition-transform duration-300"
                        appear
                    >
                        <!-- Logo -->
                        <div class="text-center">
                            <Link href="/" class="inline-block">
                                <ApplicationLogo class="h-16 w-16 text-primary-600 transition-transform duration-200 ease-in-out hover:scale-110" />
                            </Link>
                        </div>

                        <!-- Card do conteúdo -->
                        <div
                            class="mt-10 w-full overflow-hidden bg-white/80 backdrop-blur-xl px-6 py-8 shadow-xl ring-1 ring-gray-900/5 sm:rounded-2xl sm:px-8 transition-all duration-300 ease-in-out hover:shadow-2xl hover:bg-white/90"
                        >
                            <slot />
                        </div>
                    </TransitionGroup>
                </div>
            </div>
        </div>
    </div>
</template>
