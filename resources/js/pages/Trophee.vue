<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import PodiumCard from '@/components/cards/PodiumCard.vue';
import SpeechBubble from '@/components/cards/SpeechBubble.vue';
import MascottePopup from '@/components/MascottePopup.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

const { t, locale } = useI18n();

// Éditions du Trophée fournies par le back-end (podium top 3 par année) :
const props = defineProps({
    editions: {
        type: Array,
        default: () => [],
    },
});

const editions = computed(() => props.editions);

function scrollTo(target) {
    gsap.to(window, { scrollTo: target, duration: 1, ease: 'power2.inOut' });
}

const currentIndex = ref(0);
const currentEdition = computed(() => editions.value[currentIndex.value]);
const hasPrev = computed(() => currentIndex.value > 0);
const hasNext = computed(() => currentIndex.value < editions.value.length - 1);

function prev() {
    if (hasPrev.value) {
        currentIndex.value--;
    }
}
function next() {
    if (hasNext.value) {
        currentIndex.value++;
    }
}

// Vainqueur d'un rang donné pour l'édition courante (null si absent)
function winnerAt(rank) {
    return currentEdition.value?.winners.find((w) => w.rank === rank) ?? null;
}

const isSuspended = computed(
    () =>
        currentEdition.value &&
        currentEdition.value.year > 2010 &&
        currentEdition.value.year < 2026,
);

// computed pour rester réactif au changement de langue (tag passe par t()).
const historyMilestones = computed(() => [
    {
        year: '2008',
        tag: t('trophee.histoire.tag_2008'),
        color: '#B2D4A8',
        shadow: '#4E8A42',
    },
    {
        year: '2010',
        tag: t('trophee.histoire.tag_2010'),
        color: '#D4A0A0',
        shadow: '#A05050',
    },
    {
        year: '2026',
        tag: t('trophee.histoire.tag_2026'),
        color: '#B5CEED',
        shadow: '#4A7AAD',
    },
]);

const prizeConfigs = {
    grand_prix: { color: '#b8860b', badge: '/img/badge4.png' },
    progression: { color: '#2a5f5f', badge: '/img/badge3.png' },
    fidelite: { color: '#3d8080', badge: '/img/badge1.png' },
    coup_de_coeur: { color: '#a05050', badge: '/img/badge2.png' },
};

const prizes = ['grand_prix', 'progression', 'fidelite', 'coup_de_coeur'];

