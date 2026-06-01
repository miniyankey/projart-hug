import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

/**
 * Gère le cycle « cliquer supprimer → confirmer → DELETE » d'un ConfirmDialog.
 *
 * @param {(item: object) => string} resolveUrl - Construit l'URL DELETE depuis l'élément ciblé.
 * @returns {{
 *   target: import('vue').Ref<object|null>,
 *   open: import('vue').WritableComputedRef<boolean>,
 *   processing: import('vue').Ref<boolean>,
 *   ask: (item: object) => void,
 *   confirm: () => void,
 * }}
 */
export function useDeleteConfirm(resolveUrl) {
    // Élément en attente de confirmation (null = dialog fermé).
    const target = ref(null);
    const processing = ref(false);

    const open = computed({
        get: () => target.value !== null,
        set: (value) => {
            if (!value && !processing.value) {
                target.value = null;
            }
        },
    });

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

    return { target, open, processing, ask, confirm };
}
