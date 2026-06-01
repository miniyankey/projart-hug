import { createInertiaApp, router } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ScrambleTextPlugin } from 'gsap/ScrambleTextPlugin';
import { ScrollToPlugin } from 'gsap/ScrollToPlugin';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { TextPlugin } from 'gsap/TextPlugin';
import { createApp, h } from 'vue';

gsap.registerPlugin(
    ScrollTrigger,
    TextPlugin,
    ScrambleTextPlugin,
    ScrollToPlugin,
);
import { createAppI18n, DEFAULT_LOCALE, SUPPORTED_LOCALES } from './i18n.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

function resolveLocale(value) {
    return SUPPORTED_LOCALES.includes(value) ? value : DEFAULT_LOCALE;
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        //check de la locale
        const initialLocale = resolveLocale(props.initialPage.props.locale);
        const i18n = createAppI18n(initialLocale);

        //modifie l'attribut lang de la balise HTML
        document.documentElement.lang = initialLocale;

        // success se déclenche quand une navigation réussie a lieu, c'est à dire que la page a été chargée et rendue avec succès
        router.on('success', (event) => {
            const locale = resolveLocale(event.detail.page.props.locale);

            if (i18n.global.locale.value !== locale) {
                // permet de notifier tous les components qui utilisent la locale (ou la fn t()) que celle ci a changé
                i18n.global.locale.value = locale;
                document.documentElement.lang = locale;
            }
        });

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .mount(el);
    },
});
