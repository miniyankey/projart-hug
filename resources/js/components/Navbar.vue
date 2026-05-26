<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();
</script>

<template>
    <header class="border-b border-gray-200">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <Link
                :href="routes.home.url()"
                class="text-sm font-semibold tracking-wide text-gray-900 uppercase"
            >
                CTS — Trophée de la générosité
            </Link>

            <div class="flex items-center gap-8">
                <Link
                    :href="routes.trophee.url()"
                    class="text-sm text-gray-600 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.trophee') }}
                </Link>
                <Link
                    :href="routes.collecte.url()"
                    class="text-sm text-gray-600 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.collecte') }}
                </Link>
                <Link
                    :href="routes.inscription.url()"
                    class="text-sm text-gray-600 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.inscription') }}
                </Link>
                <LanguageSwitcher />
            </div>
        </nav>
    </header>
</template>
