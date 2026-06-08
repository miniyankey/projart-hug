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
import { postJson } from '@/lib/http';

// Envoie une requête de tracking à l'action spécifiée, avec les données fournies
function track(action, payload = {}) {
    postJson(action.url(), payload);
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
