<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { Menu, X } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import hugLogo from '@/../images/logos/hug.png';
import LanguageSwitcher from '@/components/layout/LanguageSwitcher.vue';
import { Button } from '@/components/ui/button';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
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
    // Si défini (ex. "#rdv"), le CTA scrolle vers cette ancre de la page
    // courante au lieu de déclencher une navigation.
    ctaScrollTarget: {
        type: String,
        default: null,
    },
});

function isActive(href) {
    return page.url === href;
}

const open = ref(false);
const navRef = ref(null);

function close() {
    open.value = false;
}

// CTA en mode "scroll" : on défile en douceur (GSAP) vers la cible de la page
// courante (ex. bloc RDV) plutôt que de relancer une navigation Inertia.
// offsetY = hauteur de la barre pour ne pas masquer la cible sous la nav sticky.
function scrollToCta() {
    close();

    const target = document.querySelector(props.ctaScrollTarget);

    if (!target) {
        return;
    }

    gsap.to(window, {
        duration: 0.8,
        ease: 'power2.inOut',
        scrollTo: { y: target, offsetY: navRef.value?.offsetHeight ?? 80 },
    });
}

// On ferme le menu mobile dès qu'une navigation Inertia réussit.
let removeListener;
onMounted(() => {
    removeListener = router.on('success', close);
});
onBeforeUnmount(() => removeListener?.());
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white">
        <nav
            ref="navRef"
            class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-3 lg:gap-8 lg:px-6 lg:py-4 xl:gap-10 xl:px-8"
        >
            <!-- Mode co-brandé : logo HUG × logo entreprise -->
            <div
                v-if="company"
                class="flex shrink-0 items-center gap-2 lg:gap-3 xl:gap-4"
            >
                <img
                    :src="hugLogo"
                    alt="Hôpitaux Universitaires Genève"
                    class="h-8 w-auto lg:h-10 xl:h-12"
                />
                <span
                    class="text-base font-light text-gray-400 lg:text-lg xl:text-xl"
                    >×</span
                >
                <img
                    v-if="company.logo"
                    :src="company.logo"
                    :alt="company.name"
                    class="h-8 w-auto max-w-[80px] object-contain lg:h-10 lg:max-w-[100px] xl:h-12 xl:max-w-[130px]"
                />
                <span
                    v-else
                    class="text-sm font-semibold text-gray-900 lg:text-base xl:text-lg"
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
                    class="h-8 w-auto lg:h-10 xl:h-12"
                />
            </Link>

            <div
                class="hidden flex-1 items-center justify-center gap-4 lg:flex xl:gap-8"
            >
                <Link
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    :class="[
                        'border-b-2 pb-1 text-xs whitespace-nowrap transition-colors hover:text-[var(--brand)] xl:text-sm',
                        isActive(link.href)
                            ? 'border-[var(--brand)] font-semibold text-[var(--brand)]'
                            : 'border-transparent text-gray-800',
                    ]"
                >
                    {{ t(link.label) }}
                </Link>
            </div>

            <div class="hidden items-center gap-2 lg:flex xl:gap-4">
                <Button
                    as-child
                    variant="cta"
                    class="h-9 px-4 text-xs xl:text-sm"
                >
                    <a
                        v-if="ctaScrollTarget"
                        :href="ctaScrollTarget"
                        @click.prevent="scrollToCta"
                        >{{ t(cta.label) }}</a
                    >
                    <a
                        v-else-if="cta.external"
                        :href="cta.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        >{{ t(cta.label) }}</a
                    >
                    <Link v-else :href="cta.href">{{ t(cta.label) }}</Link>
                </Button>
                <LanguageSwitcher />
            </div>

            <button
                type="button"
                class="inline-flex h-10 w-10 cursor-pointer items-center justify-center text-gray-800 lg:hidden"
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
            class="border-t border-gray-200 bg-white lg:hidden"
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
                        <a
                            v-if="ctaScrollTarget"
                            :href="ctaScrollTarget"
                            @click.prevent="scrollToCta"
                            >{{ t(cta.label) }}</a
                        >
                        <a
                            v-else-if="cta.external"
                            :href="cta.href"
                            target="_blank"
                            rel="noopener noreferrer"
                            >{{ t(cta.label) }}</a
                        >
                        <Link v-else :href="cta.href">{{ t(cta.label) }}</Link>
                    </Button>
                    <LanguageSwitcher />
                </div>
            </div>
        </div>
    </header>
</template>
