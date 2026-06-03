<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import LabelStepCard from '@/components/cards/LabelStepCard.vue';
import MascottePopup from '@/components/MascottePopup.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import {
    collecte as cobrandCollecte,
    jeu as cobrandJeu,
} from '@/routes/cobrand';

const { t } = useI18n();

const props = defineProps({
    company: Object,
    token: String,
});

const routeParams = computed(() => ({
    brandName: props.company?.slug,
    token: props.token,
}));

const steps = [
    {
        n: 1,
        title: t('don_sang.deroulement.step1_title'),
        desc: t('don_sang.deroulement.step1_desc'),
    },
    {
        n: 2,
        title: t('don_sang.deroulement.step2_title'),
        desc: t('don_sang.deroulement.step2_desc'),
    },
    {
        n: 3,
        title: t('don_sang.deroulement.step3_title'),
        desc: t('don_sang.deroulement.step3_desc'),
    },
    {
        n: 4,
        title: t('don_sang.deroulement.step4_title'),
        desc: t('don_sang.deroulement.step4_desc'),
    },
];

const statLabels = [
    t('don_sang.impact.stat1_label'),
    t('don_sang.impact.stat2_label'),
    t('don_sang.impact.stat3_label'),
];

const statConfigs = [
    { target: 3, suffix: '' },
    { target: 45, suffix: ' min' },
    { target: 500, suffix: ' mL' },
];

const statDisplayValues = ref(['0', '0 min', '0 mL']);

const root = ref(null);
let ctx;

onMounted(() => {
    if (!root.value) {
        return;
    }

    ctx = gsap.context(() => {
        // Vider les titres pour TextPlugin
        gsap.set(
            ['.anim-hero-title', '.anim-deroulement-title', '.anim-cta-title'],
            { text: '' },
        );

        // ── Hero - timeline séquentielle ──────────────────────
        gsap.timeline({ defaults: { ease: 'power3.out' } })
            .from('.anim-hero-eyebrow', { opacity: 0, y: 20, duration: 0.6 })
            .from(
                '.anim-hero-title',
                { opacity: 0, y: 40, duration: 0.8 },
                '-=0.3',
            )
            .from(
                '.anim-hero-subtitle',
                { opacity: 0, y: 25, duration: 0.7 },
                '-=0.5',
            )
            .from(
                '.anim-hero-ctas',
                { opacity: 0, y: 20, duration: 0.6 },
                '-=0.35',
            );

        gsap.to('.anim-hero-title', {
            text: t('don_sang.hero.title'),
            duration: 1.2,
            ease: 'none',
            delay: 0.3,
        });

        // ── Stats - fade + compteur animé (pattern Trophée) ───
        gsap.from('.anim-stat', {
            opacity: 0,
            y: 40,
            stagger: 0.18,
            duration: 0.7,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-stats-section', start: 'top 80%' },
        });

        statConfigs.forEach((stat, i) => {
            const counter = { n: 0 };
            gsap.to(counter, {
                n: stat.target,
                duration: 1.8,
                ease: 'power2.out',
                delay: i * 0.18,
                scrollTrigger: {
                    trigger: '.anim-stats-section',
                    start: 'top 80%',
                },
                onUpdate() {
                    statDisplayValues.value[i] =
                        `${Math.round(counter.n)}${stat.suffix}`;
                },
            });
        });

        // ── Déroulement - header + cards ──────────────────────
        gsap.from('.anim-deroulement-header', {
            opacity: 0,
            y: 30,
            duration: 0.7,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: '.anim-deroulement-header',
                start: 'top 80%',
            },
        });

        gsap.to('.anim-deroulement-title', {
            text: t('don_sang.deroulement.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-deroulement-header',
                start: 'top 80%',
                once: true,
            },
        });

        gsap.from('.anim-step-card', {
            opacity: 0,
            y: 50,
            stagger: 0.15,
            duration: 0.75,
            ease: 'power3.out',
            scrollTrigger: { trigger: '.anim-step-card', start: 'top 85%' },
        });

        // ── CTA finale ────────────────────────────────────────
        gsap.from('.anim-cta-box', {
            opacity: 0,
            y: 30,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-cta-box', start: 'top 80%' },
        });

        gsap.to('.anim-cta-title', {
            text: t('don_sang.cta.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-cta-box',
                start: 'top 80%',
                once: true,
            },
        });
    }, root.value);
});

