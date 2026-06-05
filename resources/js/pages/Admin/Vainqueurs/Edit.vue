<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import WinnerForm from '@/components/admin/WinnerForm.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index, update } from '@/routes/admin/vainqueurs';

const { t } = useI18n();

const props = defineProps({
    trophee: {
        type: Object,
        required: true,
    },
    companies: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AdminLayout :title="t('admin.vainqueurs.edit_heading')">
        <template #subtitle>{{ t('admin.vainqueurs.edit_subtitle') }}</template>

        <template #actions>
            <Button as-child variant="outline">
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.vainqueurs.form.cancel') }}
                </Link>
            </Button>
        </template>

        <WinnerForm
            method="put"
            :trophee="props.trophee"
            :companies="companies"
            :submit-url="update(props.trophee.id).url"
            :submit-label="t('admin.vainqueurs.form.submit_edit')"
        />
    </AdminLayout>
</template>
