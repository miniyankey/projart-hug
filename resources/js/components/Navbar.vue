<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { Button } from '@/components/ui/button';
import hugLogo from '@/../images/logos/hug.png';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();
</script>

<template>
    <header class="border-b border-gray-200 bg-white">
        <nav
            class="mx-auto flex max-w-7xl items-center justify-between gap-8 px-6 py-4"
        >
            <Link
                :href="routes.home.url()"
                class="flex shrink-0 items-center"
                aria-label="Hôpitaux Universitaires Genève"
            >
                <img
                    :src="hugLogo"
                    alt="Hôpitaux Universitaires Genève"
                    class="h-12 w-auto"
                />
            </Link>

            <div class="flex items-center gap-10">
                <Link
                    :href="routes.home.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.home') }}
                </Link>
                <Link
                    :href="routes.eligibilite.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.eligibilite') }}
                </Link>
                <Link
                    :href="routes.trophee.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.trophee') }}
                </Link>
                <Link
                    :href="routes.certification.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.certification') }}
                </Link>
            </div>

            <div class="flex items-center gap-6">
                <Button as-child variant="cta" size="cta">
                    <Link :href="routes.collecte.url()">
                        {{ t('nav.cta_creer_collecte') }}
                    </Link>
                </Button>
                <LanguageSwitcher />
            </div>
        </nav>
    </header>
</template>
