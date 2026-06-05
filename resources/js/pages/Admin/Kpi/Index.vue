<script setup>
import { Link } from '@inertiajs/vue3';
import { Award, Building2, MousePointerClick, Syringe } from 'lucide-vue-next';
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
import { show as kpiShow } from '@/routes/admin/kpi';

const { t } = useI18n();

const props = defineProps({
    companies: {
        type: Object,
        default: () => ({ total: 0, labelled: 0, active: 0 }),
    },
    collects: {
        type: Object,
        default: () => ({ total: 0, active: 0 }),
    },
    funnel: {
        type: Object,
        required: true,
    },
    appointments: {
        type: Object,
        required: true,
    },
    contact: {
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
    perCompany: {
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

const contactTypeLabels = computed(() => [
    t('admin.kpi.type_contact'),
    t('admin.kpi.type_collect_request'),
]);
const contactTypeValues = computed(() => [
    props.contact.by_type.contact,
    props.contact.by_type.collect_request,
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
    <AdminLayout :title="t('admin.kpi.title')">
        <template #subtitle>{{ t('admin.kpi.subtitle') }}</template>

        <!-- Cartes de stats globales -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                :label="t('admin.kpi.stat_companies')"
                :value="companies.total"
                :icon="Building2"
            />
            <StatCard
                :label="t('admin.kpi.stat_active_companies')"
                :value="companies.active"
                :icon="Building2"
            />
            <StatCard
                :label="t('admin.kpi.stat_labelled')"
                :value="companies.labelled"
                :icon="Award"
            />
            <StatCard
                :label="t('admin.kpi.stat_active_collects')"
                :value="collects.active"
                :icon="Syringe"
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

        <!-- Formulaire de contact -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <ChartCard
                :title="t('admin.kpi.contact_title')"
                :subtitle="t('admin.kpi.contact_subtitle')"
            >
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div
                        class="border-2 border-gray-900 bg-white p-3 text-center"
                    >
                        <p class="text-2xl font-bold text-gray-900">
                            {{ contact.clicks }}
                        </p>
                        <p
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.kpi.contact_clicks') }}
                        </p>
                    </div>
                    <div
                        class="border-2 border-gray-900 bg-white p-3 text-center"
                    >
                        <p class="text-2xl font-bold text-gray-900">
                            {{ contact.sent }}
                        </p>
                        <p
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.kpi.contact_sent') }}
                        </p>
                    </div>
                    <div
                        class="border-2 border-gray-900 bg-white p-3 text-center"
                    >
                        <p class="text-2xl font-bold text-gray-900">
                            {{ contact.rate }} %
                        </p>
                        <p
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.kpi.contact_rate') }}
                        </p>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div
                        class="border-2 border-gray-900 bg-white p-3 text-center"
                    >
                        <p class="text-2xl font-bold text-[var(--brand)]">
                            {{ contact.trophee }}
                        </p>
                        <p
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.kpi.contact_trophee') }}
                        </p>
                    </div>
                    <div
                        class="border-2 border-gray-900 bg-white p-3 text-center"
                    >
                        <p class="text-2xl font-bold text-[var(--brand)]">
                            {{ contact.trophee_rate }} %
                        </p>
                        <p
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.kpi.contact_trophee_rate') }}
                        </p>
                    </div>
                </div>
            </ChartCard>

            <ChartCard :title="t('admin.kpi.contact_by_type_title')">
                <DonutChart
                    :labels="contactTypeLabels"
                    :values="contactTypeValues"
                    :colors="['#2563eb', '#eab308']"
                >
                    <template #empty>{{ t('admin.kpi.no_data') }}</template>
                </DonutChart>
            </ChartCard>
        </div>

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

        <!-- Détail par entreprise -->
        <section class="mt-8">
            <h2 class="mb-4 text-lg font-bold tracking-tight text-gray-900">
                {{ t('admin.kpi.table_title') }}
            </h2>

            <div class="overflow-x-auto border-2 border-gray-900 bg-white">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b-2 border-gray-900 bg-gray-50 text-xs tracking-wide text-gray-600 uppercase"
                    >
                        <tr>
                            <th class="px-5 py-3 font-bold">
                                {{ t('admin.kpi.col_company') }}
                            </th>
                            <th class="px-5 py-3 text-right font-bold">
                                {{ t('admin.kpi.col_sessions') }}
                            </th>
                            <th class="px-5 py-3 text-right font-bold">
                                {{ t('admin.kpi.col_clicks') }}
                            </th>
                            <th class="px-5 py-3 text-right font-bold">
                                {{ t('admin.kpi.col_rate') }}
                            </th>
                            <th class="px-5 py-3 text-right font-bold">
                                {{ t('admin.kpi.col_actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="perCompany.length === 0">
                            <td
                                colspan="5"
                                class="px-5 py-10 text-center text-gray-500"
                            >
                                {{ t('admin.kpi.table_empty') }}
                            </td>
                        </tr>
                        <tr
                            v-for="row in perCompany"
                            :key="row.token"
                            class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50"
                        >
                            <td class="px-5 py-4 font-bold text-gray-900">
                                {{ row.name }}
                            </td>
                            <td class="px-5 py-4 text-right text-gray-700">
                                {{ row.sessions }}
                            </td>
                            <td class="px-5 py-4 text-right text-gray-700">
                                {{ row.clicks }}
                            </td>
                            <td class="px-5 py-4 text-right text-gray-700">
                                {{ row.rate }} %
                            </td>
                            <td class="px-5 py-4 text-right">
                                <Link
                                    :href="kpiShow({ token: row.token }).url"
                                    class="inline-flex items-center gap-1 border-2 border-gray-900 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 transition-colors hover:bg-gray-100"
                                >
                                    <MousePointerClick class="size-3.5" />
                                    {{ t('admin.kpi.open') }}
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Détail par collecte -->
        <CollectTable class="mt-8" :rows="perCollect" />
    </AdminLayout>
</template>
