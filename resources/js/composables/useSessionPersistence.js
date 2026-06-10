import { watch } from 'vue';

// Persistance légère en mémoire (portée module). Survit à la navigation interne
// SPA (le composant de page est démonté puis remonté, mais le module JS reste
// chargé) et repart de zéro à tout rechargement complet de la page (refresh,
// nouvel onglet). Volontairement générique : l'appelant fournit son instantané
// et les sources à observer.
const store = new Map();

// Accès direct au store (ex. GameMap qui persiste la position de Pochy).
export function memoryGet(key) {
    return store.has(key) ? store.get(key) : null;
}

export function memorySet(key, value) {
    store.set(key, value);
}

export function useSessionPersistence(key) {
    // Relit l'instantané sauvegardé (ou null si absent).
    function read() {
        return memoryGet(key);
    }

    // Sauvegarde `snapshot()` à chaque changement des `sources` observées.
    function persist(sources, snapshot) {
        watch(
            sources,
            () => {
                memorySet(key, snapshot());
            },
            { deep: true },
        );
    }

    return { read, persist };
}
