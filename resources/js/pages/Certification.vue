<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Award, Building2, Users } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import LabelStepCard from '@/components/cards/LabelStepCard.vue';
import PixelFeatureCard from '@/components/cards/PixelFeatureCard.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

const circleColors = ['#F5E07A', '#B2D4A8', '#B5CEED', '#D4A0A0'];
const circleShadows = ['#C4A800', '#4E8A42', '#4A7AAD', '#A05050'];
const sides = ['right', 'left', 'right', 'left'];

const { t } = useI18n();

// Avantages du label
const avantages = [
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
];
</script>

<template>
    <PublicLayout>
        <Head :title="t('certification.title')" />

        <!-- Hero -->
        <section style="background-color: #ede9f8" class="py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <h1
                            class="mt-6 font-pixel text-[1.35rem] leading-loose text-gray-900"
                        >
                            {{ t('certification.hero.title') }}
                        </h1>
                        <p class="mt-6 max-w-md leading-relaxed text-gray-700">
                            {{ t('certification.hero.subtitle') }}
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <Button variant="pixel_violet">
                                <Link :href="routes.collecte.url()">
                                    {{ t('certification.hero.cta_primary') }}
                                </Link>
                            </Button>
                            <Button variant="pixel_blue">
                                <a href="#avantages">
                                    {{ t('certification.hero.cta_secondary') }}
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
                            class="w-64 object-contain lg:w-80"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Avantages -->
        <section class="py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto mb-14 max-w-2xl text-center">
                    <h2 class="text-3xl font-semibold text-gray-900">
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
                    >
                        <template #icon>
                            <component :is="item.icon" :size="20" />
                        </template>
                    </PixelFeatureCard>
                </div>
            </div>
        </section>

        <!-- Parcours - header -->
        <section style="background-color: #ede9f8" class="py-16">
            <div class="mx-auto max-w-7xl px-6">
                <h2
                    class="font-pixel text-[1.1rem] leading-loose text-gray-900"
                >
                    {{ t('certification.parcours.title') }}
                </h2>
                <p class="mt-2 max-w-xl leading-relaxed text-gray-600">
                    {{ t('certification.parcours.subtitle') }}
                </p>
            </div>
        </section>

        <!-- Parcours - timeline -->
        <section class="py-24">
            <div class="mx-auto max-w-5xl px-6">
                <div class="relative">
                    <!-- Ligne violette -->
                    <div
                        class="absolute top-0 bottom-0 left-1/2 w-1 -translate-x-1/2 bg-violet-700"
                    ></div>

                    <div
                        v-for="(side, i) in sides"
                        :key="i"
                        class="relative mb-24 grid grid-cols-[1fr_60px_1fr] items-center last:mb-0"
                    >
                        <!-- Colonne gauche -->
                        <div class="flex justify-end pr-10">
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
                        <div class="flex justify-center">
                            <div
                                class="z-10 h-12 w-12 rounded-full border-4"
                                :style="{
                                    backgroundColor: circleColors[i],
                                    borderColor: circleShadows[i],
                                }"
                            ></div>
                        </div>

                        <!-- Colonne droite -->
                        <div class="pl-10">
                            <LabelStepCard
                                v-if="side === 'right'"
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
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA final -->
        <section class="bg-gray-900 py-32">
            <div class="mx-auto max-w-3xl px-6 text-center">
                <h2 class="font-pixel text-[1.2rem] leading-loose text-white">
                    {{ t('certification.cta.title') }}
                </h2>
                <p class="mt-6 leading-relaxed text-gray-400">
                    {{ t('certification.cta.subtitle') }}
                </p>
                <div class="mt-10">
                    <Button variant="pixel_violet">
                        <Link :href="routes.collecte.url()">
                            {{ t('certification.cta.button') }}
                        </Link>
                    </Button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
