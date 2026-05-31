<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import PodiumCard from '@/components/cards/PodiumCard.vue';
import SpeechBubble from '@/components/cards/SpeechBubble.vue';
import { Button } from '@/components/ui/button';
import { editions } from '@/data/trophee-editions.js';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();

const currentIndex = ref(0);
const currentEdition = computed(() => editions[currentIndex.value]);
const hasPrev = computed(() => currentIndex.value > 0);
const hasNext = computed(() => currentIndex.value < editions.length - 1);

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

const isSuspended = computed(
    () => currentEdition.value.year > 2010 && currentEdition.value.year < 2026,
);

const historyMilestones = [
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
];

const displayedBubbleText = ref('');

const statConfigs = [
    { target: 30, suffix: ' %' },
    { target: 120, suffix: '+' },
    { target: 500, suffix: '+' },
];
const statDisplayValues = ref(['0 %', '0+', '0+']);

const root = ref(null);
let ctx;

onMounted(() => {
    if (!root.value) {
        return;
    }

    const bubbleFullText = t('trophee.hero.bubble');
    const charObj = { n: 0 };

    ctx = gsap.context(() => {
        // ── Hero — timeline séquentielle ──────────────────────
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

        // Mascotte — entrée depuis la droite + flottement infini
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

        // Bulle — pop in puis typewriter
        gsap.from('.anim-speech-bubble', {
            opacity: 0,
            scale: 0.5,
            duration: 0.5,
            delay: 1.1,
            ease: 'back.out(2.5)',
        });
        gsap.to(charObj, {
            n: bubbleFullText.length,
            duration: bubbleFullText.length * 0.045,
            ease: 'none',
            delay: 1.4,
            onUpdate() {
                displayedBubbleText.value = bubbleFullText.slice(
                    0,
                    Math.round(charObj.n),
                );
            },
        });

        // ── Trophée — révélation dramatique ───────────────────
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

        // ── Vainqueurs — podiums tombent du haut 3→2→1 ───────
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

        // ── Histoire — séquentiel ─────────────────────────────
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

        // ── Stats — compteur animé ────────────────────────────
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
        gsap.from('.anim-cta', {
            opacity: 0,
            y: 30,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: { trigger: '.anim-cta', start: 'top 85%' },
        });
    }, root.value);
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
                                <Button variant="pixel_violet">
                                    <Link :href="routes.collecte.url()">
                                        {{ t('trophee.hero.cta_primary') }}
                                    </Link>
                                </Button>
                                <Button variant="pixel_white">
                                    <a href="#vainqueurs">
                                        {{ t('trophee.hero.cta_secondary') }}
                                    </a>
                                </Button>
                            </div>
                        </div>
                        <div class="flex flex-col items-center lg:items-end">
                            <SpeechBubble
                                :text="displayedBubbleText"
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
                                Remis chaque année
                            </span>
                            <h2
                                class="mt-6 font-pixel text-[1.5rem] leading-loose text-white"
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
                                class="relative overflow-hidden rounded-3xl"
                                style="width: 380px; height: 520px"
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
                    class="anim-vainqueurs mx-auto max-w-7xl bg-white px-20 py-12 shadow-[8px_8px_0px_0px_#3d8080]"
                >
                    <div class="mb-12 text-center">
                        <h2 class="text-2xl font-semibold text-gray-900">
                            {{ t('trophee.vainqueurs.title') }}
                        </h2>
                        <p class="mt-3 text-gray-700">
                            {{ t('trophee.vainqueurs.subtitle') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-6">
                        <button
                            :disabled="!hasPrev"
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-none border-4 border-teal-600 bg-white text-black shadow-[4px_4px_0px_0px_#3d8080] transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#3d8080] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:cursor-not-allowed disabled:opacity-30"
                            :aria-label="t('trophee.vainqueurs.prev')"
                            @click="prev"
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
                                    :rank="2"
                                    :logo-src="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 2,
                                        ).logo
                                    "
                                    :logo-alt="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 2,
                                        ).name
                                    "
                                    :category="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 2,
                                        ).name
                                    "
                                    description=""
                                    class="anim-podium-2 hidden w-40 lg:flex"
                                />
                                <PodiumCard
                                    :rank="1"
                                    :logo-src="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 1,
                                        ).logo
                                    "
                                    :logo-alt="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 1,
                                        ).name
                                    "
                                    :category="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 1,
                                        ).name
                                    "
                                    description=""
                                    class="anim-podium-1 w-44"
                                />
                                <PodiumCard
                                    :rank="3"
                                    :logo-src="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 3,
                                        ).logo
                                    "
                                    :logo-alt="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 3,
                                        ).name
                                    "
                                    :category="
                                        currentEdition.winners.find(
                                            (w) => w.rank === 3,
                                        ).name
                                    "
                                    description=""
                                    class="anim-podium-3 hidden w-40 lg:flex"
                                />
                            </div>
                        </div>

                        <button
                            :disabled="!hasNext"
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-none border-4 border-teal-600 bg-white text-black shadow-[4px_4px_0px_0px_#3d8080] transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#3d8080] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:cursor-not-allowed disabled:opacity-30"
                            :aria-label="t('trophee.vainqueurs.next')"
                            @click="next"
                        >
                            <ChevronRight :size="20" class="text-black" />
                        </button>
                    </div>

                    <div class="mt-8 flex justify-center gap-2">
                        <button
                            v-for="(ed, i) in editions"
                            :key="ed.year"
                            class="h-2.5 transition-all"
                            :class="
                                i === currentIndex
                                    ? 'w-6 bg-teal-600'
                                    : 'w-2.5 bg-teal-200'
                            "
                            @click="currentIndex = i"
                        />
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
                            class="absolute top-6 right-0 left-0 h-1 bg-gray-900"
                        ></div>

                        <div class="grid grid-cols-3 gap-8">
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
                        class="font-pixel text-[1.2rem] leading-loose text-white"
                    >
                        {{ t('trophee.cta.title') }}
                    </h2>
                    <p class="mt-6 leading-relaxed text-teal-100">
                        {{ t('trophee.cta.subtitle') }}
                    </p>
                    <div class="mt-10">
                        <Button as-child variant="pixel_yellow">
                            <Link :href="routes.collecte.url()">
                                {{ t('trophee.cta.button') }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
