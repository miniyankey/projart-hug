<script setup>
// Donut sobre pour une répartition (éligibilité, source du clic, type de demande).
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { baseOptions, brandColor } from './theme';

const props = defineProps({
    labels: {
        type: Array,
        default: () => [],
    },
    values: {
        type: Array,
        default: () => [],
    },
    colors: {
        type: Array,
        default: null,
    },
});

const series = computed(() => props.values);

const isEmpty = computed(() => props.values.every((value) => value === 0));

const options = computed(() => ({
    ...baseOptions(),
    chart: { ...baseOptions().chart, type: 'donut' },
    labels: props.labels,
    colors: props.colors ?? [brandColor(), '#cbd5e1', '#94a3b8'],
    stroke: { width: 2, colors: ['#ffffff'] },
    legend: {
        position: 'bottom',
        fontWeight: 600,
        markers: { radius: 0 },
    },
    plotOptions: {
        pie: {
            donut: {
                size: '62%',
                labels: { show: false },
            },
        },
    },
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
        type="donut"
        height="280"
        :options="options"
        :series="series"
    />
</template>
