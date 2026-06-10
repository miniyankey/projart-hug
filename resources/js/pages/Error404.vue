<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SpeechBubble from '@/components/cards/SpeechBubble.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();
</script>

<template>
    <PublicLayout>
        <Head :title="t('error404.title')" />

        <section
            class="flex min-h-[80vh] items-center justify-center pt-24 pb-16 md:pt-32"
            style="
                background-color: #6b21a8;
                background-image: radial-gradient(
                    circle,
                    rgba(255, 255, 255, 0.07) 1px,
                    transparent 1px
                );
                background-size: 28px 28px;
            "
        >
            <div class="mx-auto max-w-2xl px-6 text-center">
                <!-- Glitchy 404 number -->
                <div class="relative inline-block">
                    <h1
                        class="font-pixel text-[6rem] leading-none text-white select-none md:text-[8rem]"
                        style="text-shadow: 8px 8px 0 rgba(0, 0, 0, 0.3)"
                    >
                        404
                    </h1>
                    <div
                        class="absolute inset-0 font-pixel text-[6rem] leading-none text-purple-300 opacity-30 select-none md:text-[8rem]"
                        style="text-shadow: none; clip-path: inset(60% 0 0 20%)"
                    >
                        404
                    </div>
                </div>

                <!-- Speech Bubble -->
                <div class="mx-auto mt-8 flex max-w-md flex-col items-center">
                    <SpeechBubble
                        :text="t('error404.bubble')"
                        class="self-center"
                    />
                </div>

                <!-- Message Card -->
                <div
                    class="mx-auto mt-12 max-w-lg border-4 border-black bg-white p-8 shadow-[8px_8px_0_0_rgba(0,0,0,0.45)]"
                >
                    <h2
                        class="font-pixel text-sm leading-loose text-gray-900 uppercase"
                    >
                        {{ t('error404.heading') }}
                    </h2>
                    <p class="mt-4 leading-relaxed text-gray-600">
                        {{ t('error404.description') }}
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <Button as-child variant="pixel_violet" size="lg">
                            <Link :href="routes.home.url()">
                                {{ t('error404.cta_home') }}
                            </Link>
                        </Button>
                        <Button as-child variant="pixel_blue" size="lg">
                            <Link :href="routes.collecte.url()">
                                {{ t('error404.cta_collecte') }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
