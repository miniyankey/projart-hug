<script setup>
// Barres verticales sobres pour une distribution simple (une série, paliers).
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { baseOptions, brandColor, ink } from './theme';

const props = defineProps({
    labels: {
        type: Array,
        default: () => [],
    },
    values: {
        type: Array,
        default: () => [],
    },
    name: {
        type: String,
        default: '',
    },
});

const series = computed(() => [{ name: props.name, data: props.values }]);

const isEmpty = computed(() => props.values.every((value) => value === 0));

const options = computed(() => ({
    ...baseOptions(),
    chart: { ...baseOptions().chart, type: 'bar' },
    colors: [brandColor()],
    plotOptions: {
        bar: { horizontal: false, borderRadius: 0, columnWidth: '55%' },
    },
    dataLabels: {
        enabled: true,
        style: { colors: [ink], fontWeight: 700 },
        offsetY: -18,
    },
    xaxis: {
        categories: props.labels,
        labels: { style: { fontSize: '11px', fontWeight: 600 } },
    },
    yaxis: { labels: { formatter: (v) => Math.round(v) } },
    legend: { show: false },
}));
</script>

<template>
    <div
        v-if="isEmpty"
        class="flex h-[280px] items-center justify-center text-sm text-gray-400"
    >
        <slot name="empty">—</slot>
    </div>
    <VueApexCharts
        v-else
        type="bar"
        height="280"
        :options="options"
        :series="series"
    />
</template>
