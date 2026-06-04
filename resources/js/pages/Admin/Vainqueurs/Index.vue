<script setup>
import { Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import CompanyAvatar from '@/components/admin/CompanyAvatar.vue';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import { useDeleteConfirm } from '@/composables/useDeleteConfirm';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { create, destroy, edit } from '@/routes/admin/vainqueurs';

const { t } = useI18n();

defineProps({
    trophees: {
        type: Array,
        default: () => [],
    },
});

const rankLabels = { 1: '1er', 2: '2ème', 3: '3ème' };

const {
    target: tropheeToDelete,
    open: confirmOpen,
    processing: deleting,
    ask: askRemove,
    confirm: confirmRemove,
} = useDeleteConfirm((trophee) => destroy(trophee.id).url);
</script>

<template>
    <AdminLayout :title="t('admin.vainqueurs.title')">
        <template #subtitle>{{ t('admin.vainqueurs.subtitle') }}</template>

        <template #actions>
            <Button
                as-child
                class="border-2 border-gray-900 bg-[var(--brand)] text-white hover:bg-[var(--brand-hover)]"
            >
                <Link :href="create.url()">
                    <Plus class="size-4" />
                    {{ t('admin.vainqueurs.new') }}
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
                            {{ t('admin.vainqueurs.col_year') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.vainqueurs.col_rank') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.vainqueurs.col_company') }}
                        </th>
                        <th class="px-5 py-3 text-right font-bold">
                            {{ t('admin.vainqueurs.col_actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="trophees.length === 0">
                        <td
                            colspan="4"
                            class="px-5 py-10 text-center text-gray-500"
                        >
                            {{ t('admin.vainqueurs.empty') }}
                        </td>
                    </tr>
                    <tr
                        v-for="trophee in trophees"
                        :key="trophee.id"
                        class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50"
                    >
                        <td class="px-5 py-4 font-pixel text-xs text-gray-900">
                            {{ trophee.year_of }}
                        </td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center border-2 border-gray-900 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700"
                            >
                                {{ rankLabels[trophee.rank] }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <CompanyAvatar
                                    :logo-url="trophee.company?.logo_url"
                                    :color="trophee.company?.color"
                                    :name="trophee.company?.name"
                                />
                                <span class="font-bold text-gray-900">{{
                                    trophee.company?.name
                                }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="edit(trophee.id).url"
                                    class="flex size-8 items-center justify-center border-2 border-gray-900 bg-white text-gray-700 transition-colors hover:bg-gray-100"
                                    :title="t('admin.vainqueurs.edit')"
                                >
                                    <Pencil class="size-4" />
                                </Link>
                                <button
                                    type="button"
                                    class="flex size-8 items-center justify-center border-2 border-gray-900 bg-white text-red-600 transition-colors hover:bg-red-50"
                                    :title="t('admin.vainqueurs.delete')"
                                    @click="askRemove(trophee)"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ConfirmDialog
            v-model:open="confirmOpen"
            destructive
            :title="t('admin.vainqueurs.delete_title')"
            :description="
                tropheeToDelete
                    ? t('admin.vainqueurs.delete_description', {
                          name: tropheeToDelete.company?.name,
                          rank: rankLabels[tropheeToDelete.rank],
                          year: tropheeToDelete.year_of,
                      })
                    : ''
            "
            :confirm-label="t('admin.vainqueurs.delete')"
            :cancel-label="t('admin.vainqueurs.form.cancel')"
            :processing="deleting"
            @confirm="confirmRemove"
        />
    </AdminLayout>
</template>
