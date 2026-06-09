<template>
    <div :class="[
        'rounded-xl p-5 text-white shadow-sm transition-transform duration-300 hover:-translate-y-1',
        bgClass
    ]">
        <h3 class="text-sm font-medium text-white/90 mb-3">{{ title }}</h3>
        
        <div v-if="type === 'currency'">
            <div class="text-2xl font-bold tracking-tight">{{ formatCurrency(value) }}</div>
        </div>
        
        <div v-else-if="type === 'transaction'">
            <div class="space-y-1 mt-1">
                <div class="text-[1.1rem] font-bold text-white/95">{{ subValues[0].value }} <span class="font-medium text-white/90">{{ subValues[0].label }}</span></div>
                <div class="text-[1.1rem] font-bold text-white/95">{{ subValues[1].value }} <span class="font-medium text-white/90">{{ subValues[1].label }}</span></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    type: {
        type: String,
        default: 'currency' // 'currency' or 'transaction'
    },
    value: Number,
    subValues: Array,
    color: {
        type: String,
        default: 'blue'
    }
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(val);
};

const bgClass = computed(() => {
    switch (props.color) {
        case 'orange':
            return 'bg-gradient-to-br from-emerald-500 to-emerald-600';
        case 'blue':
            return 'bg-gradient-to-br from-blue-600 to-blue-800';
        case 'purple':
            return 'bg-gradient-to-br from-indigo-500 to-purple-600';
        case 'green':
            return 'bg-gradient-to-br from-green-500 to-green-600';
        default:
            return 'bg-gradient-to-br from-gray-500 to-gray-600';
    }
});
</script>
