<template>
    <Head title="Kategori Barang" />
    <AppLayout>
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm flex items-center gap-2">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm flex items-center gap-2">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 012 0v2a1 1 0 01-2 0v-2zm0-8a1 1 0 012 0v4a1 1 0 01-2 0V5z" clip-rule="evenodd"/></svg>
            {{ $page.props.flash.error }}
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Kategori {{ filters?.department === 'bar' ? 'Bar' : 'Kitchen' }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola kategori bahan baku dan barang siap jual</p>
            </div>
            <button @click="openModal()" class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium text-sm transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kategori
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="(cat, i) in categories.data" :key="cat.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-500">{{ categories.from + i }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ cat.name }}</td>
                        <td class="px-5 py-3">
                            <span :class="cat.type === 'raw_material' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'"
                                class="px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ cat.type === 'raw_material' ? 'Bahan Baku' : 'Siap Jual' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 flex gap-2">
                            <button @click="openModal(cat)" class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition-colors">Edit</button>
                            <button @click="confirmDelete(cat)" class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded border border-red-200 hover:bg-red-50 transition-colors">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="categories.data.length === 0">
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400">Belum ada kategori. Tambahkan kategori pertama Anda.</td>
                    </tr>
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Menampilkan {{ categories.from }}–{{ categories.to }} dari {{ categories.total }} data</span>
                <div class="flex gap-1">
                    <Link v-for="link in categories.links" :key="link.label" :href="link.url || '#'"
                        :class="['px-2 py-1 rounded', link.active ? 'bg-emerald-500 text-white' : 'hover:bg-gray-100', !link.url ? 'opacity-40 cursor-not-allowed' : '']"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 mx-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-5">{{ editMode ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
                    <form @submit.prevent="submitForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Contoh: Biji Kopi" required />
                            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                            <select v-model="form.type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
                                <option value="raw_material">Bahan Baku</option>
                                <option value="ready_to_sell">Siap Jual</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeModal" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Batal</button>
                            <button type="submit" :disabled="processing" class="px-4 py-2 text-sm bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition-colors disabled:opacity-60">
                                {{ processing ? 'Menyimpan...' : (editMode ? 'Simpan Perubahan' : 'Tambah') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Delete Confirm -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
                <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 mx-4 text-center">
                    <div class="text-red-500 mb-3"><svg class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Hapus Kategori?</h3>
                    <p class="text-sm text-gray-500 mb-5">Kategori "<strong>{{ deleteTarget?.name }}</strong>" akan dihapus permanen.</p>
                    <div class="flex justify-center gap-3">
                        <button @click="showDeleteModal = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                        <button @click="deleteCategory" :disabled="processing" class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">Hapus</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ categories: Object, filters: Object });

const showModal = ref(false);
const showDeleteModal = ref(false);
const editMode = ref(false);
const deleteTarget = ref(null);
const processing = ref(false);
const errors = ref({});

const form = ref({ name: '', type: 'raw_material', department: props.filters?.department || 'kitchen' });

function openModal(cat = null) {
    errors.value = {};
    editMode.value = !!cat;
    form.value = cat ? { name: cat.name, type: cat.type, department: cat.department, id: cat.id } : { name: '', type: 'raw_material', department: props.filters?.department || 'kitchen' };
    showModal.value = true;
}

function closeModal() { showModal.value = false; }

function submitForm() {
    processing.value = true;
    const url = editMode.value ? route('categories.update', form.value.id) : route('categories.store');
    const method = editMode.value ? 'put' : 'post';
    router[method](url, { name: form.value.name, type: form.value.type, department: form.value.department }, {
        onSuccess: () => { closeModal(); processing.value = false; },
        onError: (e) => { errors.value = e; processing.value = false; },
    });
}

function confirmDelete(cat) { deleteTarget.value = cat; showDeleteModal.value = true; }

function deleteCategory() {
    processing.value = true;
    router.delete(route('categories.destroy', deleteTarget.value.id), {
        onSuccess: () => { showDeleteModal.value = false; processing.value = false; },
        onFinish: () => { processing.value = false; }
    });
}
</script>
