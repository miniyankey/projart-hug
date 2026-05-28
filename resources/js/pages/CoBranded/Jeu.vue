<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/layouts/PublicLayout.vue';

const { t } = useI18n();

defineProps({
    brandName: String,
    token: String,
});
</script>

<template>
    <PublicLayout>
        <Head :title="t('eligibilite.title')" />
        <div class="mx-auto max-w-7xl px-6 py-16">
            <h1 class="text-3xl font-semibold text-gray-900">
                {{ t('eligibilite.title') }}
            </h1>
            <p class="mt-4 text-gray-600">{{ t('eligibilite.intro') }}</p>
        </div>
    </PublicLayout>
</template>
