//importer grâce a wayfinder
import {
    appointmentClick,
    collecteView,
    eligibiliteStep,
} from '@/actions/App/Http/Controllers/Kpi/CollectEventController';
import {
    click as contactClick,
    sent as contactSent,
    trophee as contactTrophee,
} from '@/actions/App/Http/Controllers/Kpi/ContactFormConversionController';

// Le cookie XSRF-TOKEN posé par Laravel sert à passer la protection CSRF des
// routes web : on le renvoie dans l'en-tête X-XSRF-TOKEN
function readXsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : null;
}

// Envoie une requête de tracking à l'action spécifiée, avec les données fournies
function track(action, payload = {}) {
    const xsrfToken = readXsrfToken();

    fetch(action.url(), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
        },
        credentials: 'same-origin',
        keepalive: true,
        body: JSON.stringify(payload),
    }).catch(() => {});
}

/**
 * Expose les événements de tracking KPI. Chaque appel est non bloquant.
 */
export function useTracking() {
    return {
        // Funnel du jeu d'éligibilité (appelé une fois le jeu implémenté).
        trackEligibiliteStep(
            collectId,
            step,
            { result = null, completed = false } = {},
        ) {
            track(eligibiliteStep, {
                collect_id: collectId,
                step,
                result,
                completed,
            });
        },

        // Visite de la page collecte co-brandée (haut du funnel RDV) : crée la
        // ligne pour distinguer « a vu la page » de « a cliqué vers le RDV ».
        trackCollecteView(collectId) {
            track(collecteView, { collect_id: collectId });
        },

        // Clic vers la prise de RDV externe (conversion don de sang)
        trackAppointmentClick(collectId, source = null) {
            track(appointmentClick, { collect_id: collectId, source });
        },

        // Première interaction avec le formulaire (type = onglet actif)
        trackContactClick(type) {
            track(contactClick, { type });
        },

        // Envoi réussi du formulaire (type = onglet actif)
        trackContactSent(type) {
            track(contactSent, { type });
        },

        // Intérêt déclaré pour la participation au Trophée (checkbox)
        trackTrophee(participation, type) {
            track(contactTrophee, {
                type,
                trophee_participation: participation,
            });
        },
    };
}
