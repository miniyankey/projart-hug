<script setup>
// Avance de réservation : distribution du délai entre le clic RDV et la date
// de la collecte, + avance moyenne.
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import BarChart from '@/components/admin/charts/BarChart.vue';
import ChartCard from '@/components/admin/charts/ChartCard.vue';

defineProps({
    leadTime: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const labels = computed(() => [
    t('admin.kpi.lead_bucket_0'),
    t('admin.kpi.lead_bucket_1'),
    t('admin.kpi.lead_bucket_2'),
    t('admin.kpi.lead_bucket_3'),
    t('admin.kpi.lead_bucket_4'),
]);
</script>

<template>
    <ChartCard
        :title="t('admin.kpi.lead_title')"
        :subtitle="t('admin.kpi.lead_subtitle')"
    >
        <div class="grid grid-cols-1 items-center gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <BarChart
                    :labels="labels"
                    :values="leadTime.buckets"
                    :name="t('admin.kpi.lead_axis')"
                >
                    <template #empty>{{ t('admin.kpi.no_data') }}</template>
                </BarChart>
            </div>
            <div class="border-2 border-gray-900 bg-white p-4 text-center">
                <p class="text-4xl font-bold text-[var(--brand)]">
                    {{
                        t('admin.kpi.lead_average_unit', {
                            n: leadTime.average,
                        })
                    }}
                </p>
                <p
                    class="mt-1 text-xs font-semibold tracking-wide text-gray-500 uppercase"
                >
                    {{ t('admin.kpi.lead_average') }}
                </p>
            </div>
        </div>
    </ChartCard>
</template>
