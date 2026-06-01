<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import CompanyForm from '@/components/admin/CompanyForm.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index, update } from '@/routes/admin/entreprises';

const { t } = useI18n();

const props = defineProps({
    company: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AdminLayout :title="t('admin.entreprises.edit_page.title')">
        <template #subtitle>{{
            t('admin.entreprises.edit_page.subtitle')
        }}</template>

        <template #actions>
            <Button as-child variant="outline">
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.entreprises.create.cancel') }}
                </Link>
            </Button>
        </template>

        <CompanyForm
            method="put"
            :company="props.company"
            :submit-url="update(props.company.id).url"
            :submit-label="t('admin.entreprises.edit_page.submit')"
        />
    </AdminLayout>
</template>
