<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/layouts/PublicLayout.vue';

const { t } = useI18n();

defineProps({
    company: Object,
    token: String,
});
</script>

<template>
    <PublicLayout :company="company" :token="token">
        <Head :title="t('nav.collecte_info')" />
        <div class="mx-auto max-w-7xl px-6 py-16">
            <h1 class="text-3xl font-semibold text-gray-900">
                {{ t('nav.collecte_info') }}
            </h1>
            <p class="mt-4 text-gray-600">
                {{ t('cobrand.intro', { company: company.name }) }}
            </p>
        </div>
    </PublicLayout>
</template>
