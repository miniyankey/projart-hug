import { getLocalTimeZone, parseDate } from '@internationalized/date';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

/**
 * Formatage de dates localisé, partagé par l'admin et le front public.
 *
 * @returns {{ formatLongDate: (value: string|null|undefined) => string }}
 */
export function useDateFormatter() {
    const { locale } = useI18n();

    /**
     * Formate une date 'YYYY-MM-DD' (ou ISO complet) en date longue localisée,
     * ex. « 15 juin 2026 ». Renvoie '' si la valeur est vide.
     */
    function formatLongDate(value) {
        if (!value) {
            return '';
        }

        return parseDate(String(value).slice(0, 10))
            .toDate(getLocalTimeZone())
            .toLocaleDateString(locale.value, {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
            });
    }

    return { formatLongDate };
}

/**
 * Pont two-way entre une chaîne 'YYYY-MM-DD' (form / back-end) et la DateValue
 * attendue par un <Calendar> mono-date.
 *
 * @param {() => string} getter - Lit la chaîne courante.
 * @param {(value: string) => void} setter - Écrit la nouvelle chaîne ('' si vidée).
 * @returns {import('vue').WritableComputedRef<import('@internationalized/date').DateValue|undefined>}
 */
export function useCalendarDate(getter, setter) {
    return computed({
        get: () => {
            const value = getter();

            return value ? parseDate(value) : undefined;
        },
        set: (value) => setter(value ? value.toString() : ''),
    });
}
