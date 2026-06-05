<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import WinnerForm from '@/components/admin/WinnerForm.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index, store } from '@/routes/admin/vainqueurs';

const { t } = useI18n();

defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AdminLayout :title="t('admin.vainqueurs.create_heading')">
        <template #subtitle>{{
            t('admin.vainqueurs.create_subtitle')
        }}</template>

        <template #actions>
            <Button as-child variant="outline">
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.vainqueurs.form.cancel') }}
                </Link>
            </Button>
        </template>

        <WinnerForm
            method="post"
            :companies="companies"
            :submit-url="store.url()"
            :submit-label="t('admin.vainqueurs.form.submit_create')"
        />
    </AdminLayout>
</template>
