<template>
    <Head title="Buku Besar" />
    <AppLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buku Besar</h1>
                <p class="text-sm text-gray-500 mt-1">Audit trail permanen seluruh mutasi stok inventaris</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Empty State -->
            <div v-if="!ledgers.data.length" class="flex flex-col items-center justify-center py-20 text-gray-400">
                <svg class="h-14 w-14 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-sm font-medium">Belum ada riwayat</p>
                <p class="text-xs mt-1">Data akan otomatis tercatat saat ada transaksi atau penambahan barang baru</p>
            </div>

            <!-- Table Data -->
            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu & Tanggal</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Referensi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mutasi</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="ledger in ledgers.data" :key="ledger.id" class="hover:bg-gray-50 transition-colors">

                        <!-- Waktu -->
                        <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ formatDate(ledger.created_at) }}
                        </td>

                        <!-- Referensi -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span v-if="ledger.transaction?.reference_number"
                                class="font-mono text-xs text-indigo-600 font-semibold">
                                {{ ledger.transaction.reference_number }}
                            </span>
                            <span v-else class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 text-xs font-medium">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                                Sistem
                            </span>
                        </td>

                        <!-- Nama Barang -->
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800 text-sm">{{ ledger.item?.name }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ ledger.item?.sku }}</p>
                        </td>

                        <!-- Mutasi Badge -->
                        <td class="px-5 py-4 text-center">
                            <span :class="[
                                'inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold min-w-[52px]',
                                ledger.mutation_type === 'increment'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-red-100 text-red-700'
                            ]">
                                {{ ledger.mutation_type === 'increment' ? '+' : '-' }}{{ ledger.quantity }}
                            </span>
                        </td>

                        <!-- Sisa Stok -->
                        <td class="px-5 py-4 text-center">
                            <span class="text-base font-bold text-gray-800">{{ ledger.balance_after }}</span>
                        </td>

                        <!-- Keterangan -->
                        <td class="px-5 py-4">
                            <span class="text-xs text-gray-500">{{ ledger.notes }}</span>
                        </td>

                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="ledgers.data.length" class="px-5 py-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Menampilkan {{ ledgers.from }}–{{ ledgers.to }} dari {{ ledgers.total }} entri</span>
                <div class="flex gap-1">
                    <template v-for="(link, key) in ledgers.links" :key="key">
                        <Link v-if="link.url" :href="link.url" v-html="link.label"
                            :class="['px-2 py-1 rounded', link.active ? 'bg-emerald-500 text-white' : 'hover:bg-gray-100']" />
                        <span v-else v-html="link.label"
                            class="px-2 py-1 rounded text-gray-300 cursor-not-allowed" />
                    </template>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    ledgers: Object
});

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}
</script>
