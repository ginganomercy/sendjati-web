<template>
    <!-- Mobile Overlay -->
    <div v-show="isOpen" @click="$emit('close')" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 md:hidden transition-opacity"></div>

    <!-- Sidebar Content -->
    <div :class="[
        'w-64 bg-white border-r border-gray-200 flex flex-col h-screen z-50 transition-transform duration-300 ease-in-out',
        'fixed top-0 bottom-0 left-0 md:sticky md:translate-x-0',
        isOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
        <!-- Logo & Mobile Close -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <img src="/images/logo.png" alt="Sendjati Logo" class="w-8 h-8 rounded-full object-cover shadow-sm border border-emerald-100" />
                <span class="font-bold text-gray-800 text-sm tracking-tight">Sendjati Cafe</span>
            </div>
            <!-- Close Button -->
            <button @click="$emit('close')" class="md:hidden text-gray-400 hover:text-gray-600 focus:outline-none p-1 -mr-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-4">
            <nav class="space-y-0.5 px-3">

                <!-- Dashboard Dropdown -->
                <div>
                    <button @click="dashboardOpen = !dashboardOpen" :class="[...navClass(isDashboardActive), 'w-full justify-between']">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Dashboard
                        </div>
                        <svg :class="['h-4 w-4 transition-transform', dashboardOpen ? 'rotate-180' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div v-show="dashboardOpen" class="mt-0.5 pl-4 space-y-0.5">
                        <Link @click="$emit('close')" :href="route('dashboard', {department: 'kitchen'})" :class="subNavClass(route().current('dashboard') && $page.props.filters?.department === 'kitchen')">Dashboard Kitchen</Link>
                        <Link @click="$emit('close')" :href="route('dashboard', {department: 'bar'})" :class="subNavClass(route().current('dashboard') && $page.props.filters?.department === 'bar')">Dashboard Bar</Link>
                    </div>
                </div>

                <!-- Master Data Kitchen -->
                <div class="mt-1">
                    <button @click="kitchenOpen = !kitchenOpen" :class="[...navClass(isKitchenActive), 'w-full justify-between']">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Kitchen
                        </div>
                        <svg :class="['h-4 w-4 transition-transform', kitchenOpen ? 'rotate-180' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div v-show="kitchenOpen" class="mt-0.5 pl-4 space-y-0.5">
                        <Link @click="$emit('close')" :href="route('items.index', {department: 'kitchen', type: 'consumable'})" :class="subNavClass(isActiveFilter('kitchen', 'consumable'))">Bahan Baku</Link>
                        <Link @click="$emit('close')" :href="route('items.index', {department: 'kitchen', type: 'equipment'})" :class="subNavClass(isActiveFilter('kitchen', 'equipment'))">Peralatan</Link>
                        <Link @click="$emit('close')" :href="route('categories.index', {department: 'kitchen'})" :class="subNavClass(isActiveCategory('kitchen'))">Kategori</Link>
                    </div>
                </div>

                <!-- Master Data Bar -->
                <div class="mt-1">
                    <button @click="barOpen = !barOpen" :class="[...navClass(isBarActive), 'w-full justify-between']">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>
                            </svg>
                            Bar
                        </div>
                        <svg :class="['h-4 w-4 transition-transform', barOpen ? 'rotate-180' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div v-show="barOpen" class="mt-0.5 pl-4 space-y-0.5">
                        <Link @click="$emit('close')" :href="route('items.index', {department: 'bar', type: 'consumable'})" :class="subNavClass(isActiveFilter('bar', 'consumable'))">Bahan Baku</Link>
                        <Link @click="$emit('close')" :href="route('items.index', {department: 'bar', type: 'equipment'})" :class="subNavClass(isActiveFilter('bar', 'equipment'))">Peralatan</Link>
                        <Link @click="$emit('close')" :href="route('categories.index', {department: 'bar'})" :class="subNavClass(isActiveCategory('bar'))">Kategori</Link>
                    </div>
                </div>

                <!-- Divider -->
                <div class="pt-2 pb-1 px-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Operasional</p>
                </div>

                <!-- Transaksi Dropdown -->
                <div>
                    <button @click="trxOpen = !trxOpen" :class="[...navClass(isTrxActive), 'w-full justify-between']">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Transaksi
                        </div>
                        <svg :class="['h-4 w-4 transition-transform', trxOpen ? 'rotate-180' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div v-show="trxOpen" class="mt-0.5 pl-4 space-y-0.5">
                        <Link @click="$emit('close')" :href="route('transactions.index', {department: 'kitchen'})" :class="subNavClass(route().current('transactions.index') && $page.props.filters?.department === 'kitchen')">Transaksi Kitchen</Link>
                        <Link @click="$emit('close')" :href="route('transactions.index', {department: 'bar'})" :class="subNavClass(route().current('transactions.index') && $page.props.filters?.department === 'bar')">Transaksi Bar</Link>
                    </div>
                </div>

                <!-- Divider -->
                <div class="pt-2 pb-1 px-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Laporan</p>
                </div>

                <!-- Buku Besar / Ledger -->
                <Link @click="$emit('close')" :href="route('ledger.index')" :class="navClass(route().current('ledger.*'))">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Buku Besar
                </Link>

            </nav>
        </div>

        <!-- User Info Bottom -->
        <div class="border-t border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-800 font-bold text-xs shrink-0">
                    {{ $page.props.auth.user?.name?.charAt(0).toUpperCase() || 'A' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-gray-800 truncate">{{ $page.props.auth.user?.name || 'Admin' }}</div>
                    <div class="text-xs text-gray-400 truncate">{{ $page.props.auth.user?.email || 'admin@sendjati.com' }}</div>
                </div>
                <Link @click="$emit('close')" :href="route('logout')" method="post" as="button" class="text-gray-400 hover:text-red-500 transition-colors shrink-0 p-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    }
});

defineEmits(['close']);

const page = usePage();

const isDashboardActive = computed(() => route().current('dashboard'));
const dashboardOpen = ref(isDashboardActive.value || true);

const isTrxActive = computed(() => route().current('transactions.*'));
const trxOpen = ref(isTrxActive.value);

const isKitchenActive = computed(() => {
    return (route().current('items.*') || route().current('categories.*')) && page.props.filters?.department === 'kitchen';
});

const isBarActive = computed(() => {
    return (route().current('items.*') || route().current('categories.*')) && page.props.filters?.department === 'bar';
});

const kitchenOpen = ref(isKitchenActive.value);
const barOpen = ref(isBarActive.value);

function isActiveFilter(dept, type) {
    return route().current('items.*') && page.props.filters?.department === dept && page.props.filters?.type === type;
}

function isActiveCategory(dept) {
    return route().current('categories.*') && page.props.filters?.department === dept;
}

function navClass(active) {
    return [
        'flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors',
        active
            ? 'bg-emerald-50 text-emerald-800 font-semibold'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
    ];
}

function subNavClass(active) {
    return [
        'block px-3 py-1.5 text-sm rounded-md transition-colors',
        active ? 'text-emerald-800 font-semibold bg-emerald-50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'
    ];
}
</script>
