<script setup>
// Évolution temporelle : courbes (clics RDV / sessions de jeu) par jour.
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { accentBlue, baseOptions, brandColor } from './theme';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    series: {
        type: Array,
        default: () => [],
    },
});

const options = computed(() => ({
    ...baseOptions(),
    chart: { ...baseOptions().chart, type: 'line' },
    colors: [brandColor(), accentBlue],
    stroke: { width: 3, curve: 'straight' },
    markers: { size: 0, hover: { size: 4 } },
    xaxis: {
        categories: props.categories,
        tickAmount: 6,
        labels: { rotate: 0, style: { fontSize: '11px' } },
    },
    yaxis: {
        min: 0,
        forceNiceScale: true,
        labels: { formatter: (v) => Math.round(v) },
    },
    legend: {
        position: 'top',
        horizontalAlign: 'left',
        fontWeight: 600,
        markers: { radius: 0 },
    },
}));
</script>

<template>
    <VueApexCharts
        type="line"
        height="300"
        :options="options"
        :series="series"
    />
</template>
