<script setup>
// Bouton de réponse néo-brutaliste.
// - Défaut : fond blanc, bordure noire, ombre portée dure.
// - Pressé (:active) ou sélectionné : fond couleur de marque, texte blanc, à plat.
// - `multiple` : ajoute une case à cocher (coche affichée quand sélectionné).
// La couleur apparaît dès l'enfoncement du clic et reste à la sélection.
defineProps({
    label: { type: String, required: true },
    descr: { type: String, default: null },
    selected: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
});

defineEmits(['toggle']);
</script>

<template>
    <button
        type="button"
        class="choice"
        :class="{ 'choice--selected': selected }"
        :aria-pressed="selected"
        @click="$emit('toggle')"
    >
        <!-- Case à cocher (choix multiples uniquement) -->
        <span v-if="multiple" class="choice__box" aria-hidden="true">
            <svg v-if="selected" class="choice__check" viewBox="0 0 24 24">
                <path
                    d="M5 13l4 4L19 7"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="3"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </span>

        <span class="choice__text">
            <span class="choice__label">{{ label }}</span>
            <span v-if="descr" class="choice__descr">{{ descr }}</span>
        </span>
    </button>
</template>

<style scoped>
.choice {
    display: inline-flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.85rem 1.1rem;
    text-align: left;
    color: #111;
    background: white;
    /* Contour couleur de marque → signale un élément interactif */
    border: 3px solid var(--brand, #7c3aed);
    /* Ombre = nuance plus foncée de la couleur de l'entreprise */
    box-shadow: 6px 6px 0 var(--brand-shadow, #4c1d95);
    cursor: pointer;
    /* Pas de transition sur la couleur : feedback instantané au clic */
    transition:
        transform 60ms ease,
        box-shadow 60ms ease;
}

/* Couleur dès l'enfoncement, et conservée quand le bouton est sélectionné */
.choice:active,
.choice--selected {
    color: white;
    background: var(--brand, #7c3aed);
    border-color: var(--brand, #7c3aed);
    box-shadow: none;
}

.choice__box {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.9rem;
    height: 1.9rem;
    border: 3px solid var(--brand, #7c3aed); /* brand par défaut */
}

/* Une fois pressé / sélectionné (fond brand), la case passe en blanc */
.choice:active .choice__box,
.choice--selected .choice__box {
    border-color: currentColor;
}

.choice__check {
    width: 100%;
    height: 100%;
    color: currentColor;
}

.choice__text {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}

.choice__label {
    font-size: 1.05rem;
    font-weight: 600;
    line-height: 1.3;
}

.choice__descr {
    font-size: 0.95rem;
    font-weight: 400;
    line-height: 1.3;
    opacity: 0.85;
}
</style>
