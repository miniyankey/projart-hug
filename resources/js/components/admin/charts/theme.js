// Thème partagé des graphiques admin : aligné sur la charte du reste du sitee
const BRAND_FALLBACK = '#8b2cf1';

/**
 * Lit la couleur de marque courante (--brand) résolue sur le document.
 * Côté client uniquement ; renvoie le violet par défaut sinon.
 */
export function brandColor() {
    // retourne la valeur css de la variable --brand définie dans le css (trop bien cette fn)
    const value = getComputedStyle(document.documentElement)
        .getPropertyValue('--brand')
        .trim();

    return value || BRAND_FALLBACK;
}

// Couleurs neutres réutilisées par les axes / grilles.
export const ink = '#111827'; // gray-900
export const muted = '#6b7280'; // gray-500
export const gridColor = '#e5e7eb'; // gray-200

// couleurs d'accents
export const accentBlue = '#2563eb';
export const accentYellow = '#eab308';
export const accentTeal = '#0d9488';

/**
 * Options communes à tous les graphiques : police, désactivation du toolbar et
 * des animations superflues, rendu net.
 *
 * @returns {object}
 */
export function baseOptions() {
    return {
        chart: {
            fontFamily: 'inherit',
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: { enabled: true, speed: 300 },
            foreColor: muted,
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 0,
        },
        dataLabels: { enabled: false },
        tooltip: {
            theme: 'light',
            style: { fontFamily: 'inherit' },
        },
        states: {
            hover: { filter: { type: 'darken', value: 0.9 } },
        },
    };
}
