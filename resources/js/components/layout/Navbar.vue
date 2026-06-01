<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import hugLogo from '@/../images/logos/hug.png';
import LanguageSwitcher from '@/components/layout/LanguageSwitcher.vue';
import { Button } from '@/components/ui/button';

const { t } = useI18n();
const page = usePage();

defineProps({
    // Liens de navigation : [{ href, label }] où label est une clé i18n
    links: {
        type: Array,
        required: true,
    },
    // Bouton d'action principal : { href, label } (label = clé i18n)
    cta: {
        type: Object,
        required: true,
    },
    // Entreprise partenaire pour le mode co-brandé : { name, logo
    // null = mode HUG normal (logo seul, cliquable vers l'accueil)
    company: {
        type: Object,
        default: null,
    },
    // Lien du logo HUG en mode normal.
    homeUrl: {
        type: String,
        default: '#',
    },
    // Lien vers le site normal (utilisé comme bouton "retour" en mode co-brandé).
    normalSiteUrl: {
        type: String,
        default: '#',
    },
});

function isActive(href) {
    return page.url === href;
}

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
            <!-- Mode co-brandé : logo HUG × logo entreprise -->
            <div
                v-if="company"
                class="flex shrink-0 items-center gap-3 md:gap-4"
            >
                <img
                    :src="hugLogo"
                    alt="Hôpitaux Universitaires Genève"
                    class="h-8 w-auto md:h-12"
                />
                <span class="text-lg font-light text-gray-400 md:text-xl"
                    >×</span
                >
                <img
                    v-if="company.logo"
                    :src="company.logo"
                    :alt="company.name"
                    class="h-8 w-auto md:h-12"
                />
                <span
                    v-else
                    class="text-base font-semibold text-gray-900 md:text-lg"
                >
                    {{ company.name }}
                </span>
            </div>

            <!-- Mode normal : logo HUG cliquable -->
            <Link
                v-else
                :href="homeUrl"
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
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    :class="[
                        'border-b-2 pb-1 text-sm transition-colors hover:text-[var(--brand)]',
                        isActive(link.href)
                            ? 'border-[var(--brand)] font-semibold text-[var(--brand)]'
                            : 'border-transparent text-gray-800',
                    ]"
                >
                    {{ t(link.label) }}
                </Link>
            </div>

            <div class="hidden items-center gap-6 md:flex">
                <Button as-child variant="cta" size="cta">
                    <Link :href="cta.href">{{ t(cta.label) }}</Link>
                </Button>
                <LanguageSwitcher />
                <Link
                    v-if="company"
                    :href="normalSiteUrl"
                    class="text-xs text-gray-500 transition-colors hover:text-[var(--brand)]"
                >
                    {{ t('nav.main_site') }}
                </Link>
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
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    :class="[
                        'rounded px-2 py-2 text-base',
                        isActive(link.href)
                            ? 'bg-[var(--brand-tint)] font-semibold text-[var(--brand)]'
                            : 'text-gray-800 hover:bg-gray-50 hover:text-[var(--brand)]',
                    ]"
                >
                    {{ t(link.label) }}
                </Link>

                <div
                    class="mt-4 flex flex-col gap-4 border-t border-gray-200 pt-4"
                >
                    <Button as-child variant="cta" size="cta" class="w-fit">
                        <Link :href="cta.href">{{ t(cta.label) }}</Link>
                    </Button>
                    <Link
                        v-if="company"
                        :href="normalSiteUrl"
                        class="text-sm text-gray-500 transition-colors hover:text-[var(--brand)]"
                    >
                        {{ t('nav.main_site') }}
                    </Link>
                    <LanguageSwitcher />
                </div>
            </div>
        </div>
    </header>
</template>
