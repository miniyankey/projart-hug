<script setup>
// Résultats du parcours d'éligibilité : éligible / inéligible / non terminé,
// + sessions, taux de complétion et plus forte chute du funnel.
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import ChartCard from '@/components/admin/charts/ChartCard.vue';
import DonutChart from '@/components/admin/charts/DonutChart.vue';

const props = defineProps({
    funnel: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const labels = computed(() => [
    t('admin.kpi.funnel_eligible'),
    t('admin.kpi.funnel_ineligible'),
    t('admin.kpi.funnel_abandoned'),
]);
const values = computed(() => [
    props.funnel.eligible,
    props.funnel.ineligible,
    props.funnel.abandoned,
]);

const hasDropoff = computed(
    () => props.funnel.dropoff && props.funnel.dropoff.from !== null,
);
</script>

<template>
    <ChartCard :title="t('admin.kpi.funnel_results_title')">
        <DonutChart
            :labels="labels"
            :values="values"
            :colors="['#0d9488', '#ef4444', '#94a3b8']"
        >
            <template #empty>{{ t('admin.kpi.no_data') }}</template>
        </DonutChart>

        <div class="mt-4 grid grid-cols-2 gap-3 text-center">
            <div class="border-2 border-gray-900 bg-white p-3">
                <p class="text-2xl font-bold text-gray-900">
                    {{ funnel.sessions }}
                </p>
                <p
                    class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                >
                    {{ t('admin.kpi.funnel_sessions') }}
                </p>
            </div>
            <div class="border-2 border-gray-900 bg-white p-3">
                <p class="text-2xl font-bold text-gray-900">
                    {{ funnel.completion_rate }} %
                </p>
                <p
                    class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                >
                    {{ t('admin.kpi.funnel_completion_rate') }}
                </p>
            </div>
        </div>

        <div class="mt-3 border-2 border-gray-900 bg-white p-3">
            <p
                class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
            >
                {{ t('admin.kpi.funnel_dropoff_label') }}
            </p>
            <p class="mt-1 text-sm font-bold text-gray-900">
                <template v-if="hasDropoff">
                    {{
                        t('admin.kpi.funnel_dropoff_value', {
                            from: funnel.dropoff.from,
                            to: funnel.dropoff.to,
                            lost: funnel.dropoff.lost,
                            rate: funnel.dropoff.rate,
                        })
                    }}
                </template>
                <template v-else>
                    {{ t('admin.kpi.funnel_dropoff_empty') }}
                </template>
            </p>
        </div>
    </ChartCard>
</template>
