<script setup>
// Détail par collecte : évite de mélanger plusieurs collectes d'une même
// entreprise (entreprise + date + lieu identifient chaque ligne).
import { useI18n } from 'vue-i18n';
import { useDateFormatter } from '@/composables/useDates';

defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
    showCompany: {
        type: Boolean,
        default: true,
    },
});

const { t } = useI18n();
const { formatLongDate } = useDateFormatter();

// Libellé + style du badge de statut (en cours / terminée).
const statusMeta = {
    ongoing: {
        label: 'admin.kpi.status_ongoing',
        class: 'bg-green-50 text-green-900',
    },
    ended: {
        label: 'admin.kpi.status_ended',
        class: 'bg-gray-100 text-gray-500',
    },
};
</script>

<template>
    <section>
        <h2 class="mb-4 text-lg font-bold tracking-tight text-gray-900">
            {{ t('admin.kpi.collect_table_title') }}
        </h2>

        <div class="overflow-x-auto border-2 border-gray-900 bg-white">
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b-2 border-gray-900 bg-gray-50 text-xs tracking-wide text-gray-600 uppercase"
                >
                    <tr>
                        <th v-if="showCompany" class="px-5 py-3 font-bold">
                            {{ t('admin.kpi.col_company') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.kpi.col_date') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.kpi.col_place') }}
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
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="rows.length === 0">
                        <td
                            :colspan="showCompany ? 6 : 5"
                            class="px-5 py-10 text-center text-gray-500"
                        >
                            {{ t('admin.kpi.table_empty') }}
                        </td>
                    </tr>
                    <tr
                        v-for="(row, index) in rows"
                        :key="index"
                        class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50"
                    >
                        <td
                            v-if="showCompany"
                            class="px-5 py-4 font-bold text-gray-900"
                        >
                            {{ row.company }}
                        </td>
                        <td class="px-5 py-4 text-gray-700">
                            <span class="flex items-center gap-2">
                                {{ formatLongDate(row.day) }}
                                <span
                                    class="border border-gray-900 px-1.5 py-0.5 text-[10px] font-semibold tracking-wide uppercase"
                                    :class="statusMeta[row.status].class"
                                >
                                    {{ t(statusMeta[row.status].label) }}
                                </span>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-700">
                            {{ row.city ?? '-' }}
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
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
