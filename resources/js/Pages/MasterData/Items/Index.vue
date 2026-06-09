<template>
    <Head title="Daftar Barang" />
    <AppLayout>
        <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">{{ $page.props.flash.success }}</div>
        <div v-if="$page.props.flash?.error" class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">{{ $page.props.flash.error }}</div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ filters?.type === 'equipment' ? 'Peralatan' : 'Bahan Baku' }} {{ filters?.department === 'bar' ? 'Bar' : 'Kitchen' }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data inventaris</p>
            </div>
            <button @click="openModal()" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium text-sm transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Barang
            </button>
        </div>

        <!-- Filter -->
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            <input v-model="search" @input="applyFilter" type="text" placeholder="Cari nama / SKU..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
            <select v-model="categoryFilter" @change="applyFilter" class="border border-gray-300 rounded-lg px-3 py-2 pr-10 min-w-[180px] w-full sm:w-auto text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm hidden md:table">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Min.</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="item in items.data" :key="item.id"
                        class="hover:bg-emerald-50/40 transition-colors cursor-pointer group"
                        @click="openHistory(item)"
                        title="Klik untuk melihat riwayat mutasi stok">
                        <td class="px-5 py-3 font-mono text-gray-500 text-xs">{{ item.sku }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ item.name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span :class="[
                                    'px-2 py-0.5 text-[10px] font-bold rounded-full',
                                    item.type === 'equipment' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'
                                ]">{{ item.type === 'equipment' ? 'Alat (Aset)' : 'Bahan Baku' }}</span>
                                <span class="text-xs text-gray-400">Klik lihat riwayat</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ item.category?.name }}</td>
                        <td class="px-5 py-3 text-center">
                            <div class="inline-flex flex-col items-center gap-1">
                                <span :class="[
                                    'text-base font-bold',
                                    item.current_stock === 0 ? 'text-red-600' :
                                    item.current_stock <= item.minimum_stock ? 'text-emerald-600' : 'text-gray-800'
                                ]">{{ item.current_stock }}</span>
                                <!-- Stok health indicator -->
                                <span :class="[
                                    'text-xs px-2 py-0.5 rounded-full font-medium',
                                    item.current_stock === 0 ? 'bg-red-100 text-red-600' :
                                    item.current_stock <= item.minimum_stock ? 'bg-emerald-100 text-emerald-700' :
                                    'bg-green-100 text-green-700'
                                ]">
                                    {{ item.current_stock === 0 ? 'Habis' : item.current_stock <= item.minimum_stock ? 'Rendah' : 'Aman' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center text-gray-500">{{ item.minimum_stock }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ item.unit?.name }}</td>
                        <td class="px-5 py-3" @click.stop>
                            <div class="flex gap-1.5">
                                <!-- Histori: ikon jam dengan tooltip -->
                                <button @click="openHistory(item)"
                                    title="Lihat Riwayat Stok"
                                    class="p-1.5 text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                                <button @click="openModal(item)"
                                    title="Edit Barang"
                                    class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="confirmDelete(item)"
                                    title="Hapus Barang"
                                    class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="items.data.length === 0">
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">Tidak ada barang ditemukan.</td>
                    </tr>
                </tbody>
            </table>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-100">
                <div v-for="item in items.data" :key="item.id" class="p-4 bg-white hover:bg-gray-50 active:bg-gray-100 cursor-pointer" @click="openHistory(item)">
                    <div class="flex justify-between items-start mb-2">
                        <div class="pr-3">
                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded mb-1 bg-gray-100 text-gray-600">{{ item.sku }}</span>
                            <h3 class="font-bold text-gray-800 text-sm leading-tight">{{ item.name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ item.category?.name }} • {{ item.unit?.name }}</p>
                        </div>
                        <div class="text-right flex flex-col items-end shrink-0">
                            <span :class="[
                                'text-lg font-black leading-none',
                                item.current_stock === 0 ? 'text-red-600' :
                                item.current_stock <= item.minimum_stock ? 'text-emerald-600' : 'text-gray-800'
                            ]">{{ item.current_stock }}</span>
                            <span :class="[
                                'text-[10px] px-1.5 py-0.5 rounded font-bold mt-1',
                                item.current_stock === 0 ? 'bg-red-100 text-red-600' :
                                item.current_stock <= item.minimum_stock ? 'bg-emerald-100 text-emerald-700' :
                                'bg-green-100 text-green-700'
                            ]">
                                {{ item.current_stock === 0 ? 'Habis' : item.current_stock <= item.minimum_stock ? 'Rendah' : 'Aman' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-50">
                        <button @click.stop="openModal(item)" class="text-xs font-medium px-4 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">Edit</button>
                        <button @click.stop="confirmDelete(item)" class="text-xs font-medium px-4 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">Hapus</button>
                    </div>
                </div>
                <div v-if="items.data.length === 0" class="p-8 text-center text-gray-400 text-sm">
                    Tidak ada barang ditemukan.
                </div>
            </div>
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Menampilkan {{ items.from }}–{{ items.to }} dari {{ items.total }} data</span>
                <div class="flex gap-1">
                    <Link v-for="link in items.links" :key="link.label" :href="link.url || '#'"
                        :class="['px-2 py-1 rounded', link.active ? 'bg-emerald-500 text-white' : 'hover:bg-gray-100', !link.url ? 'opacity-40 cursor-not-allowed' : '']"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <!-- ===== MODAL FORM TAMBAH/EDIT ===== -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-5">{{ editMode ? 'Edit Barang' : 'Tambah Barang' }}</h3>
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                                <input v-model="form.sku" type="text" disabled class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-500" :placeholder="editMode ? '' : 'Dihasilkan Otomatis'" />
                                <p class="text-xs text-gray-400 mt-1">Sistem akan generate SKU otomatis</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                                <input v-model="form.name" @blur="checkDuplicate" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Cth: Susu UHT Diamond" required />
                                <p v-if="duplicateWarning" class="text-amber-600 font-medium text-xs mt-1 bg-amber-50 p-1.5 rounded border border-amber-200">{{ duplicateWarning }}</p>
                                <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                <select v-model="form.category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
                                    <option value="">Pilih Kategori</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                                <p v-if="errors.category_id" class="text-red-500 text-xs mt-1">{{ errors.category_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                <select v-model="form.unit_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
                                    <option value="">Pilih Satuan</option>
                                    <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                                </select>
                                <p v-if="errors.unit_id" class="text-red-500 text-xs mt-1">{{ errors.unit_id }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum <span class="text-red-500">*</span></label>
                                <input v-model="form.minimum_stock" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" required />
                            </div>
                            <div v-if="!editMode">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
                                <input v-model="form.current_stock" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" required />
                            </div>
                        </div>
                        <!-- Harga Satuan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Harga Beli per Satuan
                                <span class="text-xs text-gray-400 font-normal ml-1">(opsional — untuk kalkulasi nilai stok)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                                <input v-model="form.harga_satuan" type="number" min="0" step="100"
                                    class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400"
                                    placeholder="0" />
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Harga beli barang ini dari supplier per satuannya</p>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="closeModal" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" :disabled="processing" class="px-4 py-2 text-sm bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium disabled:opacity-60">
                                {{ processing ? 'Menyimpan...' : (editMode ? 'Simpan Perubahan' : 'Tambah Barang') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- ===== MODAL HISTORI (Tasks 2, 3, 4) ===== -->
        <Teleport to="body">
            <div v-if="showHistoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col max-h-[88vh]">

                    <!-- Header Modal -->
                    <div class="px-6 py-5 border-b border-gray-100">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-800">Riwayat Mutasi Stok</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ historyItem?.sku }}</span>
                                    <span class="ml-1.5">{{ historyItem?.name }}</span>
                                </p>
                            </div>
                            <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors flex-shrink-0">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Task 2: Ringkasan stok -->
                        <div v-if="!historyLoading && historyLedgers.length > 0" class="mt-4 grid grid-cols-3 gap-3">
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-500 mb-1">Stok Saat Ini</p>
                                <p :class="[
                                    'text-2xl font-bold',
                                    historyItem?.current_stock === 0 ? 'text-red-600' :
                                    historyItem?.current_stock <= historyItem?.minimum_stock ? 'text-emerald-600' : 'text-gray-800'
                                ]">{{ historyItem?.current_stock }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ historyItem?.unit?.name }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-500 mb-1">Total Mutasi</p>
                                <p class="text-2xl font-bold text-gray-800">{{ historyLedgers.length }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">transaksi</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-500 mb-1">Status Stok</p>
                                <p :class="[
                                    'text-sm font-bold mt-1',
                                    historyItem?.current_stock === 0 ? 'text-red-600' :
                                    historyItem?.current_stock <= historyItem?.minimum_stock ? 'text-emerald-600' : 'text-green-600'
                                ]">
                                    {{ historyItem?.current_stock === 0 ? '⚠ Habis' : historyItem?.current_stock <= historyItem?.minimum_stock ? '⚠ Rendah' : '✓ Aman' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">Min: {{ historyItem?.minimum_stock }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Body Timeline -->
                    <div class="overflow-y-auto flex-1 px-6 py-4">
                        <div v-if="historyLoading" class="flex items-center justify-center py-16 gap-3 text-gray-400">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="text-sm">Memuat riwayat...</span>
                        </div>

                        <div v-else-if="historyLedgers.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
                            <svg class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-medium">Belum ada riwayat mutasi</p>
                            <p class="text-xs mt-1">Lakukan transaksi untuk mulai mencatat riwayat barang ini</p>
                        </div>

                        <!-- Timeline -->
                        <ol v-else class="relative border-l border-gray-200 ml-3">
                            <li v-for="entry in historyLedgers" :key="entry.id" class="mb-5 ml-6 last:mb-0">
                                <span :class="[
                                    'absolute flex items-center justify-center w-7 h-7 rounded-full -left-3.5 ring-4 ring-white',
                                    entry.mutation_type === 'increment' ? 'bg-green-100' : 'bg-red-100'
                                ]">
                                    <svg class="h-3.5 w-3.5" :class="entry.mutation_type === 'increment' ? 'text-green-600' : 'text-red-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            :d="entry.mutation_type === 'increment' ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'" />
                                    </svg>
                                </span>

                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 hover:border-gray-200 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <span :class="[
                                                'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold mb-1.5',
                                                entry.mutation_type === 'increment' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                            ]">
                                                {{ entry.mutation_type === 'increment' ? '▲ Stok Masuk' : '▼ Stok Keluar' }}
                                            </span>
                                            <p class="text-sm font-semibold text-gray-800">{{ entry.notes }}</p>
                                            <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-400">
                                                <span>Sisa stok setelah mutasi:</span>
                                                <strong class="text-gray-700 text-sm">{{ entry.balance_after }}</strong>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p :class="['text-2xl font-bold', entry.mutation_type === 'increment' ? 'text-green-600' : 'text-red-600']">
                                                {{ entry.mutation_type === 'increment' ? '+' : '-' }}{{ entry.quantity }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5 whitespace-nowrap">{{ entry.date }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <div class="px-6 py-3 border-t border-gray-100 text-right">
                        <button @click="showHistoryModal = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Modal Hapus -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
                <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 mx-4 text-center">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Barang?</h3>
                    <p class="text-sm text-gray-500 mb-5">"<strong>{{ deleteTarget?.name }}</strong>" akan dihapus (soft delete).</p>
                    <div class="flex justify-center gap-3">
                        <button @click="showDeleteModal = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                        <button @click="deleteItem" class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">Hapus</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ items: Object, categories: Array, units: Array, filters: Object });

const showModal       = ref(false);
const editMode        = ref(false);
const processing      = ref(false);
const errors          = ref({});
const duplicateWarning = ref('');
const search          = ref(props.filters?.search || '');
const categoryFilter  = ref(props.filters?.category_id || '');
const defaultForm = () => ({ sku: '', name: '', type: props.filters?.type || 'consumable', department: props.filters?.department || 'kitchen', category_id: '', unit_id: '', minimum_stock: 0, current_stock: 0, harga_satuan: 0 });
const form            = ref(defaultForm());

const showHistoryModal = ref(false);
const historyLoading   = ref(false);
const historyItem      = ref(null);
const historyLedgers   = ref([]);

const showDeleteModal = ref(false);
const deleteTarget    = ref(null);

function applyFilter() {
    router.get(route('items.index'), { search: search.value, category_id: categoryFilter.value }, { preserveState: true, replace: true });
}

function openModal(item = null) {
    errors.value   = {};
    duplicateWarning.value = '';
    editMode.value = !!item;
    form.value     = item
        ? { id: item.id, sku: item.sku, name: item.name, type: item.type, department: item.department, category_id: item.category_id, unit_id: item.unit_id, minimum_stock: item.minimum_stock, current_stock: item.current_stock, harga_satuan: item.harga_satuan ?? 0 }
        : defaultForm();
    showModal.value = true;
}
function closeModal() { showModal.value = false; }

async function checkDuplicate() {
    if (!form.value.name || editMode.value) return;
    try {
        const { data } = await axios.get(route('items.check_duplicate'), { params: { name: form.value.name } });
        if (data.exists) {
            duplicateWarning.value = `⚠ Barang ini sudah terdaftar! Stok terakhir: ${data.item.current_stock}. Sebaiknya gunakan menu 'Buat Transaksi Restok' daripada membuat barang ganda.`;
        } else {
            duplicateWarning.value = '';
        }
    } catch(e) {}
}

function submitForm() {
    processing.value = true;
    const url    = editMode.value ? route('items.update', form.value.id) : route('items.store');
    const method = editMode.value ? 'put' : 'post';
    router[method](url, { ...form.value }, {
        onSuccess: () => { closeModal(); processing.value = false; },
        onError:   (e) => { errors.value = e; processing.value = false; },
    });
}

async function openHistory(item) {
    historyItem.value    = item;
    historyLedgers.value = [];
    historyLoading.value = true;
    showHistoryModal.value = true;
    try {
        const { data } = await axios.get(route('items.history', item.id));
        historyLedgers.value = data.ledgers;
    } catch {
        historyLedgers.value = [];
    } finally {
        historyLoading.value = false;
    }
}

function confirmDelete(item) { deleteTarget.value = item; showDeleteModal.value = true; }
function deleteItem() {
    router.delete(route('items.destroy', deleteTarget.value.id), {
        onSuccess: () => { showDeleteModal.value = false; },
    });
}
</script>
