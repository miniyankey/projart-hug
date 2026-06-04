<script setup>
// Funnel d'éligibilité : barres horizontales décroissantes, une par étape.
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
});

const series = computed(() => [{ name: 'sessions', data: props.values }]);

const options = computed(() => ({
    ...baseOptions(),
    chart: { ...baseOptions().chart, type: 'bar' },
    colors: [brandColor()],
    plotOptions: {
        bar: {
            horizontal: true,
            borderRadius: 0,
            barHeight: '70%',
            distributed: false,
        },
    },
    dataLabels: {
        enabled: true,
        style: { colors: [ink], fontWeight: 700 },
        offsetX: 16,
    },
    xaxis: {
        categories: props.labels,
        labels: { style: { fontWeight: 600 } },
    },
    grid: { ...baseOptions().grid, xaxis: { lines: { show: true } } },
    legend: { show: false },
}));
</script>

<template>
    <VueApexCharts
        type="bar"
        height="320"
        :options="options"
        :series="series"
    />
</template>
