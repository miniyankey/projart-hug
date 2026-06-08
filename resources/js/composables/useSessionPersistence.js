import { watch } from 'vue';

// Persistance légère dans sessionStorage. Survit à la navigation interne SPA
// (le composant de page est démonté puis remonté) mais pas à la fermeture de
// l'onglet → le jeu reprend là où on l'a laissé en naviguant, et repart de zéro
// quand l'onglet est fermé. Volontairement générique : l'appelant fournit son
// instantané et les sources à observer.
export function useSessionPersistence(key) {
    // Relit l'instantané sauvegardé (ou null si absent / illisible).
    function read() {
        try {
            return JSON.parse(sessionStorage.getItem(key) ?? 'null');
        } catch {
            return null;
        }
    }

    // Sauvegarde `snapshot()` à chaque changement des `sources` observées.
    function persist(sources, snapshot) {
        watch(
            sources,
            () => {
                try {
                    sessionStorage.setItem(key, JSON.stringify(snapshot()));
                } catch {
                    /* quota dépassé / stockage indisponible → on ignore */
                }
            },
            { deep: true },
        );
    }

    return { read, persist };
}
