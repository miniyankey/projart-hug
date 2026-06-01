<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import CollectForm from '@/components/admin/CollectForm.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index, store } from '@/routes/admin/collectes';

const { t } = useI18n();

defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
    places: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AdminLayout :title="t('admin.collectes.create.title')">
        <template #subtitle>{{
            t('admin.collectes.create.subtitle')
        }}</template>

        <template #actions>
            <Button as-child variant="outline">
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.collectes.create.cancel') }}
                </Link>
            </Button>
        </template>

        <CollectForm
            method="post"
            :companies="companies"
            :places="places"
            :submit-url="store.url()"
            :submit-label="t('admin.collectes.create.submit')"
        />
    </AdminLayout>
</template>
