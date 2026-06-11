<script setup>
import { Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import AdminIconButton from '@/components/admin/AdminIconButton.vue';
import AdminStatusBadge from '@/components/admin/AdminStatusBadge.vue';
import CobrandLink from '@/components/admin/CobrandLink.vue';
import CompanyAvatar from '@/components/admin/CompanyAvatar.vue';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import { useDateFormatter } from '@/composables/useDates';
import { useDeleteConfirm } from '@/composables/useDeleteConfirm';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { create, destroy, edit } from '@/routes/admin/collectes';

const { t } = useI18n();
const { formatLongDate } = useDateFormatter();

defineProps({
    collects: {
        type: Array,
        default: () => [],
    },
});

const {
    target: collectToDelete,
    open: confirmOpen,
    processing: deleting,
    ask: askRemove,
    confirm: confirmRemove,
} = useDeleteConfirm((collect) => destroy(collect.id).url);

function formatDay(day) {
    return formatLongDate(day) || '-';
}

function formatTimeRange(start, end) {
    if (!start && !end) {
        return null;
    }

    const trim = (time) => (time ? time.slice(0, 5) : '');

    return `${trim(start)}${end ? ` - ${trim(end)}` : ''}`;
}
</script>

<template>
    <AdminLayout :title="t('admin.collectes.title')">
        <template #subtitle>{{ t('admin.collectes.subtitle') }}</template>

        <template #actions>
            <Button as-child variant="pixel_violet">
                <Link :href="create.url()">
                    <Plus class="size-4" />
                    {{ t('admin.collectes.new') }}
                </Link>
            </Button>
        </template>

        <div class="overflow-x-auto border-2 border-gray-900 bg-white">
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b-2 border-gray-900 bg-gray-50 text-xs tracking-wide text-gray-600 uppercase"
                >
                    <tr>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.collectes.col_company') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.collectes.col_place') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.collectes.col_date') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.collectes.col_status') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.collectes.col_link') }}
                        </th>
                        <th class="px-5 py-3 text-right font-bold">
                            {{ t('admin.collectes.col_actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="collects.length === 0">
                        <td
                            colspan="6"
                            class="px-5 py-10 text-center text-gray-500"
                        >
                            {{ t('admin.collectes.empty') }}
                        </td>
                    </tr>
                    <tr
                        v-for="collect in collects"
                        :key="collect.id"
                        class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50"
                    >
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <CompanyAvatar
                                    :logo-url="collect.company?.logo_url"
                                    :color="collect.company?.color"
                                    :name="collect.company?.name"
                                />
                                <span class="font-bold text-gray-900">{{
                                    collect.company?.name
                                }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-900">
                                {{ collect.place?.name }}
                            </div>
                            <div class="text-gray-500">
                                {{ collect.place?.city }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-900">
                                {{ formatDay(collect.day) }}
                            </div>
                            <div
                                v-if="
                                    formatTimeRange(
                                        collect.start_time,
                                        collect.end_time,
                                    )
                                "
                                class="text-gray-500"
                            >
                                {{
                                    formatTimeRange(
                                        collect.start_time,
                                        collect.end_time,
                                    )
                                }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <AdminStatusBadge
                                v-if="collect.status === 'ongoing'"
                                tone="ongoing"
                            >
                                {{ t('admin.collectes.status_ongoing') }}
                            </AdminStatusBadge>
                            <AdminStatusBadge v-else tone="neutral">
                                {{ t('admin.collectes.status_ended') }}
                            </AdminStatusBadge>
                        </td>
                        <td class="px-5 py-4">
                            <CobrandLink
                                :company-slug="collect.company?.slug"
                                :collect-slug="collect.slug"
                            />
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <AdminIconButton
                                    :href="edit(collect.id).url"
                                    :title="t('admin.collectes.edit')"
                                >
                                    <Pencil class="size-4" />
                                </AdminIconButton>
                                <AdminIconButton
                                    destructive
                                    :title="t('admin.collectes.delete')"
                                    @click="askRemove(collect)"
                                >
                                    <Trash2 class="size-4" />
                                </AdminIconButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ConfirmDialog
            v-model:open="confirmOpen"
            destructive
            :title="t('admin.collectes.delete_title')"
            :description="
                collectToDelete
                    ? t('admin.collectes.delete_description', {
                          company: collectToDelete.company?.name,
                          date: formatDay(collectToDelete.day),
                      })
                    : ''
            "
            :confirm-label="t('admin.collectes.delete')"
            :cancel-label="t('admin.collectes.create.cancel')"
            :processing="deleting"
            @confirm="confirmRemove"
        />
    </AdminLayout>
</template>
