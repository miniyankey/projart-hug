<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import CompanyForm from '@/components/admin/CompanyForm.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index, store } from '@/routes/admin/entreprises';

const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('admin.entreprises.create.title')">
        <template #subtitle>{{
            t('admin.entreprises.create.subtitle')
        }}</template>

        <template #actions>
            <Button as-child variant="admin_outline">
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.entreprises.create.cancel') }}
                </Link>
            </Button>
        </template>

        <CompanyForm
            method="post"
            :submit-url="store.url()"
            :submit-label="t('admin.entreprises.create.submit')"
        />
    </AdminLayout>
</template>
