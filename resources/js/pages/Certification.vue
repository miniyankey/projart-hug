<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Award, Building2, Users } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import LabelStepCard from '@/components/cards/LabelStepCard.vue';
import PixelFeatureCard from '@/components/cards/PixelFeatureCard.vue';
import MascottePopup from '@/components/MascottePopup.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

const { t, locale } = useI18n();

function scrollTo(target) {
    gsap.to(window, { scrollTo: target, duration: 1, ease: 'power2.inOut' });
}

const circleColors = ['#F5E07A', '#B2D4A8', '#B5CEED', '#D4A0A0'];
const circleShadows = ['#C4A800', '#4E8A42', '#4A7AAD', '#A05050'];
const sides = ['right', 'left', 'right', 'left'];

// computed pour rester réactif au changement de langue (titre/desc via t()).
const avantages = computed(() => [
    {
        icon: Building2,
        title: t('certification.avantages.item1_title'),
        description: t('certification.avantages.item1_desc'),
    },
    {
        icon: Award,
        title: t('certification.avantages.item2_title'),
        description: t('certification.avantages.item2_desc'),
    },
    {
        icon: Users,
        title: t('certification.avantages.item3_title'),
        description: t('certification.avantages.item3_desc'),
    },
]);

const root = ref(null);
let ctx;

function buildAnimations() {
    if (!root.value) {
        return;
    }

    ctx = gsap.context(() => {
        // Vider les titres avant animation pour que TextPlugin parte de zéro
        gsap.set(
            [
                '.anim-hero-title',
                '.anim-avantages-title',
                '.anim-parcours-title',
                '.anim-cta-title',
            ],
            { text: '' },
        );

        // Cacher les éléments animés via onEnter avant que le scroll trigger ne se déclenche
        gsap.set(
            ['.anim-step-left', '.anim-step-right', '.anim-timeline-circle'],
            {
                opacity: 0,
            },
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
                '.anim-hero-ctas',
                { opacity: 0, y: 20, duration: 0.6 },
                '-=0.35',
            );

        gsap.from('.anim-hero-label', {
            x: 80,
            opacity: 0,
            scale: 0.9,
            duration: 1,
            ease: 'back.out(1.4)',
            delay: 0.3,
        });

        gsap.to('.anim-hero-title', {
            text: t('certification.hero.title'),
            duration: 1.4,
            ease: 'none',
        });

        // ── Avantages ─────────────────────────────────────────
        gsap.from('.anim-avantages-header', {
            opacity: 0,
            y: 30,
            duration: 0.7,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: '.anim-avantages-header',
                start: 'top 80%',
            },
        });

        gsap.to('.anim-avantages-title', {
            text: t('certification.avantages.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-avantages-header',
                start: 'top 80%',
                once: true,
            },
        });

        gsap.from('.anim-feature-card', {
            opacity: 0,
            y: 50,
            stagger: 0.18,
            duration: 0.75,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: '.anim-feature-card',
                start: 'top 85%',
            },
        });

        // ── Parcours header ───────────────────────────────────
        gsap.from('.anim-parcours-header > *', {
            opacity: 0,
            y: 25,
            stagger: 0.15,
            duration: 0.7,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: '.anim-parcours-header',
                start: 'top 80%',
            },
        });

        gsap.to('.anim-parcours-title', {
            text: t('certification.parcours.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-parcours-header',
                start: 'top 80%',
                once: true,
            },
        });

        // ── Timeline - ligne forward-only ─────────────────────
        const lineEl = root.value.querySelector('.anim-timeline-line');
        gsap.set(lineEl, { scaleY: 0, transformOrigin: 'top center' });

        let lineProgress = 0;
        ScrollTrigger.create({
            trigger: '.anim-timeline-section',
            start: 'top 70%',
            end: 'bottom 30%',
            onUpdate(self) {
                const p = Math.max(lineProgress, self.progress);

                if (p !== lineProgress) {
                    lineProgress = p;
                    gsap.set(lineEl, { scaleY: p });
                }
            },
        });

        // Cercles : apparaissent quand le trait les atteint
        gsap.utils
            .toArray('.anim-timeline-circle', root.value)
            .forEach((el) => {
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top center',
                    once: true,
                    onEnter() {
                        gsap.fromTo(
                            el,
                            { scale: 0, opacity: 0 },
                            {
                                scale: 1,
                                opacity: 1,
                                duration: 0.6,
                                ease: 'back.out(2)',
                            },
                        );
                    },
                });
            });

        // Cartes gauche
        gsap.utils.toArray('.anim-step-left', root.value).forEach((el) => {
            ScrollTrigger.create({
                trigger: el,
                start: 'top 65%',
                once: true,
                onEnter() {
                    gsap.fromTo(
                        el,
                        { x: -80, opacity: 0 },
                        { x: 0, opacity: 1, duration: 0.8, ease: 'power3.out' },
                    );
                },
            });
        });

        // Cartes droite
        gsap.utils.toArray('.anim-step-right', root.value).forEach((el) => {
            ScrollTrigger.create({
                trigger: el,
                start: 'top 65%',
                once: true,
                onEnter() {
                    gsap.fromTo(
                        el,
                        { x: 80, opacity: 0 },
                        { x: 0, opacity: 1, duration: 0.8, ease: 'power3.out' },
                    );
                },
            });
        });

        // ── CTA ───────────────────────────────────────────────
        gsap.from('.anim-cta', {
            opacity: 0,
            y: 30,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-cta', start: 'top 85%' },
        });

        gsap.to('.anim-cta-title', {
            text: t('certification.cta.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-cta',
                start: 'top 85%',
                once: true,
            },
        });
    }, root.value);
}

