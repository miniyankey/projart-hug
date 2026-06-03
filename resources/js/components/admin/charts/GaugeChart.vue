<script setup>
// Jauge radiale pour un taux unique (ex. conversion clic → RDV).
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { baseOptions, brandColor, ink } from './theme';

const props = defineProps({
    value: {
        type: Number,
        default: 0,
    },
    label: {
        type: String,
        default: '',
    },
});

const series = computed(() => [props.value]);

const options = computed(() => ({
    ...baseOptions(),
    chart: { ...baseOptions().chart, type: 'radialBar' },
    colors: [brandColor()],
    plotOptions: {
        radialBar: {
            hollow: { size: '60%' },
            track: { background: '#e5e7eb', strokeWidth: '100%' },
            dataLabels: {
                name: {
                    offsetY: 22,
                    color: '#6b7280',
                    fontSize: '13px',
                    fontWeight: 600,
                },
                value: {
                    offsetY: -12,
                    color: ink,
                    fontSize: '34px',
                    fontWeight: 700,
                    formatter: (val) => `${Math.round(val * 10) / 10} %`,
                },
            },
        },
    },
    labels: [props.label],
    stroke: { lineCap: 'butt' },
}));
</script>

<template>
    <VueApexCharts
        type="radialBar"
        height="280"
        :options="options"
        :series="series"
    />
</template>
