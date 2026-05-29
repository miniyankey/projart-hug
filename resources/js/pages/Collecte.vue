<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import CollectForm from '@/components/CollectForm.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

const { t } = useI18n();
</script>

<template>
    <PublicLayout>
        <Head :title="t('collecte.title')" />
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-16">
            <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">
                {{ t('collecte.title') }}
            </h1>
            <p class="mt-4 text-base leading-relaxed text-gray-600">
                {{ t('collecte.intro') }}
            </p>

            <div class="mt-10">
                <CollectForm />
            </div>
        </div>
    </PublicLayout>
</template>
