<script setup>
import { computed } from 'vue';
import Footer from '@/components/layout/Footer.vue';
import Navbar from '@/components/layout/Navbar.vue';
import {
    collecte as cobrandCollecte,
    donSang as cobrandDonSang,
    jeu as cobrandJeu,
} from '@/routes/cobrand';
import * as routes from '@/routes/index.ts';

const props = defineProps({
    //si company = null => mode normal, sinon mode co-brandé avec les données de l'entreprise
    company: {
        type: Object,
        default: null,
    },
    token: {
        type: String,
        default: null,
    },
});

const isCobrand = computed(() => props.company !== null);

const routeParams = computed(() => ({
    brandName: props.company?.slug,
    token: props.token,
}));

const links = computed(() =>
    isCobrand.value
        ? [
              {
                  href: cobrandCollecte.url(routeParams.value),
                  label: 'nav.collecte_info',
              },
              {
                  href: cobrandJeu.url(routeParams.value),
                  label: 'nav.eligibilite',
              },
              {
                  href: cobrandDonSang.url(routeParams.value),
                  label: 'nav.don_sang_info',
              },
          ]
        : [
              { href: routes.home.url(), label: 'nav.home' },
              { href: routes.eligibilite.url(), label: 'nav.eligibilite' },
              { href: routes.trophee.url(), label: 'nav.trophee' },
              { href: routes.certification.url(), label: 'nav.certification' },
          ],
);

const cta = computed(() =>
    isCobrand.value
        ? {
              href: cobrandCollecte.url(routeParams.value),
              label: 'nav.cta_inscrire_collecte',
          }
        : { href: routes.collecte.url(), label: 'nav.cta_creer_collecte' },
);

// Couleur de l'entreprise posée en variables CSS, qui cascadent
const brandVars = computed(() => {
    const color = props.company?.color;

    if (!color) {
        return {};
    }

    return {
        '--brand': color,
        '--brand-shadow': `color-mix(in srgb, ${color} 55%, black)`,
        '--brand-hover': `color-mix(in srgb, ${color} 85%, black)`,
        '--brand-tint': `color-mix(in srgb, ${color} 10%, white)`,
    };
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white" :style="brandVars">
        <Navbar
            :links="links"
            :cta="cta"
            :company="company"
            :home-url="routes.home.url()"
            :normal-site-url="routes.home.url()"
        />

        <main class="flex-1">
            <slot />
        </main>

        <Footer />
    </div>
</template>
