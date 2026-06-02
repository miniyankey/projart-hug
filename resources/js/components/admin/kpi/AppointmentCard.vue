<script setup>
// Conversion prise de RDV : jauge du taux clic → RDV, volumes et part des
// clics provenant du jeu d'éligibilité.
import { useI18n } from 'vue-i18n';
import ChartCard from '@/components/admin/charts/ChartCard.vue';
import GaugeChart from '@/components/admin/charts/GaugeChart.vue';

defineProps({
    appointments: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();
</script>

<template>
    <ChartCard
        :title="t('admin.kpi.appointments_title')"
        :subtitle="t('admin.kpi.appointments_subtitle')"
    >
        <div class="grid grid-cols-1 items-center gap-4 sm:grid-cols-2">
            <GaugeChart
                :value="appointments.rate"
                :label="t('admin.kpi.appointments_rate')"
            />
            <div class="space-y-3">
                <div class="border-2 border-gray-900 bg-white p-3">
                    <p class="text-2xl font-bold text-gray-900">
                        {{ appointments.clicks }}
                    </p>
                    <p
                        class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                    >
                        {{ t('admin.kpi.appointments_clicks') }}
                    </p>
                </div>
                <div class="border-2 border-gray-900 bg-white p-3">
                    <p class="text-2xl font-bold text-gray-900">
                        {{ appointments.sessions }}
                    </p>
                    <p
                        class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                    >
                        {{ t('admin.kpi.appointments_sessions') }}
                    </p>
                </div>
                <div class="border-2 border-gray-900 bg-white p-3">
                    <p class="text-2xl font-bold text-[var(--brand)]">
                        {{ appointments.game_share }} %
                    </p>
                    <p
                        class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                    >
                        {{ t('admin.kpi.appointments_game_share') }}
                    </p>
                </div>
            </div>
        </div>
    </ChartCard>
</template>
