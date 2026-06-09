<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0 bg-cover bg-center"
        style="background-image: url('/images/bg-login.png');"
    >
        <!-- Overlay Gelap -->
        <div class="absolute inset-0 bg-black/50 z-0"></div>

        <div class="relative z-10">
            <Link href="/">
                <ApplicationLogo class="h-24 w-24 fill-current text-white drop-shadow-md transition-transform duration-300 hover:scale-105" />
            </Link>
        </div>

        <div
            class="relative z-10 mt-6 w-full overflow-hidden bg-white/80 backdrop-blur-md px-8 py-8 shadow-2xl sm:max-w-md sm:rounded-2xl border border-white/40"
        >
            <slot />
        </div>
    </div>
</template>
