<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import StepCard from '@/components/cards/StepCard.vue';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();

const root = ref(null);
let ctx;

onMounted(() => {
    if (!root.value) {
        return;
    }

    ctx = gsap.context(() => {
        // Vider les titres avant animation pour que TextPlugin parte de zéro
        gsap.set(
            [
                '.anim-hero-title',
                '.anim-trophee-title',
                '.anim-label-title',
                '.anim-steps-title',
                '.anim-faq-title',
            ],
            { text: '' },
        );

        // ── Hero ──────────────────────────────────────────────
        gsap.timeline({ defaults: { ease: 'power3.out' } })
            .from('.anim-hero-title', { opacity: 0, y: 50, duration: 0.9 })
            .from(
                '.anim-hero-subtitle',
                { opacity: 0, y: 25, duration: 0.7 },
                '-=0.5',
            )
            .from(
                '.anim-hero-game-cta',
                { opacity: 0, y: 20, duration: 0.6 },
                '-=0.4',
            )
            .from(
                '.anim-hero-ctas',
                { opacity: 0, y: 20, duration: 0.6 },
                '-=0.35',
            );

        gsap.to('.anim-hero-title', {
            text: t('home.hero.title'),
            duration: 1.4,
            ease: 'none',
        });

        // ── Section Trophée ───────────────────────────────────
        gsap.from(
            '.anim-trophee-text > span, .anim-trophee-text > h2, .anim-trophee-text > p',
            {
                opacity: 0,
                x: -50,
                stagger: 0.18,
                duration: 0.75,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '.anim-trophee-text',
                    start: 'top 80%',
                    once: true,
                },
            },
        );

        gsap.from('.anim-trophee-btn', {
            opacity: 0,
            y: 10,
            duration: 0.5,
            delay: 0.55,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: '.anim-trophee-text',
                start: 'top 80%',
                once: true,
            },
        });

        gsap.from('.anim-podium-1', {
            opacity: 0,
            scale: 0.97,
            duration: 1,
            ease: 'power3.out',
            scrollTrigger: { trigger: '.anim-podium-1', start: 'top 80%' },
        });

        gsap.to('.anim-trophee-title', {
            text: t('home.trophee.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-trophee-text',
                start: 'top 75%',
                once: true,
            },
        });

        // ── Section Label CTS ─────────────────────────────────
        gsap.from('.anim-label-img', {
            x: -60,
            opacity: 0,
            scale: 0.95,
            duration: 1,
            ease: 'power3.out',
            scrollTrigger: { trigger: '.anim-label-img', start: 'top 80%' },
        });

        gsap.from('.anim-label-text > *', {
            opacity: 0,
            x: 50,
            stagger: 0.18,
            duration: 0.75,
            ease: 'power3.out',
            scrollTrigger: { trigger: '.anim-label-text', start: 'top 80%' },
        });

        gsap.to('.anim-label-title', {
            text: t('home.label.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-label-text',
                start: 'top 80%',
                once: true,
            },
        });

        // ── Section étapes ────────────────────────────────────
        gsap.from('.anim-steps-header', {
            opacity: 0,
            y: 30,
            duration: 0.7,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: '.anim-steps-header',
                start: 'top 80%',
            },
        });

        gsap.from('.anim-step-card', {
            opacity: 0,
            y: 40,
            stagger: 0.12,
            duration: 0.65,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: '.anim-step-card',
                start: 'top 85%',
            },
        });

        gsap.from('.anim-steps-cta', {
            opacity: 0,
            y: 20,
            duration: 0.6,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-steps-cta', start: 'top 90%' },
        });

        gsap.to('.anim-steps-title', {
            text: t('home.steps.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-steps-header',
                start: 'top 80%',
                once: true,
            },
        });

        // ── Section FAQ ───────────────────────────────────────
        gsap.to('.anim-faq-title', {
            text: t('home.faq.title'),
            duration: 1,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-faq-title',
                start: 'top 80%',
                once: true,
            },
        });

        gsap.from('.anim-faq-title', {
            opacity: 0,
            y: 30,
            duration: 0.7,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-faq-title', start: 'top 80%' },
        });

        gsap.from('.anim-faq-item', {
            opacity: 0,
            y: 20,
            stagger: 0.1,
            duration: 0.6,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-faq-item', start: 'top 85%' },
        });
        ScrollTrigger.refresh();
    }, root.value);
});

onUnmounted(() => {
    ctx?.revert();
});
</script>

