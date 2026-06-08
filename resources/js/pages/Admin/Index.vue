<script setup>
import { Link } from '@inertiajs/vue3';
import { Award, Building2, Syringe } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { showRegister } from '@/actions/App/Http/Controllers/AdminAuthController';
import CompanyAvatar from '@/components/admin/CompanyAvatar.vue';
import StatCard from '@/components/admin/StatCard.vue';
import { Button } from '@/components/ui/button';
import { useDateFormatter } from '@/composables/useDates';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { collecte as cobrandCollecte } from '@/routes/cobrand';

const { t } = useI18n();
const { formatLongDate } = useDateFormatter();

defineProps({
    stats: {
        type: Object,
        default: () => ({ companies: 0, labelled: 0, active_collects: 0 }),
    },
    activeCollects: {
        type: Array,
        default: () => [],
    },
});

function cobrandUrl(collect) {
    return cobrandCollecte({
        brandName: collect.company_slug,
        collect: collect.collect_slug,
    }).url;
}
</script>

<template>
    <AdminLayout :title="t('admin.overview.title')">
        <template #subtitle>{{ t('admin.overview.subtitle') }}</template>

        <template #actions>
            <Button as-child variant="admin_outline">
                <Link :href="showRegister.url()">
                    {{ t('admin.overview.create_admin') }}
                </Link>
            </Button>
        </template>

        <!-- Cartes de stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <StatCard
                :label="t('admin.overview.stat_companies')"
                :value="stats.companies"
                :icon="Building2"
            />
            <StatCard
                :label="t('admin.overview.stat_labelled')"
                :value="stats.labelled"
                :icon="Award"
            />
            <StatCard
                :label="t('admin.overview.stat_active_collects')"
                :value="stats.active_collects"
                :icon="Syringe"
            />
        </div>

        <!-- Collectes en cours -->
        <section class="mt-8">
            <h2 class="mb-4 text-lg font-bold tracking-tight text-gray-900">
                {{ t('admin.overview.active_collects') }}
            </h2>

            <div class="overflow-x-auto border-2 border-gray-900 bg-white">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b-2 border-gray-900 bg-gray-50 text-xs tracking-wide text-gray-600 uppercase"
                    >
                        <tr>
                            <th class="px-5 py-3 font-bold">
                                {{ t('admin.overview.col_company') }}
                            </th>
                            <th class="px-5 py-3 font-bold">
                                {{ t('admin.overview.col_date') }}
                            </th>
                            <th class="px-5 py-3 font-bold">
                                {{ t('admin.overview.col_place') }}
                            </th>
                            <th class="px-5 py-3 text-right font-bold">
                                {{ t('admin.overview.col_link') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="activeCollects.length === 0">
                            <td
                                colspan="4"
                                class="px-5 py-10 text-center text-gray-500"
                            >
                                {{ t('admin.overview.empty') }}
                            </td>
                        </tr>
                        <tr
                            v-for="collect in activeCollects"
                            :key="collect.id"
                            class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50"
                        >
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <CompanyAvatar
                                        class="size-8"
                                        :logo-url="collect.logo_url"
                                        :color="collect.color"
                                        :name="collect.company"
                                    />
                                    <span class="font-bold text-gray-900">
                                        {{ collect.company }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-700">
                                {{ formatLongDate(collect.day) }}
                            </td>
                            <td class="px-5 py-4 text-gray-700">
                                {{ collect.city ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a
                                    :href="cobrandUrl(collect)"
                                    target="_blank"
                                    class="inline-flex items-center border-2 border-gray-900 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 transition-colors hover:bg-gray-100"
                                >
                                    {{ t('admin.overview.open_link') }}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>
