<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.vainqueurs.create_title')" />
    <div>
        <h1>{{ t('admin.vainqueurs.create_heading') }}</h1>
    </div>
</template>
