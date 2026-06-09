<template>
    <div class="min-h-screen bg-gray-50 flex font-sans text-gray-900">
        <Sidebar :is-open="isSidebarOpen" @close="isSidebarOpen = false" />
        <div class="flex-1 flex flex-col min-w-0">
            <Topbar @toggle-sidebar="isSidebarOpen = !isSidebarOpen" />
            <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';

const isSidebarOpen = ref(false);

watch(isSidebarOpen, (val) => {
    if (val) {
        document.body.classList.add('overflow-hidden');
    } else {
        document.body.classList.remove('overflow-hidden');
    }
});
</script>
