<template>
    <Head title="Transaksi" />
    <AppLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Transaksi {{ filters?.department === 'bar' ? 'Bar' : (filters?.department === 'kitchen' ? 'Kitchen' : '') }}</h1>
                <p class="text-sm text-gray-500 mt-1">Semua mutasi barang masuk, keluar, retur, dan opname</p>
            </div>
            <button @click="openSlideOver()"
                class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium text-sm transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Transaksi Baru
            </button>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-wrap gap-2 mb-5">
            <button v-for="tab in filterTabs" :key="tab.value"
                @click="applyFilter(tab.value)"
                :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors border',
                    activeFilter === tab.value
                        ? tab.activeClass
                        : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
                ]">
                {{ tab.label }}
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
            <!-- Overlay Translucent if panel open -->
            <div v-if="slideOverOpen" class="absolute inset-0 bg-gray-500 bg-opacity-20 z-10 transition-opacity"></div>
            
            <!-- Empty State -->
            <div v-if="!transactions.data.length" class="flex flex-col items-center justify-center py-20 text-gray-400">
                <svg class="h-14 w-14 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium">Belum ada transaksi</p>
                <p class="text-xs mt-1">Mulai dengan menekan tombol Buat Transaksi Baru</p>
            </div>

            <!-- Table Data -->
            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Referensi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="trx in transactions.data" :key="trx.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 font-mono text-xs text-gray-600">{{ trx.reference_number }}</td>
                        <td class="px-5 py-4">
                            <span :class="['px-2.5 py-1 rounded-full text-xs font-bold tracking-wide uppercase', typeStyle[trx.type]?.badge]">
                                {{ typeStyle[trx.type]?.label }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <!-- Detail barang per baris -->
                            <div class="space-y-0.5">
                                <div v-for="d in trx.details" :key="d.id" class="text-gray-700 text-xs flex items-center">
                                    <span class="font-medium truncate max-w-[200px]">{{ d.item?.name }}</span>
                                    <span class="text-gray-400 ml-1">× {{ d.quantity }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-500 whitespace-nowrap text-xs">{{ formatDate(trx.created_at) }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 text-xs text-emerald-700 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                {{ trx.status.charAt(0).toUpperCase() + trx.status.slice(1) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="transactions.data.length" class="px-5 py-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 relative z-20 bg-white">
                <span>Menampilkan {{ transactions.from }}–{{ transactions.to }} dari {{ transactions.total }} data</span>
                <div class="flex gap-1">
                    <Link v-for="link in transactions.links" :key="link.label"
                        :href="link.url || '#'"
                        :class="['px-2 py-1 rounded', link.active ? 'bg-emerald-500 text-white' : 'hover:bg-gray-100', !link.url ? 'opacity-40 cursor-not-allowed' : '']"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <!-- Slide Over Panel (Buat Transaksi) -->
        <div v-if="slideOverOpen" class="fixed inset-0 overflow-hidden z-50">
            <div class="absolute inset-0 bg-gray-800 bg-opacity-40 backdrop-blur-sm transition-opacity" @click="closeSlideOver()"></div>
            <div class="fixed inset-y-0 right-0 max-w-2xl w-full flex">
                <div class="w-full h-full bg-white shadow-2xl flex flex-col transform transition-transform border-l border-gray-200">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Buat Transaksi Baru</h2>
                            <p class="text-xs text-gray-500 mt-1">Lengkapi form mutasi di bawah ini</p>
                        </div>
                        <button @click="closeSlideOver()" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 relative">
                        <!-- Flash Error -->
                        <div v-if="$page.props.flash?.error" class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl text-sm text-red-700 font-medium">
                            {{ $page.props.flash.error }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-8 pb-10">
                            <!-- Step 1: Tipe Mutasi -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xs font-bold">1</span>
                                    Tipe Mutasi
                                </h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <label v-for="type in ['in', 'out', 'return', 'opname']" :key="type"
                                        class="relative flex cursor-pointer flex-col p-4 border rounded-xl transition-all duration-200"
                                        :class="form.type === type ? 'border-emerald-500 ring-1 ring-emerald-500 bg-emerald-50 shadow-sm' : 'border-gray-200 hover:border-emerald-200 hover:bg-gray-50'">
                                        <input type="radio" :value="type" v-model="form.type" class="sr-only">
                                        <span class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                            <svg v-if="form.type === type" class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            {{ typeLabels[type]?.title }}
                                        </span>
                                        <span class="text-xs text-gray-500 mt-1 pl-6">{{ typeLabels[type]?.subtitle }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Step 2: Detail Barang -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xs font-bold">2</span>
                                        Detail Barang
                                    </h3>
                                    <button type="button" @click="addLineItem" class="text-xs font-medium text-emerald-700 hover:text-white flex items-center gap-1 bg-emerald-100 hover:bg-emerald-600 px-3 py-1.5 rounded-lg transition-all duration-200 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Tambah Baris
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div v-for="(detail, index) in form.details" :key="index" class="flex flex-col sm:flex-row items-start sm:items-end gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200 transition-colors hover:border-gray-300">
                                        <div class="flex-1 w-full">
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pilih Barang</label>
                                            <select v-model="detail.item_id" @change="autoFillPrice(index)" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                                <option value="" disabled>-- Cari Barang --</option>
                                                <option v-for="item in filteredItems" :key="item.id" :value="item.id">
                                                    [{{ item.sku }}] {{ item.name }} (Stok: {{ item.current_stock }} {{ item.unit?.name }})
                                                </option>
                                            </select>
                                        </div>
                                        <div class="w-full sm:w-28">
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Kuantitas</label>
                                            <input type="number" min="1" v-model="detail.quantity" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-center font-bold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                        </div>
                                        <div class="w-full sm:w-40" v-if="form.type === 'in'">
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Hrg Beli/Sat</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                                                <input type="number" min="0" step="100" v-model="detail.unit_price" class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm font-bold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                            </div>
                                        </div>
                                        <button type="button" @click="removeLineItem(index)" :disabled="form.details.length === 1" class="p-2.5 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-lg disabled:opacity-30 disabled:cursor-not-allowed transition-colors border border-transparent hover:border-red-100 flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div v-if="Object.keys(form.errors).length > 0" class="mt-4 p-4 bg-red-50 rounded-xl border border-red-100 text-sm text-red-600 font-medium flex items-center gap-3">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Pastikan form terisi dengan benar (Error validasi server).
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-end gap-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] relative z-10">
                        <button type="button" @click="closeSlideOver()" class="px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                        <button type="button" @click="submit" :disabled="form.processing" class="inline-flex items-center px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:ring-emerald-200 text-white text-sm font-bold rounded-xl shadow-sm disabled:opacity-60 transition-all">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Transaksi' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    transactions: Object,
    items: Array,
    filters: Object,
});

// -- List and Filter logic --
const typeStyle = {
    in:     { label: 'Masuk',   badge: 'bg-emerald-100 text-emerald-700' },
    out:    { label: 'Keluar',  badge: 'bg-red-100 text-red-700' },
    return: { label: 'Retur',   badge: 'bg-emerald-100 text-emerald-700' },
    opname: { label: 'Opname',  badge: 'bg-blue-100 text-blue-700' },
};

const filterTabs = [
    { value: '',       label: 'Semua',  activeClass: 'bg-gray-800 border-gray-800 text-white' },
    { value: 'in',    label: 'Masuk',  activeClass: 'bg-emerald-500 border-emerald-500 text-white' },
    { value: 'out',   label: 'Keluar', activeClass: 'bg-red-500 border-red-500 text-white' },
    { value: 'return',label: 'Retur',  activeClass: 'bg-emerald-500 border-emerald-500 text-white' },
    { value: 'opname',label: 'Opname', activeClass: 'bg-blue-500 border-blue-500 text-white' },
];

const activeFilter = computed(() => props.filters?.type ?? '');

function applyFilter(type) {
    router.get(route('transactions.index'), { type, department: props.filters?.department }, {
        preserveState: true,
        replace: true,
    });
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
}

// -- Slide Over and Form logic --
const slideOverOpen = ref(false);

const typeLabels = {
    in:     { title: 'Barang Masuk',  subtitle: 'Catat restok dari supplier' },
    out:    { title: 'Barang Keluar', subtitle: 'Catat pemakaian internal' },
    return: { title: 'Retur Supplier',subtitle: 'Barang rusak / lebih' },
    opname: { title: 'Stok Opname',   subtitle: 'Penyesuaian fisik vs sistem' },
};

const form = useForm({
    type: 'in',
    details: [
        { item_id: '', quantity: 1, unit_price: 0 }
    ]
});

const filteredItems = computed(() => {
    // Jika keluar/retur, biasanya peralatan jarang, tapi kita tetap memunculkan jika diminta
    // Sesuai logika asli, 'out' & 'return' tidak memunculkan equipment
    if (['out', 'return'].includes(form.type)) {
        return props.items.filter(item => item.type !== 'equipment');
    }
    return props.items;
});

function openSlideOver() {
    form.reset();
    form.clearErrors();
    slideOverOpen.value = true;
}

function closeSlideOver() {
    slideOverOpen.value = false;
}

function addLineItem() {
    form.details.push({ item_id: '', quantity: 1, unit_price: 0 });
}

function removeLineItem(index) {
    if (form.details.length > 1) {
        form.details.splice(index, 1);
    }
}

// Fitur Pintar: Auto Fill Harga
function autoFillPrice(index) {
    if (form.type !== 'in') return;
    const detail = form.details[index];
    const item = props.items.find(i => i.id === detail.item_id);
    if (item && item.harga_satuan) {
        detail.unit_price = item.harga_satuan;
    }
}

function submit() {
    form.post(route('transactions.store'), {
        onSuccess: () => {
            closeSlideOver();
        }
    });
}
</script>
