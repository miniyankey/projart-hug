<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '../components/LanguageSwitcher.vue';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('home.title')" />
    <div class="min-h-screen bg-white text-gray-900">
        <header class="flex items-center justify-end border-b border-gray-200 px-6 py-4">
            <LanguageSwitcher />
        </header>
        <main class="mx-auto max-w-3xl px-6 py-16">
            <h1 class="text-3xl font-semibold">{{ t('home.heading') }}</h1>
            <p class="mt-4 text-gray-600">{{ t('home.subheading') }}</p>
        </main>
    </div>
</template>
