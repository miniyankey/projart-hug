<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.kpi.index_title')" />
    <div>
        <h1>{{ t('admin.kpi.title') }}</h1>
    </div>
</template>
