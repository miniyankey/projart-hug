<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PodiumCard from '@/components/PodiumCard.vue';
import StepCard from '@/components/StepCard.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';


const { t } = useI18n();
</script>

<template>
    <PublicLayout>

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
                    <Button variant="pixel_violet">
                        <Link :href="routes.collecte.url()">
                            {{ t('home.hero.cta_primary') }}
                        </Link>
                    </Button>
                    <Button variant="pixel_blue">
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
                            class="inline-block rounded border border-gray-300 px-2.5 py-1 text-xs font-medium tracking-wide text-gray-600 uppercase">
                            {{ t('home.trophee.label') }}
                        </span>
                        <h2 class="mt-4 text-3xl font-semibold text-gray-900">
                            {{ t('home.trophee.title') }}
                        </h2>
                        <p class="mt-4 leading-relaxed text-gray-600">
                            {{ t('home.trophee.description') }}
                        </p>
                        <Button as-child variant="pixel_violet" class="mt-6">
                            <Link :href="routes.trophee.url()">
                                {{ t('home.trophee.link') }}
                            </Link>
                        </Button>
                    </div>

                    <div class="flex items-end justify-center gap-3">
                        <PodiumCard :rank="2" logo-src="/img/migros.png" logo-alt="Migros"
                            :category="t('home.trophee.winners_title')"
                            description="A gagné pour avoir cumulé plus de 500 dons dans l'année." class="w-40" />
                        <PodiumCard :rank="1" logo-src="/img/rolex.svg" logo-alt="Rolex"
                            :category="t('home.trophee.winners_title')"
                            description="A gagné pour avoir cumulé plus de 500 dons dans l'année." class="w-40" />
                        <PodiumCard :rank="3" logo-src="/img/nestle.png" logo-alt="Nestlé"
                            :category="t('home.trophee.winners_title')"
                            description="A gagné pour avoir cumulé plus de 500 dons dans l'année." class="w-40" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Label CTS -->
        <section class="py-32">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div class="order-2 lg:order-1">
                        <div class="flex h-40 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">
                            <p class="text-sm text-gray-400">
                                {{ t('home.label.visual_placeholder') }}
                            </p>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2">
                        <span
                            class="inline-block rounded border border-gray-300 px-2.5 py-1 text-xs font-medium tracking-wide text-gray-600 uppercase">
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
                        <Button as-child variant="pixel_blue" class="mt-4">
                            <Link :href="routes.collecte.url()">
                                {{ t('home.label.link') }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        

        <!-- Section étapes trophée -->
        <section
            class="py-16"
            style="background-color: #6B21A8; background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px); background-size: 28px 28px;"
        >
            <div class="mx-auto max-w-5xl px-6 text-center">
                <h2 class="font-pixel mx-auto max-w-xl text-[1rem] leading-loose text-white">
                    {{ t('home.steps.title') }}
                </h2>
                <p class="mt-5 mb-12 text-sm text-purple-200">
                    {{ t('home.steps.subtitle') }}
                </p>

                <div class="flex items-stretch justify-center gap-2">
                    <StepCard :step="1" :label="t('home.steps.step1')" class="w-52" />
                    <span class="font-pixel self-center shrink-0 text-xl text-white/50">></span>
                    <StepCard :step="2" :label="t('home.steps.step2')" class="w-52" />
                    <span class="font-pixel self-center shrink-0 text-xl text-white/50">></span>
                    <StepCard :step="3" :label="t('home.steps.step3')" class="w-52" />
                    <span class="font-pixel self-center shrink-0 text-xl text-white/50">></span>
                    <StepCard :step="4" :label="t('home.steps.step4')" class="w-52" />
                </div>

                <div class="mt-12">
                    <Button as-child variant="pixel_violet">
                        <Link :href="routes.inscription.url()">
                            > {{ t('home.steps.cta') }}
                        </Link>
                    </Button>
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
                    <Button as-child size="lg" variant="ghost"
                        class="border border-white text-white hover:bg-white! hover:text-gray-900!">
                        <Link :href="routes.inscription.url()">
                            {{ t('home.cta.button') }}
                        </Link>
                    </Button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
