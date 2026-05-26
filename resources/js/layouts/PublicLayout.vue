<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Navbar from '@/components/Navbar.vue';
import * as routes from '@/routes/index.ts';


const { t } = useI18n();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white">
        <Navbar />

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-6">
                <!-- Corps du footer -->
                <div class="grid gap-12 py-14 sm:grid-cols-3">
                    <!-- Col 1 : Brand -->
                    <div>
                        <p class="text-sm font-semibold tracking-wide text-gray-900 uppercase">
                            CTS — Trophée de la générosité
                        </p>
                        <p class="mt-3 text-sm leading-relaxed text-gray-500">
                            {{ t('footer.description') }}
                        </p>
                    </div>

                    <!-- Col 2 : Navigation -->
                    <div>
                        <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase">
                            {{ t('footer.nav_title') }}
                        </p>
                        <ul class="mt-4 space-y-2">
                            <li>
                                <Link
                                    :href="routes.home.url()"
                                    class="text-sm text-gray-600 transition-colors hover:text-gray-900"
                                >
                                    {{ t('footer.nav.home') }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    :href="routes.trophee.url()"
                                    class="text-sm text-gray-600 transition-colors hover:text-gray-900"
                                >
                                    {{ t('nav.trophee') }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    :href="routes.collecte.url()"
                                    class="text-sm text-gray-600 transition-colors hover:text-gray-900"
                                >
                                    {{ t('nav.collecte') }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    :href="routes.inscription.url()"
                                    class="text-sm text-gray-600 transition-colors hover:text-gray-900"
                                >
                                    {{ t('nav.inscription') }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 3 : CTA collaborateur -->
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-6">
                        <p class="text-sm font-semibold text-gray-900">
                            {{ t('footer.collaborateur.title') }}
                        </p>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500">
                            {{ t('footer.collaborateur.description') }}
                        </p>
                        <Link
                            :href="routes.login.url()"
                            class="mt-4 inline-flex items-center gap-1.5 rounded border border-gray-900 bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700"
                        >
                            {{ t('footer.collaborateur.cta') }}
                            <span aria-hidden="true"></span>
                        </Link>
                    </div>
                </div>

                <!-- Bas de footer -->
                <div class="border-t border-gray-100 py-6 text-sm text-gray-400">
                    {{ t('footer.copyright') }}
                </div>
            </div>
        </footer>
    </div>
</template>
