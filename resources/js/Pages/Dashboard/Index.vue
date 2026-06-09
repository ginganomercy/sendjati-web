<template>
    <Head title="Dashboard" />
    <AppLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Dashboard Inventaris {{ filters?.department === 'bar' ? 'Bar' : (filters?.department === 'kitchen' ? 'Kitchen' : '') }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">Ringkasan kondisi stok dan transaksi — {{ stats.bulanIni }}</p>
            </div>
        </div>

        <!-- Premium KPI Cards (Task 2) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <!-- Total Jenis Barang -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500 p-5 hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 pointer-events-none"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Total Barang</p>
                    <p class="text-2xl sm:text-3xl font-black text-gray-800">{{ stats.totalBarang }}</p>
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Jenis barang aktif
                    </p>
                </div>
            </div>

            <!-- Stok Kritis -->
            <div :class="[
                'bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 p-5 hover:shadow-md transition-shadow relative overflow-hidden',
                stats.stokKritis > 0 ? 'border-l-red-500' : 'border-l-gray-300'
            ]">
                <div :class="[
                    'absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 rounded-full opacity-50 pointer-events-none',
                    stats.stokKritis > 0 ? 'bg-red-50' : 'bg-gray-50'
                ]"></div>
                <div class="relative z-10">
                    <p :class="['text-xs font-bold uppercase tracking-wider mb-1', stats.stokKritis > 0 ? 'text-red-500' : 'text-gray-500']">Stok Kritis</p>
                    <p :class="['text-2xl sm:text-3xl font-black', stats.stokKritis > 0 ? 'text-red-600' : 'text-gray-800']">{{ stats.stokKritis }}</p>
                    <p :class="['text-xs mt-2 flex items-center gap-1', stats.stokKritis > 0 ? 'text-red-500 font-medium' : 'text-gray-500']">
                        <svg v-if="stats.stokKritis > 0" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <svg v-else class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ stats.stokKritis > 0 ? 'Barang perlu restok segera' : 'Semua stok dalam batas aman' }}
                    </p>
                </div>
            </div>

            <!-- Transaksi Masuk Bulan Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-green-500 p-5 hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 bg-green-50 rounded-full opacity-50 pointer-events-none"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-green-500 uppercase tracking-wider mb-1">Masuk Bulan Ini</p>
                    <p class="text-2xl sm:text-3xl font-black text-gray-800">{{ stats.transaksiMasukBulanIni }}</p>
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Transaksi penerimaan
                    </p>
                </div>
            </div>

            <!-- Nilai Stok -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-emerald-500 p-5 hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 pointer-events-none"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-1">Est. Nilai Stok</p>
                    <p class="text-xl sm:text-2xl font-black text-gray-800 leading-9">{{ nilaiStokFormatted }}</p>
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hanya dari barang berharga
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Chart.js Area Chart (Task 3) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-gray-800">Pergerakan Stok (6 Bulan Terakhir)</h3>
                    <div class="flex items-center gap-4 text-xs font-medium">
                        <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Barang Masuk</span>
                        <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span> Barang Keluar</span>
                    </div>
                </div>
                <!-- Canvas untuk Chart.js -->
                <div class="flex-1 min-h-[200px] md:min-h-[300px] relative w-full">
                    <canvas ref="chartCanvas"></canvas>
                </div>
            </div>

            <!-- Panel Stok Kritis -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col h-full overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Alert Stok Kritis
                    </h3>
                    <Link :href="route('items.index', {department: filters?.department ?? 'kitchen'})" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold bg-emerald-50 px-2 py-1 rounded-md transition-colors">Lihat Semua</Link>
                </div>

                <div class="flex-1 p-6 overflow-y-auto">
                    <div v-if="barangKritis.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400">
                        <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-700 mb-1">Aman Terkendali</p>
                        <p class="text-xs text-center">Tidak ada barang yang mendekati batas minimum stok saat ini.</p>
                    </div>

                    <ul v-else class="space-y-4">
                        <li v-for="item in barangKritis" :key="item.id" class="group">
                            <div class="flex items-center justify-between mb-1.5">
                                <p class="text-sm font-bold text-gray-800 truncate pr-3 group-hover:text-emerald-600 transition-colors">{{ item.name }}</p>
                                <p class="text-sm font-black text-red-600">{{ item.current_stock }}</p>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span class="font-mono bg-gray-100 px-1.5 rounded">{{ item.sku }}</span>
                                <span>Min: {{ item.minimum_stock }} {{ item.unit?.name }}</span>
                            </div>
                            <!-- Progress bar visualisasi kritis -->
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 overflow-hidden">
                                <div class="bg-red-500 h-1.5 rounded-full" :style="`width: ${(item.current_stock / item.minimum_stock) * 100}%`"></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    <Link :href="route('transactions.index', {department: filters?.department ?? 'kitchen'})"
                        class="flex items-center justify-center gap-2 w-full py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold rounded-lg transition-colors shadow-sm hover:shadow">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Transaksi Restok
                    </Link>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
