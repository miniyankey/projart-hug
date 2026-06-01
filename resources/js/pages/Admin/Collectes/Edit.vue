<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    collecte: Object,
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.collectes.edit_title')" />
    <div>
        <h1>{{ t('admin.collectes.edit_heading') }}</h1>
    </div>
</template>