onUnmounted(() => {
    ctx?.revert();
});
</script>

<template>
    <PublicLayout :company="company" :token="token">
        <Head :title="t('nav.don_sang_info')" />

        <div ref="root">
            <!-- Hero -->
            <section class="bg-[var(--brand-tint)] py-24 lg:py-36">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="mx-auto max-w-2xl text-center">
                        <span
                            class="anim-hero-eyebrow inline-block rounded-full bg-[var(--brand)] px-4 py-1.5 text-xs font-semibold tracking-wide text-white uppercase"
                        >
                            {{ t('don_sang.hero.eyebrow') }}
                        </span>
                        <h1
                            class="anim-hero-title mt-6 text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl"
                        >
                            {{ t('don_sang.hero.title') }}
                        </h1>
                        <p
                            class="anim-hero-subtitle mt-6 text-lg leading-relaxed text-gray-700"
                        >
                            {{ t('don_sang.hero.subtitle') }}
                        </p>
                        <div
                            class="anim-hero-ctas mt-8 flex flex-wrap justify-center gap-4"
                        >
                            <Button as-child variant="pixel_violet" size="cta">
                                <Link :href="cobrandJeu.url(routeParams)">
                                    {{ t('don_sang.hero.cta_eligible') }}
                                </Link>
                            </Button>
                            <Button as-child variant="pixel_white" size="cta">
                                <Link :href="cobrandCollecte.url(routeParams)">
                                    {{ t('don_sang.hero.cta_collecte') }}
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Déroulement en 4 étapes -->
            <section class="py-32">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="anim-deroulement-header mb-14 text-center">
                        <h2
                            class="anim-deroulement-title text-3xl font-semibold text-gray-900"
                        >
                            {{ t('don_sang.deroulement.title') }}
                        </h2>
                        <p class="mt-4 text-gray-600">
                            {{ t('don_sang.deroulement.subtitle') }}
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <LabelStepCard
                            v-for="step in steps"
                            :key="step.n"
                            :step="step.n"
                            :title="step.title"
                            :description="step.desc"
                            class="anim-step-card"
                        />
                    </div>
                </div>
            </section>

            <!-- Impact : 3 chiffres clés -->
            <section class="anim-stats-section bg-[var(--brand)] py-28">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="grid gap-12 text-center sm:grid-cols-3">
                        <div
                            v-for="(label, i) in statLabels"
                            :key="i"
                            class="anim-stat flex flex-col items-center"
                        >
                            <span
                                class="font-pixel text-5xl leading-none text-white"
                            >
                                {{ statDisplayValues[i] }}
                            </span>
                            <span
                                class="mt-3 text-sm font-medium text-white/80"
                            >
                                {{ label }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA finale : test d'éligibilité -->
            <section class="bg-[var(--brand-tint)] py-40">
                <div class="mx-auto max-w-7xl px-6">
                    <div
                        class="anim-cta-box mx-auto max-w-2xl border-[3px] border-black bg-white px-10 py-14 text-center shadow-[8px_8px_0_0_rgba(0,0,0,0.45)]"
                    >
                        <span
                            class="font-pixel text-[0.7rem] leading-loose text-[var(--brand)]"
                        >
                            &gt; {{ t('don_sang.hero.eyebrow') }}
                        </span>
                        <h2
                            class="anim-cta-title mt-4 text-3xl font-semibold text-gray-900"
                        >
                            {{ t('don_sang.cta.title') }}
                        </h2>
                        <p class="mt-4 leading-relaxed text-gray-600">
                            {{ t('don_sang.cta.subtitle') }}
                        </p>
                        <Button
                            as-child
                            variant="pixel_violet"
                            size="cta"
                            class="mt-8"
                        >
                            <Link :href="cobrandJeu.url(routeParams)">
                                {{ t('don_sang.cta.button') }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </div>
        <MascottePopup :href="cobrandJeu.url(routeParams)" />
    </PublicLayout>
</template>
