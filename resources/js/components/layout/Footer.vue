<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import facebookIcon from '@/../images/icons/facebook.svg';
import instagramIcon from '@/../images/icons/instagram.svg';
import linkedinIcon from '@/../images/icons/linkedin.svg';
import tiktokIcon from '@/../images/icons/tiktok.svg';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();

const socials = [
    {
        name: 'Instagram',
        href: 'https://www.instagram.com/hug_ge/',
        icon: instagramIcon,
    },
    {
        name: 'Facebook',
        href: 'https://www.facebook.com/hopitaux.universitaires.geneve',
        icon: facebookIcon,
    },
    {
        name: 'LinkedIn',
        href: 'https://www.linkedin.com/company/hopitaux-universitaires-de-geneve',
        icon: linkedinIcon,
    },
    {
        name: 'TikTok',
        href: 'https://www.tiktok.com/@hug_ge',
        icon: tiktokIcon,
    },
];
</script>

<template>
    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 py-10 sm:gap-10 sm:py-14 md:grid-cols-2 lg:grid-cols-3">
                <div class="md:col-span-2 lg:col-span-1">
                    <p
                        class="text-sm font-semibold tracking-wide text-gray-900 uppercase"
                    >
                        CTS - Trophée de la générosité
                    </p>
                    <p class="mt-3 max-w-md text-sm leading-relaxed text-gray-500">
                        {{ t('footer.description') }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-xs font-semibold tracking-widest text-gray-400 uppercase"
                    >
                        {{ t('footer.social_title') }}
                    </p>
                    <ul class="mt-4 flex items-center gap-3">
                        <li v-for="social in socials" :key="social.name">
                            <a
                                :href="social.href"
                                target="_blank"
                                rel="noopener noreferrer"
                                :aria-label="social.name"
                                class="group inline-flex h-10 w-10 items-center justify-center sm:h-12 sm:w-12"
                            >
                                <span
                                    class="block h-7 w-7 bg-gray-900 transition-opacity group-hover:opacity-75 sm:h-8 sm:w-8"
                                    :style="{
                                        maskImage: `url(${social.icon})`,
                                        maskRepeat: 'no-repeat',
                                        maskPosition: 'center',
                                        maskSize: 'contain',
                                        WebkitMaskImage: `url(${social.icon})`,
                                        WebkitMaskRepeat: 'no-repeat',
                                        WebkitMaskPosition: 'center',
                                        WebkitMaskSize: 'contain',
                                    }"
                                    aria-hidden="true"
                                ></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <Card
                    class="rounded-none border-gray-200 bg-white py-0 shadow-[4px_4px_0_0_#E5E7EB]"
                >
                    <CardContent class="p-6">
                        <p class="text-sm font-semibold text-gray-900">
                            {{ t('footer.collaborateur.title') }}
                        </p>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500">
                            {{ t('footer.collaborateur.description') }}
                        </p>
                        <Button as-child variant="cta" size="cta" class="mt-4">
                            <Link :href="routes.login.url()">
                                {{ t('footer.collaborateur.cta') }}
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <div class="border-t border-gray-100 py-6 text-sm text-gray-400">
                {{ t('footer.copyright') }}
            </div>
        </div>
    </footer>
</template>
