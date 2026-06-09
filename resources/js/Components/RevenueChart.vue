<template>
    <div class="w-full h-[400px]">
        <apexchart type="line" height="100%" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const apexchart = VueApexCharts;

const series = ref([
    {
        name: 'Jumlah Pendapatan',
        type: 'column',
        data: [160000000, 200000000, 150000000, 130000000, 180000000, 210000000, 150000000, 250000000, 150000000, 70000000, 140000000, 190000000]
    },
    {
        name: 'Pengeluaran',
        type: 'line',
        data: [150000000, 145000000, 148000000, 180000000, 220000000, 170000000, 140000000, 280000000, 110000000, 90000000, 130000000, 240000000]
    }
]);

const chartOptions = ref({
    chart: {
        height: 400,
        type: 'line',
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true
            }
        }
    },
    colors: ['#f59e0b', '#22c55e'],
    stroke: {
        width: [0, 3],
        curve: 'straight'
    },
    dataLabels: {
        enabled: false,
    },
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    xaxis: {
        type: 'category'
    },
    yaxis: {
        title: {
            text: 'Jumlah Pendapatan'
        },
        labels: {
            formatter: (value) => {
                if (value >= 1000000) {
                    return (value / 1000000) + 'M';
                }
                return value;
            }
        }
    },
    legend: {
        show: false
    },
    grid: {
        borderColor: '#f1f1f1',
    }
});
</script>
