<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    vainqueur: Object,
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.vainqueurs.edit_title')" />
    <div>
        <h1>{{ t('admin.vainqueurs.edit_heading') }}</h1>
    </div>
</template>
