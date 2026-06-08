<script setup>
import { CheckCircle2, Clock, Eye, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import AdminIconButton from '@/components/admin/AdminIconButton.vue';
import AdminStatusBadge from '@/components/admin/AdminStatusBadge.vue';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import { useDateFormatter } from '@/composables/useDates';
import { useDeleteConfirm } from '@/composables/useDeleteConfirm';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { destroy, show } from '@/routes/admin/formulaires';

const { t } = useI18n();
const { formatLongDate } = useDateFormatter();

const props = defineProps({
    submissions: {
        type: Array,
        default: () => [],
    },
});

// Filtre client : on bascule entre les demandes de contact et les demandes
// d'organisation de collecte. Tout est déjà chargé côté serveur.
const activeType = ref('contact');

const counts = computed(() => ({
    contact: props.submissions.filter((s) => s.type === 'contact').length,
    collect_request: props.submissions.filter(
        (s) => s.type === 'collect_request',
    ).length,
}));

const filteredSubmissions = computed(() =>
    props.submissions.filter((s) => s.type === activeType.value),
);

const {
    target: submissionToDelete,
    open: confirmOpen,
    processing: deleting,
    ask: askRemove,
    confirm: confirmRemove,
} = useDeleteConfirm((submission) => destroy(submission.id).url);
</script>

<template>
    <AdminLayout :title="t('admin.formulaires.title')">
        <template #subtitle>{{ t('admin.formulaires.subtitle') }}</template>

        <!-- Toggle de filtre contact / collecte -->
        <div
            class="mb-6 inline-flex border-2 border-gray-900 bg-white"
            role="tablist"
        >
            <button
                type="button"
                role="tab"
                :aria-selected="activeType === 'contact'"
                class="border-r-2 border-gray-900 px-4 py-2.5 text-sm font-bold transition-colors"
                :class="
                    activeType === 'contact'
                        ? 'bg-[var(--brand)] text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-100'
                "
                @click="activeType = 'contact'"
            >
                {{ t('admin.formulaires.filter_contact') }}
                <span class="ml-1 opacity-70">({{ counts.contact }})</span>
            </button>
            <button
                type="button"
                role="tab"
                :aria-selected="activeType === 'collect_request'"
                class="px-4 py-2.5 text-sm font-bold transition-colors"
                :class="
                    activeType === 'collect_request'
                        ? 'bg-[var(--brand)] text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-100'
                "
                @click="activeType = 'collect_request'"
            >
                {{ t('admin.formulaires.filter_collect') }}
                <span class="ml-1 opacity-70"
                    >({{ counts.collect_request }})</span
                >
            </button>
        </div>

        <div class="overflow-x-auto border-2 border-gray-900 bg-white">
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b-2 border-gray-900 bg-gray-50 text-xs tracking-wide text-gray-600 uppercase"
                >
                    <tr>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.formulaires.col_company') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.formulaires.col_city') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.formulaires.col_contact') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.formulaires.col_received') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.formulaires.col_status') }}
                        </th>
                        <th class="px-5 py-3 text-right font-bold">
                            {{ t('admin.formulaires.col_actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filteredSubmissions.length === 0">
                        <td
                            colspan="6"
                            class="px-5 py-10 text-center text-gray-500"
                        >
                            {{ t('admin.formulaires.empty') }}
                        </td>
                    </tr>
                    <tr
                        v-for="submission in filteredSubmissions"
                        :key="submission.id"
                        class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50"
                    >
                        <td class="px-5 py-4 font-bold text-gray-900">
                            {{ submission.company_name }}
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ submission.city }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-900">
                                {{ submission.name }}
                            </div>
                            <div class="text-gray-500">
                                {{ submission.contact_email }}
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ formatLongDate(submission.created_at) }}
                        </td>
                        <td class="px-5 py-4">
                            <AdminStatusBadge
                                v-if="submission.handled_at"
                                tone="success"
                            >
                                <CheckCircle2 class="size-3.5" />
                                {{
                                    t('admin.formulaires.handled_by', {
                                        name: submission.handler,
                                    })
                                }}
                            </AdminStatusBadge>
                            <AdminStatusBadge v-else tone="neutral">
                                <Clock class="size-3.5" />
                                {{ t('admin.formulaires.to_handle') }}
                            </AdminStatusBadge>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <AdminIconButton
                                    :href="show(submission.id).url"
                                    :title="t('admin.formulaires.view')"
                                >
                                    <Eye class="size-4" />
                                </AdminIconButton>
                                <AdminIconButton
                                    destructive
                                    :title="t('admin.formulaires.delete')"
                                    @click="askRemove(submission)"
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
            :title="t('admin.formulaires.delete_title')"
            :description="
                submissionToDelete
                    ? t('admin.formulaires.delete_description', {
                          name: submissionToDelete.name,
                      })
                    : ''
            "
            :confirm-label="t('admin.formulaires.delete')"
            :cancel-label="t('admin.formulaires.cancel')"
            :processing="deleting"
            @confirm="confirmRemove"
        />
    </AdminLayout>
</template>
