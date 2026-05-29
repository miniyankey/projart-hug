<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PodiumCard from '@/components/PodiumCard.vue';
import SpeechBubble from '@/components/SpeechBubble.vue';
import StepCard from '@/components/StepCard.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';


const { t } = useI18n();
</script>

<template>
    <PublicLayout>

        <Head :title="t('home.title')" />

        <!-- Hero -->
        <section style="background-color: #EDE9F8" class="py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <!-- Texte -->
                    <div>
                        <h1 class="font-pixel text-[1.35rem] leading-loose text-gray-900">
                            {{ t('home.hero.title') }}
                        </h1>
                        <p class="mt-8 max-w-md leading-relaxed text-gray-700">
                            {{ t('home.hero.subtitle') }}
                        </p>
                        <p class="mt-6 max-w-md font-semibold text-gray-900">
                            {{ t('home.hero.game_cta') }}
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <Button variant="pixel_violet">
                                <Link :href="routes.collecte.url()">
                                    {{ t('home.hero.cta_primary') }}
                                </Link>
                            </Button>
                            <Button variant="pixel_blue" class="border border-blue-600 text-white">
                                <Link :href="routes.eligibilite.url()">
                                    {{ t('home.hero.cta_secondary') }}
                                </Link>
                            </Button>
                        </div>
                    </div>

                    <!-- Mascotte -->
                    <div class="flex flex-col items-center lg:items-end">
                        <SpeechBubble :text="t('home.hero.bubble')" class="mb-8 max-w-xs self-start" />
                        <img
                            src="/img/mascotte.png"
                            :alt="t('home.hero.mascot_alt')"
                            class="w-72 lg:w-96"
                            style="image-rendering: pixelated"
                        />
                    </div>
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
                            description="A gagné pour avoir cumulé plus de 500 dons dans l'année." class="hidden w-40 lg:flex" />
                        <PodiumCard :rank="1" logo-src="/img/rolex.svg" logo-alt="Rolex"
                            :category="t('home.trophee.winners_title')"
                            description="A gagné pour avoir cumulé plus de 500 dons dans l'année." class="w-40" />
                        <PodiumCard :rank="3" logo-src="/img/nestle.png" logo-alt="Nestlé"
                            :category="t('home.trophee.winners_title')"
                            description="A gagné pour avoir cumulé plus de 500 dons dans l'année." class="hidden w-40 lg:flex" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Label CTS -->
        <section class="py-32">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div class="order-2 lg:order-1">
                        <div class="flex items-center justify-center">
                            <img src="/img/label-cts.png" :alt="t('home.label.label')" class="max-h-110 w-auto object-contain" />
                        </div>
                    </div>

                    <div class="order-1 lg:order-2">
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

                <div class="grid grid-cols-2 gap-4 lg:flex lg:items-stretch lg:gap-2">
                    <StepCard :step="1" :label="t('home.steps.step1')" class="w-full lg:w-52" />
                    <span class="font-pixel hidden self-center shrink-0 text-xl text-white/50 lg:inline">></span>
                    <StepCard :step="2" :label="t('home.steps.step2')" class="w-full lg:w-52" />
                    <span class="font-pixel hidden self-center shrink-0 text-xl text-white/50 lg:inline">></span>
                    <StepCard :step="3" :label="t('home.steps.step3')" class="w-full lg:w-52" />
                    <span class="font-pixel hidden self-center shrink-0 text-xl text-white/50 lg:inline">></span>
                    <StepCard :step="4" :label="t('home.steps.step4')" class="w-full lg:w-52" />
                </div>

                <div class="mt-12">
                    <Button as-child variant="pixel_violet">
                        <Link :href="routes.collecte.url()">
                            > {{ t('home.steps.cta') }}
                        </Link>
                    </Button>
                </div>
            </div>
        </section>

        <!-- Section FAQ -->
        <section class="py-24">
            <div class="mx-auto max-w-3xl px-6">
                <h2 class="mb-10 text-center text-2xl font-semibold text-gray-900">
                    {{ t('home.faq.title') }}
                </h2>
                <Accordion type="single" collapsible>
                    <AccordionItem v-for="(item, i) in 5" :key="i" :value="`faq-${i}`">
                        <AccordionTrigger class="text-left font-medium text-gray-900">
                            {{ t(`home.faq.q${i + 1}`) }}
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 leading-relaxed">
                            {{ t(`home.faq.a${i + 1}`) }}
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </section>
    </PublicLayout>
</template>
