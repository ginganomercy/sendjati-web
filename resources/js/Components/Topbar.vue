<template>
    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-10">
        <div class="flex items-center gap-3">
            <!-- Hamburger Menu (Mobile Only) -->
            <button @click="$emit('toggle-sidebar')" class="md:hidden p-2 -ml-2 text-gray-500 hover:text-emerald-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            
            <!-- Department Badge Context -->
            <div v-if="$page.props.filters?.department" class="hidden sm:flex items-center">
                <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm flex items-center gap-1.5">
                    <span v-if="$page.props.filters.department === 'kitchen'">🍳</span>
                    <span v-else>🍸</span>
                    {{ $page.props.filters.department === 'bar' ? 'Bar' : 'Kitchen' }} Area
                </span>
            </div>
            <div v-else class="hidden sm:block">
                <span class="text-sm font-bold text-gray-500">Sendjati Cafe</span>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <img class="h-8 w-8 rounded-full object-cover border border-gray-200 p-0.5" 
                    :src="'https://ui-avatars.com/api/?name=' + ($page.props.auth.user?.name || 'A') + '&background=f3f4f6&color=374151'" 
                    alt="User avatar">
                <span class="text-sm font-medium text-gray-700 truncate max-w-[100px] sm:max-w-[150px]">
                    Hi, {{ $page.props.auth.user?.name || 'Administrator' }}
                </span>
            </div>
        </div>
    </header>
</template>

<script setup>
defineEmits(['toggle-sidebar']);
</script>
