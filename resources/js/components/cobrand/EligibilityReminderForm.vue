<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { store } from '@/actions/App/Http/Controllers/EligibilityReminderController';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps({
    collectId: {
        type: Number,
        default: null,
    },
    // Jours d'inéligibilité calculés côté front (lib/eligibility.js).
    days: {
        type: Number,
        default: 0,
    },
});

const { t } = useI18n();
const page = usePage();

// Clé de traduction renvoyée par le back (flash.reminder_*) une fois la demande traitée.
const resultKey = ref(null);

const form = useForm({
    email: '',
    collect_id: props.collectId,
    days: props.days,
    newsletter: false,
});

function submit() {
    resultKey.value = null;

    form.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            resultKey.value = page.props.flash?.success ?? null;
            form.reset('email');
        },
    });
}
</script>

<template>
    <div
        class="border-2 border-black bg-white p-6 shadow-[8px_8px_0_0_var(--brand-shadow)] sm:p-8"
    >
        <h2
            class="mb-2 font-mono text-lg font-bold tracking-wide text-[#2D1B4E] uppercase"
        >
            {{ t('eligibilite.reminder.title') }}
        </h2>
        <p class="mb-6 text-sm text-gray-600">
            {{ t('eligibilite.reminder.intro') }}
        </p>

        <p
            v-if="resultKey"
            class="mb-6 border-2 border-green-700 bg-green-50 px-4 py-3 font-mono text-sm text-green-800 shadow-[4px_4px_0_0_#15803d]"
        >
            {{ t(resultKey) }}
        </p>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label
                    for="reminder_email"
                    class="font-mono text-sm font-bold tracking-wide text-[#2D1B4E] uppercase"
                >
                    {{ t('eligibilite.reminder.email') }}
                </label>
                <input
                    id="reminder_email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    class="h-11 w-full border-2 border-black bg-white px-3 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] outline-none placeholder:text-gray-400 focus:ring-2 focus:ring-[var(--brand)]"
                    :placeholder="t('eligibilite.reminder.email_placeholder')"
                />
                <p
                    v-if="form.errors.email"
                    class="font-mono text-xs text-red-700"
                >
                    {{ form.errors.email }}
                </p>
                <p
                    v-if="form.errors.days"
                    class="font-mono text-xs text-red-700"
                >
                    {{ form.errors.days }}
                </p>
            </div>

            <label
                class="flex cursor-pointer items-start gap-3 text-sm text-gray-600"
            >
                <Checkbox v-model="form.newsletter" class="mt-0.5 shrink-0" />
                {{ t('eligibilite.reminder.newsletter') }}
            </label>

            <Button
                type="submit"
                variant="cta"
                size="cta"
                class="w-fit font-mono tracking-wide uppercase"
                :disabled="form.processing"
            >
                {{ t('eligibilite.reminder.submit') }}
            </Button>
        </form>
    </div>
</template>
