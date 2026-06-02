<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    token: String,
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.kpi.show_title')" />
    <div>
        <h1>{{ t('admin.kpi.show_heading') }}</h1>
    </div>
</template>