onMounted(buildAnimations);

// Les titres animés sont écrits par GSAP (TextPlugin) avec t() évalué une
// seule fois : ils ne sont pas réactifs. On reconstruit les animations au
// changement de langue pour qu'ils se traduisent sans hard refresh.
watch(locale, () => {
    ctx?.revert();
    buildAnimations();
});

onUnmounted(() => {
    ctx?.revert();
});
</script>

<template>
    <PublicLayout>
        <Head :title="t('certification.title')" />

        <div ref="root">
            <!-- Hero -->
            <section style="background-color: #ede9f8" class="py-24 lg:py-36">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div>
                            <h1
                                class="anim-hero-title mt-6 font-pixel text-[1.35rem] leading-loose text-pretty text-gray-900"
                            >
                                {{ t('certification.hero.title') }}
                            </h1>
                            <p
                                class="anim-hero-subtitle mt-6 max-w-md leading-relaxed text-gray-700"
                            >
                                {{ t('certification.hero.subtitle') }}
                            </p>
                            <div
                                class="anim-hero-ctas mt-8 flex flex-wrap gap-4"
                            >
                                <Button as-child variant="pixel_violet" size="cta">
                                    <Link :href="routes.collecte.url()">
                                        {{
                                            t('certification.hero.cta_primary')
                                        }}
                                    </Link>
                                </Button>
                                <Button as-child variant="pixel_blue" size="cta">
                                    <a
                                        href="#avantages"
                                        @click.prevent="scrollTo('#avantages')"
                                    >
                                        {{
                                            t(
                                                'certification.hero.cta_secondary',
                                            )
                                        }}
                                    </a>
                                </Button>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-center lg:justify-end"
                        >
                            <img
                                src="/img/label-cts.png"
                                :alt="t('certification.hero.label_alt')"
                                class="anim-hero-label w-64 object-contain lg:w-80"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Avantages -->
            <section id="avantages" class="py-32">
                <div class="mx-auto max-w-7xl px-6">
                    <div
                        class="anim-avantages-header mx-auto mb-14 max-w-2xl text-center"
                    >
                        <h2
                            class="anim-avantages-title text-3xl font-semibold text-gray-900"
                        >
                            {{ t('certification.avantages.title') }}
                        </h2>
                        <p class="mt-4 leading-relaxed text-gray-600">
                            {{ t('certification.avantages.subtitle') }}
                        </p>
                    </div>
                    <div class="grid gap-6 lg:grid-cols-3">
                        <PixelFeatureCard
                            v-for="(item, i) in avantages"
                            :key="i"
                            :title="item.title"
                            :description="item.description"
                            class="anim-feature-card"
                        >
                            <template #icon>
                                <component :is="item.icon" :size="20" />
                            </template>
                        </PixelFeatureCard>
                    </div>
                </div>
            </section>

            <!-- Parcours - header -->
            <section style="background-color: #ede9f8" class="py-24">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="anim-parcours-header">
                        <h2
                            class="anim-parcours-title font-pixel text-[1.1rem] leading-loose text-gray-900"
                        >
                            {{ t('certification.parcours.title') }}
                        </h2>
                        <p class="mt-2 max-w-xl leading-relaxed text-gray-600">
                            {{ t('certification.parcours.subtitle') }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Parcours - timeline -->
            <section class="anim-timeline-section py-32">
                <div class="mx-auto max-w-5xl px-6">
                    <div class="relative">
                        <!-- Ligne : gauche sur mobile/tablet, centrée sur desktop -->
                        <div
                            class="anim-timeline-line absolute top-0 bottom-0 left-6 w-1 bg-violet-700 md:left-8 lg:left-1/2 lg:-translate-x-1/2"
                        ></div>

                        <div
                            v-for="(side, i) in sides"
                            :key="i"
                            class="relative mb-16 grid grid-cols-[48px_1fr] items-center gap-x-4 last:mb-0 md:grid-cols-[64px_1fr] md:gap-x-6 lg:mb-24 lg:grid-cols-[1fr_60px_1fr] lg:gap-x-0"
                        >
                            <!-- Colonne gauche (desktop uniquement) -->
                            <div
                                class="anim-step-left hidden justify-end pr-10 lg:flex"
                            >
                                <LabelStepCard
                                    v-if="side === 'left'"
                                    :step="i + 1"
                                    :title="
                                        t(
                                            `certification.parcours.step${i + 1}_title`,
                                        )
                                    "
                                    :description="
                                        t(
                                            `certification.parcours.step${i + 1}_desc`,
                                        )
                                    "
                                    class="w-full max-w-xs"
                                />
                            </div>

                            <!-- Cercle marqueur -->
                            <div class="flex justify-start lg:justify-center">
                                <div
                                    class="anim-timeline-circle z-10 h-12 w-12 rounded-full border-4"
                                    :style="{
                                        backgroundColor: circleColors[i],
                                        borderColor: circleShadows[i],
                                    }"
                                ></div>
                            </div>

                            <!-- Colonne droite : steps droite sur desktop, tous les steps sur mobile -->
                            <div class="anim-step-right pl-4 md:pl-6 lg:pl-10">
                                <LabelStepCard
                                    :step="i + 1"
                                    :title="
                                        t(
                                            `certification.parcours.step${i + 1}_title`,
                                        )
                                    "
                                    :description="
                                        t(
                                            `certification.parcours.step${i + 1}_desc`,
                                        )
                                    "
                                    class="w-full max-w-xs md:max-w-sm"
                                    :class="
                                        side === 'left' ? 'block lg:hidden' : ''
                                    "
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA final -->
            <section class="py-40" style="background-color: #3d8080">
                <div class="anim-cta mx-auto max-w-3xl px-6 text-center">
                    <h2
                        class="anim-cta-title font-pixel text-[1.2rem] leading-loose text-white"
                    >
                        {{ t('certification.cta.title') }}
                    </h2>
                    <p class="mt-6 leading-relaxed text-teal-100">
                        {{ t('certification.cta.subtitle') }}
                    </p>
                    <div class="mt-10">
                        <Button as-child variant="pixel_violet" size="cta">
                            <Link :href="routes.collecte.url()">
                                {{ t('certification.cta.button') }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </div>
        <MascottePopup :href="routes.eligibilite.url()" />
    </PublicLayout>
</template>