/**
 * Komponen Dashboard Utama (Frontend)
 * Menggunakan Vue 3 Composition API dengan <script setup>
 * Berfungsi untuk merender metrik ringkasan dan grafik pergerakan stok secara dinamis.
 */
import { computed, onMounted, ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats:         Object, // Data metrik utama dari DashboardController
    chartData:     Array,  // Data pergerakan stok 6 bulan terakhir
    barangKritis:  Array,  // Daftar 5 barang dengan stok <= batas minimum
    filters:       Object,
});

/**
 * Format angka nominal uang ke dalam standar Rupiah (Rp).
 * Digunakan secara reaktif pada tampilan "Est. Nilai Stok".
 */

const nilaiStokFormatted = computed(() => {
    if (!props.stats.nilaiStok) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(props.stats.nilaiStok);
});

// Referensi DOM ke elemen <canvas> untuk menggambar grafik Chart.js
const chartCanvas = ref(null);
let chartInstance = null; // Menyimpan instance chart agar bisa di-destroy sebelum digambar ulang

/**
 * Hook lifecycle Vue: Dijalankan otomatis saat komponen selesai dipasang (mounted) ke DOM.
 */
onMounted(() => {
    initChart();
});

/**
 * Watcher (Pengamat) reaktivitas:
 * Memantau perubahan pada props.chartData. Jika data berubah (misal user memfilter data),
 * grafik akan digambar ulang secara instan.
 */
watch(() => props.chartData, () => {
    initChart();
}, { deep: true });

/**
 * Fungsi inisialisasi dan konfigurasi Chart.js
 * Merender grafik Area (Line Chart dengan background gradient)
 */
function initChart() {
    if (chartInstance) {
        chartInstance.destroy();
    }
    
    if (!chartCanvas.value) return;

    const ctx = chartCanvas.value.getContext('2d');
    
    // Siapkan data
    const labels = props.chartData.map(d => d.label);
    const dataMasuk = props.chartData.map(d => d.masuk);
    const dataKeluar = props.chartData.map(d => d.keluar);

    // Bikin gradient untuk line chart
    const gradientMasuk = ctx.createLinearGradient(0, 0, 0, 400);
    gradientMasuk.addColorStop(0, 'rgba(52, 211, 153, 0.4)'); // Emerald-400 transparent
    gradientMasuk.addColorStop(1, 'rgba(52, 211, 153, 0.0)');

    const gradientKeluar = ctx.createLinearGradient(0, 0, 0, 400);
    gradientKeluar.addColorStop(0, 'rgba(251, 113, 133, 0.4)'); // Rose-400 transparent
    gradientKeluar.addColorStop(1, 'rgba(251, 113, 133, 0.0)');

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Stok Masuk',
                    data: dataMasuk,
                    borderColor: '#34D399', // Emerald-400
                    backgroundColor: gradientMasuk,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#34D399',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Curve mulus
                },
                {
                    label: 'Stok Keluar',
                    data: dataKeluar,
                    borderColor: '#FB7185', // Rose-400
                    backgroundColor: gradientKeluar,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#FB7185',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false // Sudah pakai custom legend di HTML atas
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)', // Gray-900
                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                    bodyFont: { size: 13, family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    boxPadding: 4,
                    usePointStyle: true,
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 12 },
                        color: '#6B7280'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#F3F4F6', // Gray-100
                        drawBorder: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 12 },
                        color: '#9CA3AF',
                        stepSize: 10
                    }
                }
            }
        }
    });
}
</script>
