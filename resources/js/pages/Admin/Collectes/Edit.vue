<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import CollectForm from '@/components/admin/CollectForm.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index, update } from '@/routes/admin/collectes';

const props = defineProps({
    collect: {
        type: Object,
        required: true,
    },
    companies: {
        type: Array,
        default: () => [],
    },
    places: {
        type: Array,
        default: () => [],
    },
});

const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('admin.collectes.edit_page.title')">
        <template #subtitle>{{
            t('admin.collectes.edit_page.subtitle')
        }}</template>

        <template #actions>
            <Button as-child variant="admin_outline">
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.collectes.create.cancel') }}
                </Link>
            </Button>
        </template>

        <CollectForm
            method="put"
            :collect="collect"
            :companies="companies"
            :places="places"
            :submit-url="update(props.collect.id).url"
            :submit-label="t('admin.collectes.edit_page.submit')"
        />
    </AdminLayout>
</template>