const statConfigs = [
    { target: 30, suffix: ' %' },
    { target: 120, suffix: '+' },
    { target: 500, suffix: '+' },
];
const statDisplayValues = ref(['0 %', '0+', '0+']);

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
                '.anim-trophy-title',
                '.anim-vainqueurs-title',
                '.anim-prix-title',
                '.anim-histoire-title',
                '.anim-cta-title',
            ],
            { text: '' },
        );

        // ── Hero - timeline séquentielle ──────────────────────
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

        // Mascotte - entrée depuis la droite + flottement infini
        gsap.timeline()
            .from('.anim-hero-mascot', {
                x: 80,
                opacity: 0,
                rotation: -8,
                duration: 1,
                ease: 'back.out(1.4)',
                delay: 0.2,
            })
            .to(
                '.anim-hero-mascot',
                {
                    y: -16,
                    rotation: 2,
                    duration: 2.4,
                    repeat: -1,
                    yoyo: true,
                    ease: 'sine.inOut',
                },
                '+=0.1',
            );

        gsap.to('.anim-hero-title', {
            text: t('trophee.hero.title'),
            duration: 1.4,
            ease: 'none',
        });

        // Bulle - pop in puis scramble reveal
        gsap.from('.anim-speech-bubble', {
            opacity: 0,
            scale: 0.5,
            duration: 0.5,
            delay: 1.1,
            ease: 'back.out(2.5)',
        });
        gsap.to('.anim-speech-bubble-text', {
            duration: 1.8,
            delay: 1.4,
            scrambleText: {
                text: t('trophee.hero.bubble'),
                chars: 'upperCase',
                speed: 0.4,
                revealDelay: 0.2,
            },
        });

        // ── Trophée - révélation trophée ───────────────────
        // Texte : stagger des enfants directs
        gsap.timeline({
            scrollTrigger: {
                trigger: '.anim-trophy-section',
                start: 'top 70%',
            },
            defaults: { ease: 'power3.out' },
        }).from('.anim-trophy-text > *', {
            opacity: 0,
            x: -50,
            stagger: 0.18,
            duration: 0.75,
        });

        // Trophée : rideau de révélation
        gsap.timeline({
            scrollTrigger: {
                trigger: '.anim-trophy-section',
                start: 'top 65%',
            },
        })
            .to('.anim-curtain-left', {
                x: '-101%',
                duration: 1.3,
                ease: 'power3.inOut',
                delay: 0.4,
            })
            .to(
                '.anim-curtain-right',
                { x: '101%', duration: 1.3, ease: 'power3.inOut' },
                '<',
            );

        gsap.to('.anim-trophy-title', {
            text: t('trophee.trophee_visuel.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-trophy-section',
                start: 'top 70%',
                once: true,
            },
        });

        // ── Vainqueurs - podiums tombent du haut 3→2→1 ───────
        gsap.timeline({
            scrollTrigger: { trigger: '.anim-vainqueurs', start: 'top 80%' },
            defaults: { ease: 'power3.out' },
        })
            .from('.anim-vainqueurs', { opacity: 0, y: 40, duration: 0.7 })
            .from(
                '.anim-podium-3',
                { y: 120, opacity: 0, duration: 1.1 },
                '-=0.2',
            )
            .from(
                '.anim-podium-2',
                { y: 120, opacity: 0, duration: 1.1 },
                '-=0.6',
            )
            .from(
                '.anim-podium-1',
                { y: 120, opacity: 0, duration: 1.1 },
                '-=0.6',
            );

        gsap.to('.anim-vainqueurs-title', {
            text: t('trophee.vainqueurs.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-vainqueurs',
                start: 'top 80%',
                once: true,
            },
        });

        // ── Prix d'honneur ────────────────────────────────────
        gsap.to('.anim-prix-title', {
            text: t('trophee.prix_honneur.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-prix-section',
                start: 'top 80%',
                once: true,
            },
        });

        gsap.from('.anim-prix-card', {
            opacity: 0,
            y: 50,
            stagger: 0.15,
            duration: 0.8,
            ease: 'power3.out',
            scrollTrigger: { trigger: '.anim-prix-section', start: 'top 75%' },
        });

        // ── Histoire - séquentiel ─────────────────────────────
        gsap.timeline({
            scrollTrigger: {
                trigger: '.anim-histoire-title',
                start: 'top 85%',
            },
            defaults: { ease: 'power2.out' },
        }).from('.anim-histoire-title', { opacity: 0, y: 30, duration: 0.7 });

        gsap.from('.anim-milestone', {
            opacity: 0,
            x: -120,
            stagger: 0.35,
            duration: 0.8,
            ease: 'power3.out',
            scrollTrigger: { trigger: '.anim-milestone', start: 'top 90%' },
        });

        gsap.to('.anim-histoire-title', {
            text: t('trophee.histoire.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-histoire-title',
                start: 'top 85%',
                once: true,
            },
        });

        // ── Stats - compteur animé ────────────────────────────
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

        // ── CTA ───────────────────────────────────────────────
        gsap.to('.anim-cta-title', {
            text: t('trophee.cta.title'),
            duration: 1.2,
            ease: 'none',
            scrollTrigger: {
                trigger: '.anim-cta',
                start: 'top 85%',
                once: true,
            },
        });

        gsap.from('.anim-cta', {
            opacity: 0,
            y: 30,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-cta', start: 'top 85%' },
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
        <Head :title="t('trophee.title')" />

        <div ref="root">
            <!-- Hero -->
            <section style="background-color: #ede9f8" class="py-24 lg:py-36">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div>
                            <h1
                                class="anim-hero-title font-pixel text-[1.35rem] leading-loose text-gray-900"
                            >
                                {{ t('trophee.hero.title') }}
                            </h1>
                            <p
                                class="anim-hero-subtitle mt-6 max-w-md leading-relaxed text-gray-700"
                            >
                                {{ t('trophee.hero.subtitle') }}
                            </p>
                            <div
                                class="anim-hero-ctas mt-8 flex flex-wrap gap-4"
                            >
                                <Button
                                    as-child
                                    variant="pixel_violet"
                                    size="cta"
                                >
                                    <Link :href="routes.collecte.url()">
                                        {{ t('trophee.hero.cta_primary') }}
                                    </Link>
                                </Button>
                                <Button
                                    as-child
                                    variant="pixel_white"
                                    size="cta"
                                >
                                    <a
                                        href="#vainqueurs"
                                        @click.prevent="scrollTo('#vainqueurs')"
                                    >
                                        {{ t('trophee.hero.cta_secondary') }}
                                    </a>
                                </Button>
                            </div>
                        </div>
                        <div class="flex flex-col items-center lg:items-end">
                            <SpeechBubble
                                :text="t('trophee.hero.bubble')"
                                class="anim-speech-bubble mb-8 max-w-xs self-start"
                            />
                            <img
                                src="/img/mascotte.png"
                                :alt="t('trophee.hero.mascot_alt')"
                                class="anim-hero-mascot w-64 lg:w-80"
                                style="image-rendering: pixelated"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Trophée visuel -->
            <section
                class="anim-trophy-section py-32"
                style="background-color: #3d8080"
            >
                <div class="mx-auto max-w-7xl px-6">
                    <div class="grid items-center gap-12 lg:grid-cols-2">
                        <div class="anim-trophy-text">
                            <span
                                class="inline-block bg-yellow-300 px-3 py-1 font-pixel text-[0.6rem] tracking-widest text-black uppercase"
                            >
                                {{ t('trophee.trophee_visuel.tag') }}
                            </span>
                            <h2
                                class="anim-trophy-title mt-6 font-pixel text-[1.5rem] leading-loose text-white"
                            >
                                {{ t('trophee.trophee_visuel.title') }}
                            </h2>
                            <div class="mt-4 h-1 w-12 bg-teal-200"></div>
                            <p
                                class="mt-6 max-w-md text-base leading-relaxed text-teal-100"
                            >
                                {{ t('trophee.trophee_visuel.desc') }}
                            </p>
                        </div>
                        <div
                            class="flex items-center justify-center lg:justify-end"
                        >
                            <div
                                class="relative w-full max-w-[380px] overflow-hidden rounded-3xl"
                                style="aspect-ratio: 380/520"
                            >
                                <!-- Halo -->
                                <div
                                    class="anim-trophy-halo absolute inset-0 opacity-40 blur-3xl"
                                    style="
                                        background: radial-gradient(
                                            circle,
                                            #7c3aed,
                                            transparent
                                        );
                                    "
                                ></div>
                                <!-- Trophée -->
                                <img
                                    src="/img/trophy.png"
                                    :alt="t('trophee.trophee_visuel.alt')"
                                    class="anim-trophy-img absolute inset-0 z-10 h-full w-full object-cover drop-shadow-2xl"
                                />
                                <!-- Rideaux -->
                                <div
                                    class="anim-curtain-left pointer-events-none absolute inset-y-0 left-0 z-20 w-1/2 border-r-2 border-teal-900"
                                    style="background-color: #3d8080"
                                ></div>
                                <div
                                    class="anim-curtain-right pointer-events-none absolute inset-y-0 right-0 z-20 w-1/2 border-l-2 border-teal-900"
                                    style="background-color: #3d8080"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Vainqueurs -->
            <section
                id="vainqueurs"
                style="background-color: #ede9f8"
                class="py-32"
            >
                <div
                    class="anim-vainqueurs mx-auto max-w-7xl bg-white px-4 py-8 shadow-[8px_8px_0px_0px_#3d8080] sm:px-12 sm:py-12 lg:px-20"
                >
                    <div class="mb-12 text-center">
                        <h2
                            class="anim-vainqueurs-title font-pixel text-[1.1rem] leading-loose text-gray-900"
                        >
                            {{ t('trophee.vainqueurs.title') }}
                        </h2>
                        <p class="mt-3 text-gray-700">
                            {{ t('trophee.vainqueurs.subtitle') }}
                        </p>
                    </div>

                    <div v-if="currentEdition" class="flex items-center gap-6">
                        <button
                            :disabled="!hasNext"
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-none border-4 border-teal-600 bg-white text-black shadow-[4px_4px_0px_0px_#3d8080] transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#3d8080] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:cursor-not-allowed disabled:opacity-30"
                            :aria-label="t('trophee.vainqueurs.prev')"
                            @click="next"
                        >
                            <ChevronLeft :size="20" class="text-black" />
                        </button>

                        <div class="flex flex-1 flex-col items-center gap-4">
                            <div
                                class="inline-block border-4 border-teal-800 bg-teal-600 px-6 py-2 font-pixel text-lg text-white shadow-[4px_4px_0px_0px_#2a5f5f]"
                            >
                                {{ t('trophee.vainqueurs.edition') }}
                                {{ currentEdition.year }}
                            </div>
                            <p
                                v-if="isSuspended"
                                class="text-sm text-gray-400 italic"
                            >
                                {{ t('trophee.vainqueurs.suspended') }}
                            </p>
                            <div
                                class="flex w-full items-end justify-center gap-3"
                            >
                                <PodiumCard
                                    v-if="winnerAt(2)"
                                    :rank="2"
                                    :logo-src="winnerAt(2).logo"
                                    :logo-alt="winnerAt(2).name"
                                    :category="winnerAt(2).name"
                                    description=""
                                    class="anim-podium-2 hidden w-36 md:flex md:w-40"
                                />
                                <PodiumCard
                                    v-if="winnerAt(1)"
                                    :rank="1"
                                    :logo-src="winnerAt(1).logo"
                                    :logo-alt="winnerAt(1).name"
                                    :category="winnerAt(1).name"
                                    description=""
                                    class="anim-podium-1 w-32 md:w-40 lg:w-44"
                                />
                                <PodiumCard
                                    v-if="winnerAt(3)"
                                    :rank="3"
                                    :logo-src="winnerAt(3).logo"
                                    :logo-alt="winnerAt(3).name"
                                    :category="winnerAt(3).name"
                                    description=""
                                    class="anim-podium-3 hidden w-36 md:flex md:w-40"
                                />
                            </div>
                        </div>

                        <button
                            :disabled="!hasPrev"
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-none border-4 border-teal-600 bg-white text-black shadow-[4px_4px_0px_0px_#3d8080] transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#3d8080] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:cursor-not-allowed disabled:opacity-30"
                            :aria-label="t('trophee.vainqueurs.next')"
                            @click="prev"
                        >
                            <ChevronRight :size="20" class="text-black" />
                        </button>
                    </div>

                    <p v-else class="py-12 text-center text-gray-500 italic">
                        {{ t('trophee.vainqueurs.empty') }}
                    </p>

                    <div
                        v-if="editions.length > 1"
                        class="mt-8 flex justify-center gap-2"
                    >
                        <button
                            v-for="(ed, i) in [...editions].reverse()"
                            :key="ed.year"
                            class="h-2.5 transition-all"
                            :class="
                                editions.length - 1 - i === currentIndex
                                    ? 'w-6 bg-teal-600'
                                    : 'w-2.5 bg-teal-200'
                            "
                            @click="currentIndex = editions.length - 1 - i"
                        />
                    </div>
                </div>
            </section>

            <!-- Prix d'honneur -->
            <section
                class="anim-prix-section pb-32"
                style="background-color: #ede9f8"
            >
                <div class="mx-auto max-w-7xl px-6">
                    <div class="mb-10 text-center">
                        <h2
                            class="anim-prix-title font-pixel text-[1.1rem] leading-loose text-gray-900"
                        >
                            {{ t('trophee.prix_honneur.title') }}
                        </h2>
                        <p class="mt-3 text-gray-600">
                            {{ t('trophee.prix_honneur.subtitle') }}
                        </p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div
                            v-for="id in prizes"
                            :key="id"
                            class="anim-prix-card flex items-center gap-4 bg-white p-5 shadow-[6px_6px_0px_0px_#3d8080] sm:gap-6 sm:p-8"
                        >
                            <img
                                :src="prizeConfigs[id].badge"
                                :alt="t(`trophee.prix_honneur.${id}.name`)"
                                class="h-24 w-24 shrink-0 object-contain sm:h-44 sm:w-44"
                            />
                            <div>
                                <div
                                    class="mb-3 h-1 w-10"
                                    :style="{
                                        backgroundColor: prizeConfigs[id].color,
                                    }"
                                ></div>
                                <p
                                    class="text-sm leading-relaxed text-gray-600"
                                >
                                    {{
                                        t(
                                            `trophee.prix_honneur.${id}.description`,
                                        )
                                    }}
                                </p>
                                <p
                                    class="mt-3 text-xs font-medium italic"
                                    :style="{ color: prizeConfigs[id].color }"
                                >
                                    {{
                                        t(`trophee.prix_honneur.${id}.tagline`)
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Histoire -->
            <section
                class="anim-histoire-section py-32"
                style="background-color: #ede9f8"
            >
                <div class="mx-auto max-w-7xl px-6">
                    <h2
                        class="anim-histoire-title mb-16 text-center font-pixel text-[1.1rem] leading-loose text-gray-900"
                    >
                        {{ t('trophee.histoire.title') }}
                    </h2>

                    <div class="relative">
                        <!-- Ligne horizontale -->
                        <div
                            class="absolute top-6 right-0 left-0 hidden h-1 bg-gray-900 md:block"
                        ></div>

                        <div
                            class="grid grid-cols-1 gap-12 md:grid-cols-3 md:gap-8"
                        >
                            <div
                                v-for="(m, i) in historyMilestones"
                                :key="m.year"
                                class="anim-milestone flex flex-col items-center"
                            >
                                <!-- Cercle -->
                                <div
                                    class="z-10 mb-4 h-12 w-12 rounded-full border-4 border-gray-900"
                                    :style="{
                                        backgroundColor: m.color,
                                        boxShadow: `3px 3px 0 ${m.shadow}`,
                                    }"
                                ></div>

                                <!-- Tag -->
                                <span
                                    class="mb-2 px-3 py-1 font-pixel text-xs text-white"
                                    :style="{ backgroundColor: m.shadow }"
                                >
                                    {{ m.tag }}
                                </span>

                                <!-- Année -->
                                <span
                                    class="mb-6 font-pixel text-3xl text-gray-900"
                                    >{{ m.year }}</span
                                >

                                <!-- Carte description -->
                                <div
                                    class="w-full bg-white p-5"
                                    :style="{
                                        boxShadow: `4px 4px 0 ${m.shadow}`,
                                    }"
                                >
                                    <p
                                        class="text-sm leading-relaxed text-gray-700"
                                    >
                                        {{ t(`trophee.histoire.p${i + 1}`) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats -->
            <section
                class="anim-stats-section py-28"
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
                <div class="mx-auto max-w-7xl px-6">
                    <div class="grid gap-12 text-center lg:grid-cols-3">
                        <div v-for="i in 3" :key="i" class="anim-stat">
                            <p class="font-pixel text-5xl text-yellow-300">
                                {{ statDisplayValues[i - 1] }}
                            </p>
                            <p class="mt-3 text-sm font-medium text-teal-100">
                                {{ t(`trophee.stats.s${i}_label`) }}
                            </p>
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
                        {{ t('trophee.cta.title') }}
                    </h2>
                    <p class="mt-6 leading-relaxed text-teal-100">
                        {{ t('trophee.cta.subtitle') }}
                    </p>
                    <div class="mt-10">
                        <Button as-child variant="pixel_yellow" size="cta">
                            <Link :href="routes.collecte.url()">
                                {{ t('trophee.cta.button') }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </div>
        <MascottePopup :href="routes.eligibilite.url()" />
    </PublicLayout>
</template>
