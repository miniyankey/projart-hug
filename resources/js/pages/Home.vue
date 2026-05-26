<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

defineOptions({ layout: PublicLayout });

const { t } = useI18n();


//mettre les infos des gagnants depuis la BD, ici des données fictives pour l'affichage
const placeholderWinners = [
    { year: 2024, company: 'Entreprise A', category: 'Grande entreprise' },
    { year: 2023, company: 'Entreprise B', category: 'PME' },
    { year: 2022, company: 'Entreprise C', category: 'Grande entreprise' },
];
</script>

<template>
    <Head :title="t('home.title')" />

    <!-- Hero -->
    <section class="border-b border-gray-100 py-36 lg:py-48">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <p class="mb-4 text-sm font-medium tracking-widest text-gray-400 uppercase">
                {{ t('home.hero.eyebrow') }}
            </p>
            <h1 class="text-4xl leading-tight font-semibold text-gray-900 lg:text-5xl">
                {{ t('home.hero.title') }}
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-gray-600">
                {{ t('home.hero.subtitle') }}
            </p>
            <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
                <Button as-child size="lg">
                    <Link :href="routes.inscription.url()">
                        {{ t('home.hero.cta_primary') }}
                    </Link>
                </Button>
                <Button as-child variant="outline" size="lg">
                    <Link :href="routes.trophee.url()">
                        {{ t('home.hero.cta_secondary') }}
                    </Link>
                </Button>
            </div>
        </div>
    </section>

    <!-- Section Trophée -->
    <section class="bg-gray-50 py-32">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid items-start gap-12 lg:grid-cols-2">
                <div>
                    <span
                        class="inline-block rounded border border-gray-300 px-2.5 py-1 text-xs font-medium tracking-wide text-gray-600 uppercase"
                    >
                        {{ t('home.trophee.label') }}
                    </span>
                    <h2 class="mt-4 text-3xl font-semibold text-gray-900">
                        {{ t('home.trophee.title') }}
                    </h2>
                    <p class="mt-4 leading-relaxed text-gray-600">
                        {{ t('home.trophee.description') }}
                    </p>
                    <Link
                        :href="routes.trophee.url()"
                        class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-gray-900 underline-offset-4 hover:underline"
                    >
                        {{ t('home.trophee.link') }}
                        <span aria-hidden="true">→</span>
                    </Link>
                </div>

                <div class="space-y-3">
                    <p class="mb-4 text-sm font-medium text-gray-500">
                        {{ t('home.trophee.winners_title') }}
                    </p>
                    <div
                        v-for="winner in placeholderWinners"
                        :key="winner.year"
                        class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-5 py-4"
                    >
                        <div>
                            <p class="font-medium text-gray-900">{{ winner.company }}</p>
                            <p class="mt-0.5 text-sm text-gray-500">{{ winner.category }}</p>
                        </div>
                        <span class="text-2xl font-semibold text-gray-300">{{ winner.year }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Label CTS -->
    <section class="py-32">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="order-2 lg:order-1">
                    <div
                        class="flex h-40 items-center justify-center rounded-lg border border-gray-200 bg-gray-50"
                    >
                        <p class="text-sm text-gray-400">
                            {{ t('home.label.visual_placeholder') }}
                        </p>
                    </div>
                </div>

                <div class="order-1 lg:order-2">
                    <span
                        class="inline-block rounded border border-gray-300 px-2.5 py-1 text-xs font-medium tracking-wide text-gray-600 uppercase"
                    >
                        {{ t('home.label.label') }}
                    </span>
                    <h2 class="mt-4 text-3xl font-semibold text-gray-900">
                        {{ t('home.label.title') }}
                    </h2>
                    <p class="mt-4 leading-relaxed text-gray-600">
                        {{ t('home.label.description') }}
                    </p>
                    <p class="mt-6 text-2xl font-semibold text-gray-900">
                        {{ t('home.label.certified_count', { count: 47 }) }}
                    </p>
                    <Link
                        :href="routes.collecte.url()"
                        class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-gray-900 underline-offset-4 hover:underline"
                    >
                        {{ t('home.label.link') }}
                        <span aria-hidden="true"></span>
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Chiffres clés -->
    <section class="border-y border-gray-100 bg-gray-50 py-32">
        <div class="mx-auto max-w-7xl px-6">
            <h2 class="text-center text-2xl font-semibold text-gray-900">
                {{ t('home.stats.title') }}
            </h2>
            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-3">
                <div class="text-center">
                    <p class="text-5xl font-semibold text-gray-900">
                        {{ t('home.stats.stat1_value') }}
                    </p>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">
                        {{ t('home.stats.stat1_label') }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-5xl font-semibold text-gray-900">
                        {{ t('home.stats.stat2_value') }}
                    </p>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">
                        {{ t('home.stats.stat2_label') }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-5xl font-semibold text-gray-900">
                        {{ t('home.stats.stat3_value') }}
                    </p>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">
                        {{ t('home.stats.stat3_label') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA final -->
    <section class="bg-gray-900 py-32">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <h2 class="text-3xl font-semibold text-white">
                {{ t('home.cta.title') }}
            </h2>
            <p class="mt-4 text-gray-400">
                {{ t('home.cta.subtitle') }}
            </p>
            <div class="mt-10">
                <Button as-child size="lg" variant="ghost" class="border border-white text-white hover:bg-white! hover:text-gray-900!">
                    <Link :href="routes.inscription.url()">
                        {{ t('home.cta.button') }}
                    </Link>
                </Button>
            </div>
        </div>
    </section>
</template>
