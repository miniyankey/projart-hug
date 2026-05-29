<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Award, Building2, Users } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import PixelFeatureCard from '@/components/PixelFeatureCard.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();
</script>

<template>
    <PublicLayout>
        <Head :title="t('certification.title')" />

        <!-- Hero -->
        <section style="background-color: #EDE9F8" class="py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <span class="inline-block border-2 border-violet-400 px-3 py-1 text-xs font-medium tracking-widest text-violet-700 uppercase">
                            {{ t('certification.hero.eyebrow') }}
                        </span>
                        <h1 class="font-pixel mt-6 text-[1.35rem] leading-loose text-gray-900">
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
                            <Button variant="pixel_white">
                                <a href="#avantages">
                                    {{ t('certification.hero.cta_secondary') }}
                                </a>
                            </Button>
                        </div>
                    </div>

                    <div class="flex items-center justify-center lg:justify-end">
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
                        :title="t('certification.avantages.item1_title')"
                        :description="t('certification.avantages.item1_desc')"
                        :active="true"
                    >
                        <template #icon><Building2 :size="20" /></template>
                    </PixelFeatureCard>
                    <PixelFeatureCard
                        :title="t('certification.avantages.item2_title')"
                        :description="t('certification.avantages.item2_desc')"
                    >
                        <template #icon><Award :size="20" /></template>
                    </PixelFeatureCard>
                    <PixelFeatureCard
                        :title="t('certification.avantages.item3_title')"
                        :description="t('certification.avantages.item3_desc')"
                    >
                        <template #icon><Users :size="20" /></template>
                    </PixelFeatureCard>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
