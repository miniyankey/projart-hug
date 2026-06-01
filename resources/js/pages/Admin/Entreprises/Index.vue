<script setup>
import { Link } from '@inertiajs/vue3';
import { Award, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import CobrandLink from '@/components/admin/CobrandLink.vue';
import CompanyAvatar from '@/components/admin/CompanyAvatar.vue';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import { useDeleteConfirm } from '@/composables/useDeleteConfirm';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { create, destroy, edit } from '@/routes/admin/entreprises';

const { t } = useI18n();

defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
});

const {
    target: companyToDelete,
    open: confirmOpen,
    processing: deleting,
    ask: askRemove,
    confirm: confirmRemove,
} = useDeleteConfirm((company) => destroy(company.id).url);
</script>

<template>
    <AdminLayout :title="t('admin.entreprises.title')">
        <template #subtitle>{{ t('admin.entreprises.subtitle') }}</template>

        <template #actions>
            <Button
                as-child
                class="border-2 border-gray-900 bg-[var(--brand)] text-white hover:bg-[var(--brand-hover)]"
            >
                <Link :href="create.url()">
                    <Plus class="size-4" />
                    {{ t('admin.entreprises.new') }}
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
                            {{ t('admin.entreprises.col_name') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.entreprises.col_contact') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.entreprises.col_link') }}
                        </th>
                        <th class="px-5 py-3 font-bold">
                            {{ t('admin.entreprises.col_label') }}
                        </th>
                        <th class="px-5 py-3 text-right font-bold">
                            {{ t('admin.entreprises.col_actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="companies.length === 0">
                        <td
                            colspan="5"
                            class="px-5 py-10 text-center text-gray-500"
                        >
                            {{ t('admin.entreprises.empty') }}
                        </td>
                    </tr>
                    <tr
                        v-for="company in companies"
                        :key="company.id"
                        class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50"
                    >
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <CompanyAvatar
                                    :logo-url="company.logo_url"
                                    :color="company.color"
                                    :name="company.name"
                                />
                                <span class="font-bold text-gray-900">{{
                                    company.name
                                }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div
                                v-if="company.contact_name"
                                class="font-semibold text-gray-900"
                            >
                                {{ company.contact_name }}
                            </div>
                            <div class="text-gray-500">
                                {{ company.email_contact }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <CobrandLink
                                :slug="company.slug"
                                :token="company.token"
                            />
                        </td>
                        <td class="px-5 py-4">
                            <span
                                v-if="company.is_labelled"
                                class="inline-flex items-center gap-1.5 border-2 border-amber-500 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700"
                            >
                                <Award class="size-3.5" />
                                {{ t('admin.entreprises.label_active') }}
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 border-2 border-gray-300 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-500"
                            >
                                {{ t('admin.entreprises.label_inactive') }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="edit(company.id).url"
                                    class="flex size-8 items-center justify-center border-2 border-gray-900 bg-white text-gray-700 transition-colors hover:bg-gray-100"
                                    :title="t('admin.entreprises.edit')"
                                >
                                    <Pencil class="size-4" />
                                </Link>
                                <button
                                    type="button"
                                    class="flex size-8 items-center justify-center border-2 border-gray-900 bg-white text-red-600 transition-colors hover:bg-red-50"
                                    :title="t('admin.entreprises.delete')"
                                    @click="askRemove(company)"
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
            :title="t('admin.entreprises.delete_title')"
            :description="
                companyToDelete
                    ? t('admin.entreprises.delete_description', {
                          name: companyToDelete.name,
                      })
                    : ''
            "
            :confirm-label="t('admin.entreprises.delete')"
            :cancel-label="t('admin.entreprises.create.cancel')"
            :processing="deleting"
            @confirm="confirmRemove"
        />
    </AdminLayout>
</template>