<template>
    <PublicLayout>
        <Head :title="t('home.title')" />

        <div ref="root">
            <!-- Hero -->
            <section
                class="py-24 lg:py-36"
                style="
                    background-image: url('/img/Bg.webp');
                    background-size: cover;
                    background-position: center;
                "
            >
                <div class="mx-auto max-w-7xl px-6">
                    <div class="max-w-xl">
                        <h1
                            class="anim-hero-title font-pixel text-[1.35rem] leading-loose text-gray-900"
                        >
                            {{ t('home.hero.title') }}
                        </h1>
                        <p
                            class="anim-hero-subtitle mt-8 max-w-md leading-relaxed text-gray-700"
                        >
                            {{ t('home.hero.subtitle') }}
                        </p>
                        <p
                            class="anim-hero-game-cta mt-6 max-w-md font-semibold text-gray-900"
                        >
                            {{ t('home.hero.game_cta') }}
                        </p>
                        <div class="anim-hero-ctas mt-8 flex flex-wrap gap-4">
                            <Button variant="pixel_violet">
                                <Link :href="routes.collecte.url()">
                                    {{ t('home.hero.cta_primary') }}
                                </Link>
                            </Button>
                            <Button
                                variant="pixel_blue"
                                class="border border-blue-600 text-white"
                            >
                                <Link :href="routes.eligibilite.url()">
                                    {{ t('home.hero.cta_secondary') }}
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section Trophée -->
            <section class="bg-white py-32">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="grid items-start gap-12 lg:grid-cols-2">
                        <div class="anim-trophee-text">
                            <h2
                                class="anim-trophee-title mt-4 text-3xl font-semibold text-gray-900"
                            >
                                {{ t('home.trophee.title') }}
                            </h2>
                            <p class="mt-4 leading-relaxed text-gray-600">
                                {{ t('home.trophee.description') }}
                            </p>
                            <Button
                                as-child
                                variant="pixel_violet"
                                class="anim-trophee-btn mt-6"
                            >
                                <Link :href="routes.trophee.url()">
                                    {{ t('home.trophee.link') }}
                                </Link>
                            </Button>
                        </div>

                        <div class="anim-podium-1 overflow-hidden rounded-xl">
                            <img
                                src="/img/winners.webp"
                                :alt="t('home.trophee.winners_title')"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section Label CTS -->
            <section class="py-32" style="background-color: #eff6ff">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="grid items-center gap-12 lg:grid-cols-2">
                        <div
                            class="anim-label-img order-2 flex items-center justify-center lg:order-1"
                        >
                            <img
                                src="/img/label-cts.png"
                                :alt="t('home.label.label')"
                                class="max-h-110 w-auto object-contain"
                            />
                        </div>

                        <div class="anim-label-text order-1 lg:order-2">
                            <h2
                                class="anim-label-title mt-4 text-3xl font-semibold text-gray-900"
                            >
                                {{ t('home.label.title') }}
                            </h2>
                            <p class="mt-4 leading-relaxed text-gray-600">
                                {{ t('home.label.description') }}
                            </p>
                            <p
                                class="mt-6 text-2xl font-semibold text-gray-900"
                            >
                                {{
                                    t('home.label.certified_count', {
                                        count: 47,
                                    })
                                }}
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <Button as-child variant="pixel_violet">
                                    <Link :href="routes.certification.url()">
                                        {{ t('home.label.learn_more') }}
                                    </Link>
                                </Button>
                                <Button as-child variant="pixel_blue">
                                    <Link :href="routes.collecte.url()">
                                        {{ t('home.label.link') }}
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section étapes trophée -->
            <section
                class="py-28"
                style="
                    background-color: #2a5f5f;
                    background-image: radial-gradient(
                        circle,
                        rgba(255, 255, 255, 0.07) 1px,
                        transparent 1px
                    );
                    background-size: 28px 28px;
                "
            >
                <div class="mx-auto max-w-5xl px-6 text-center">
                    <div class="anim-steps-header">
                        <h2
                            class="anim-steps-title mx-auto max-w-xl font-pixel text-[1rem] leading-loose text-white"
                        >
                            {{ t('home.steps.title') }}
                        </h2>
                        <p class="mt-5 mb-12 text-sm text-teal-100">
                            {{ t('home.steps.subtitle') }}
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-4 lg:flex lg:items-stretch lg:gap-2"
                    >
                        <StepCard
                            :step="1"
                            :label="t('home.steps.step1')"
                            class="anim-step-card w-full lg:w-52"
                        />
                        <span
                            class="hidden shrink-0 self-center font-pixel text-xl text-white/50 lg:inline"
                            >></span
                        >
                        <StepCard
                            :step="2"
                            :label="t('home.steps.step2')"
                            class="anim-step-card w-full lg:w-52"
                        />
                        <span
                            class="hidden shrink-0 self-center font-pixel text-xl text-white/50 lg:inline"
                            >></span
                        >
                        <StepCard
                            :step="3"
                            :label="t('home.steps.step3')"
                            class="anim-step-card w-full lg:w-52"
                        />
                        <span
                            class="hidden shrink-0 self-center font-pixel text-xl text-white/50 lg:inline"
                            >></span
                        >
                        <StepCard
                            :step="4"
                            :label="t('home.steps.step4')"
                            class="anim-step-card w-full lg:w-52"
                        />
                    </div>

                    <div class="anim-steps-cta mt-12">
                        <Button as-child variant="pixel_violet">
                            <Link :href="routes.collecte.url()">
                                {{ t('home.steps.cta') }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>

            <!-- Section FAQ -->
            <section class="py-32" style="background-color: #dbeafe">
                <div class="mx-auto max-w-3xl px-6">
                    <h2
                        class="anim-faq-title mb-10 text-center text-2xl font-semibold text-gray-900"
                    >
                        {{ t('home.faq.title') }}
                    </h2>
                    <Accordion type="single" collapsible>
                        <AccordionItem
                            v-for="(item, i) in 5"
                            :key="i"
                            :value="`faq-${i}`"
                            class="anim-faq-item"
                        >
                            <AccordionTrigger
                                class="text-left font-medium text-gray-900"
                            >
                                {{ t(`home.faq.q${i + 1}`) }}
                            </AccordionTrigger>
                            <AccordionContent
                                class="leading-relaxed text-gray-600"
                            >
                                {{ t(`home.faq.a${i + 1}`) }}
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
