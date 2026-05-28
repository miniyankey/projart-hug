<script setup>
import { Link, router } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import hugLogo from '@/../images/logos/hug.png';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { Button } from '@/components/ui/button';
import * as routes from '@/routes/index.ts';

const { t } = useI18n();
const open = ref(false);

function close() {
    open.value = false;
}

// On ferme le menu mobile dès qu'une navigation Inertia réussit.
let removeListener;
onMounted(() => {
    removeListener = router.on('success', close);
});
onBeforeUnmount(() => removeListener?.());
</script>

<template>
    <header class="border-b border-gray-200 bg-white">
        <nav
            class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 md:gap-8 md:px-6 md:py-4"
        >
            <Link
                :href="routes.home.url()"
                class="flex shrink-0 items-center"
                aria-label="Hôpitaux Universitaires Genève"
            >
                <img
                    :src="hugLogo"
                    alt="Hôpitaux Universitaires Genève"
                    class="h-8 w-auto md:h-12"
                />
            </Link>

            <div class="hidden items-center gap-10 md:flex">
                <Link
                    :href="routes.home.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.home') }}
                </Link>
                <Link
                    :href="routes.eligibilite.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.eligibilite') }}
                </Link>
                <Link
                    :href="routes.trophee.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.trophee') }}
                </Link>
                <Link
                    :href="routes.certification.url()"
                    class="text-sm text-gray-800 transition-colors hover:text-gray-900"
                >
                    {{ t('nav.certification') }}
                </Link>
            </div>

            <div class="hidden items-center gap-6 md:flex">
                <Button as-child variant="cta" size="cta">
                    <Link :href="routes.collecte.url()">
                        {{ t('nav.cta_creer_collecte') }}
                    </Link>
                </Button>
                <LanguageSwitcher />
            </div>

            <button
                type="button"
                class="inline-flex h-10 w-10 cursor-pointer items-center justify-center text-gray-800 md:hidden"
                :aria-label="open ? t('nav.menu_close') : t('nav.menu_open')"
                :aria-expanded="open"
                aria-controls="mobile-menu"
                @click="open = !open"
            >
                <X v-if="open" class="h-6 w-6" aria-hidden="true" />
                <Menu v-else class="h-6 w-6" aria-hidden="true" />
            </button>
        </nav>

        <div
            v-if="open"
            id="mobile-menu"
            class="border-t border-gray-200 bg-white md:hidden"
        >
            <div class="flex flex-col gap-1 px-4 py-4">
                <Link
                    :href="routes.home.url()"
                    class="rounded px-2 py-2 text-base text-gray-800 hover:bg-gray-50"
                >
                    {{ t('nav.home') }}
                </Link>
                <Link
                    :href="routes.eligibilite.url()"
                    class="rounded px-2 py-2 text-base text-gray-800 hover:bg-gray-50"
                >
                    {{ t('nav.eligibilite') }}
                </Link>
                <Link
                    :href="routes.trophee.url()"
                    class="rounded px-2 py-2 text-base text-gray-800 hover:bg-gray-50"
                >
                    {{ t('nav.trophee') }}
                </Link>
                <Link
                    :href="routes.certification.url()"
                    class="rounded px-2 py-2 text-base text-gray-800 hover:bg-gray-50"
                >
                    {{ t('nav.certification') }}
                </Link>

                <div
                    class="mt-4 flex flex-col gap-4 border-t border-gray-200 pt-4"
                >
                    <Button as-child variant="cta" size="cta" class="w-fit">
                        <Link :href="routes.collecte.url()">
                            {{ t('nav.cta_creer_collecte') }}
                        </Link>
                    </Button>
                    <LanguageSwitcher />
                </div>
            </div>
        </div>
    </header>
</template>
