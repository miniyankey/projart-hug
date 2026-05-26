import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import fr from './locales/fr.json';

export const SUPPORTED_LOCALES = ['fr', 'en'];
export const DEFAULT_LOCALE = 'fr';

//Crée l'instance vue-i18n de l'application.
export function createAppI18n(locale = DEFAULT_LOCALE) {
    return createI18n({
        legacy: false,
        locale: SUPPORTED_LOCALES.includes(locale) ? locale : DEFAULT_LOCALE,
        fallbackLocale: DEFAULT_LOCALE,
        messages: { fr, en },
    });
}
