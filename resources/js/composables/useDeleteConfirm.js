import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

/**
 * Gère le cycle « cliquer supprimer → confirmer → DELETE » d'un ConfirmDialog.
 *
 * @param {(item: object) => string} resolveUrl - Construit l'URL DELETE depuis l'élément ciblé.
 * @returns {{
 *   target: import('vue').Ref<object|null>,
 *   processing: import('vue').Ref<boolean>,
 *   ask: (item: object) => void,
 *   confirm: () => void,
 *   onOpenChange: (open: boolean) => void,
 * }}
 */
export function useDeleteConfirm(resolveUrl) {
    // Élément en attente de confirmation (null = dialog fermé).
    const target = ref(null);
    const processing = ref(false);

    function ask(item) {
        target.value = item;
    }

    function confirm() {
        if (!target.value) {
            return;
        }

        router.delete(resolveUrl(target.value), {
            preserveScroll: true,
            onStart: () => {
                processing.value = true;
            },
            onFinish: () => {
                processing.value = false;
                target.value = null;
            },
        });
    }

    function onOpenChange(open) {
        if (!open && !processing.value) {
            target.value = null;
        }
    }

    return { target, processing, ask, confirm, onOpenChange };
}
