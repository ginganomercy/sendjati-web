<template>
    <div class="min-h-screen bg-gray-50 flex font-sans text-gray-900">
        <Sidebar />
        <div class="flex-1 flex flex-col min-w-0">
            <Topbar />
            <main class="flex-1 p-6 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';
</script>
