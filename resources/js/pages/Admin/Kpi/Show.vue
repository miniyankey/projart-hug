<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Syringe } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import ChartCard from '@/components/admin/charts/ChartCard.vue';
import DonutChart from '@/components/admin/charts/DonutChart.vue';
import FunnelChart from '@/components/admin/charts/FunnelChart.vue';
import LineChart from '@/components/admin/charts/LineChart.vue';
import AppointmentCard from '@/components/admin/kpi/AppointmentCard.vue';
import CollectTable from '@/components/admin/kpi/CollectTable.vue';
import FunnelResultsCard from '@/components/admin/kpi/FunnelResultsCard.vue';
import LeadTimeCard from '@/components/admin/kpi/LeadTimeCard.vue';
import StatCard from '@/components/admin/StatCard.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index as kpiIndex } from '@/routes/admin/kpi';

const { t } = useI18n();

const props = defineProps({
    company: {
        type: Object,
        required: true,
    },
    funnel: {
        type: Object,
        required: true,
    },
    appointments: {
        type: Object,
        required: true,
    },
    leadTime: {
        type: Object,
        required: true,
    },
    timeline: {
        type: Array,
        default: () => [],
    },
    perCollect: {
        type: Array,
        default: () => [],
    },
});

const funnelLabels = computed(() =>
    props.funnel.steps.map((row) =>
        t('admin.kpi.funnel_step', { n: row.step }),
    ),
);
const funnelValues = computed(() =>
    props.funnel.steps.map((row) => row.sessions),
);

const sourceLabels = computed(() => [
    t('admin.kpi.source_collecte'),
    t('admin.kpi.source_jeu'),
]);
const sourceValues = computed(() => [
    props.appointments.sources.collecte,
    props.appointments.sources.jeu,
]);

const timelineCategories = computed(() =>
    props.timeline.map((point) => {
        const [, month, day] = point.date.split('-');

        return `${day}/${month}`;
    }),
);
const timelineSeries = computed(() => [
    {
        name: t('admin.kpi.timeline_clicks'),
        data: props.timeline.map((p) => p.clicks),
    },
    {
        name: t('admin.kpi.timeline_sessions'),
        data: props.timeline.map((p) => p.sessions),
    },
]);
</script>

<template>
    <AdminLayout :title="t('admin.kpi.show_heading', { name: company.name })">
        <template #subtitle>{{ t('admin.kpi.show_subtitle') }}</template>

        <template #actions>
            <Link
                :href="kpiIndex.url()"
                class="inline-flex items-center gap-2 border-2 border-gray-900 bg-white px-3 py-2 text-sm font-semibold text-gray-900 transition-colors hover:bg-gray-100"
            >
                <ArrowLeft class="size-4" />
                {{ t('admin.kpi.back_to_index') }}
            </Link>
        </template>

        <!-- Cartes de stats de l'entreprise -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                :label="t('admin.kpi.stat_active_collects')"
                :value="company.collects_active"
                :icon="Syringe"
            />
            <StatCard
                :label="t('admin.kpi.stat_collects_total')"
                :value="company.collects_total"
                :icon="Syringe"
            />
            <StatCard
                :label="t('admin.kpi.appointments_clicks')"
                :value="appointments.clicks"
            />
            <StatCard
                :label="t('admin.kpi.appointments_rate')"
                :value="`${appointments.rate} %`"
            />
        </div>

        <p
            class="mt-4 border-l-4 border-gray-900 bg-gray-50 px-4 py-3 text-sm text-gray-600"
        >
            {{ t('admin.kpi.honest_note') }}
        </p>

        <!-- Funnel d'éligibilité -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <ChartCard
                class="lg:col-span-2"
                :title="t('admin.kpi.funnel_title')"
                :subtitle="t('admin.kpi.funnel_subtitle')"
            >
                <FunnelChart :labels="funnelLabels" :values="funnelValues" />
            </ChartCard>

            <FunnelResultsCard :funnel="funnel" />
        </div>

        <!-- Conversion prise de RDV -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <AppointmentCard :appointments="appointments" />

            <ChartCard :title="t('admin.kpi.appointments_sources_title')">
                <DonutChart :labels="sourceLabels" :values="sourceValues">
                    <template #empty>{{ t('admin.kpi.no_data') }}</template>
                </DonutChart>
            </ChartCard>
        </div>

        <!-- Avance de réservation -->
        <LeadTimeCard class="mt-6" :lead-time="leadTime" />

        <!-- Évolution temporelle -->
        <ChartCard
            class="mt-6"
            :title="t('admin.kpi.timeline_title')"
            :subtitle="t('admin.kpi.timeline_subtitle')"
        >
            <LineChart
                :categories="timelineCategories"
                :series="timelineSeries"
            />
        </ChartCard>

        <!-- Détail par collecte -->
        <CollectTable class="mt-8" :rows="perCollect" :show-company="false" />
    </AdminLayout>
</template>
