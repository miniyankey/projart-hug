<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.collectes.create_title')" />
    <div>
        <h1>{{ t('admin.collectes.create_heading') }}</h1>
    </div>
</template>
