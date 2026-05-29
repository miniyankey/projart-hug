<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { store } from '@/actions/App/Http/Controllers/FormSubmissionController';
import MultiDatePicker from '@/components/MultiDatePicker.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';

const { t } = useI18n();

const TYPE_CONTACT = 'contact';
const TYPE_COLLECT = 'collect_request';

// null | 'success' | 'error' : pilote la bannière de retour.
const status = ref(null);

const form = useForm({
    type: TYPE_CONTACT,
    name: '',
    contact_email: '',
    company_name: props.company,
    message: '',
    trophy_participation: false,
    preferred_dates: [],
});

const isCollect = computed(() => form.type === TYPE_COLLECT);

// Date minimale = demain 
const minDate = computed(() => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);

    return tomorrow.toISOString().slice(0, 10);
});

function selectType(type) {
    if (form.type === type) {
        return;
    }

    form.type = type;
    form.clearErrors();

    // On masque la bannière de retour lors d'un changement de vue
    status.value = null;

    if (type === TYPE_CONTACT) {
        form.trophy_participation = false;
        form.preferred_dates = [];
    }
}

function submit() {
    status.value = null;

    form.transform((data) => ({
        ...data,
        // On retire les chaînes vides du tableau de dates avant l'envoi
        preferred_dates: data.preferred_dates.filter((date) => date !== ''),
    })).post(store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            status.value = 'success';
            form.reset(
                'name',
                'contact_email',
                'message',
                'trophy_participation',
                'preferred_dates',
            );

            if (!props.company) {
                form.reset('company_name');
            }
        },
        onError: () => {
            status.value = 'error';
        },
    });
}
</script>

<template>
    <div class="bg-[#9D6CE0] p-6 shadow-[10px_10px_0_0_#5B21B6] sm:p-8">
        <!-- toogle -->
        <div class="mb-8 flex flex-col gap-2 sm:flex-row">
            <button
                type="button"
                class="flex-1 border-2 border-black px-4 py-2.5 font-mono text-sm font-bold tracking-wide uppercase transition-all"
                :class="
                    form.type === TYPE_CONTACT
                        ? 'bg-[#5B21B6] text-white shadow-[4px_4px_0_0_#2D1B4E] hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#2D1B4E] active:translate-y-0 active:shadow-[2px_2px_0_0_#2D1B4E]'
                        : 'bg-white text-[#2D1B4E] shadow-[4px_4px_0_0_#000] hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow-[5px_5px_0_0_#000] active:translate-y-0 active:shadow-[2px_2px_0_0_#000]'
                "
                @click="selectType(TYPE_CONTACT)"
            >
                {{ t('collecte.form.tab_contact') }}
            </button>
            <button
                type="button"
                class="flex-1 border-2 border-black px-4 py-2.5 font-mono text-sm font-bold tracking-wide uppercase transition-all"
                :class="
                    form.type === TYPE_COLLECT
                        ? 'bg-[#5B21B6] text-white shadow-[4px_4px_0_0_#2D1B4E] hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#2D1B4E] active:translate-y-0 active:shadow-[2px_2px_0_0_#2D1B4E]'
                        : 'bg-white text-[#2D1B4E] shadow-[4px_4px_0_0_#000] hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow-[5px_5px_0_0_#000] active:translate-y-0 active:shadow-[2px_2px_0_0_#000]'
                "
                @click="selectType(TYPE_COLLECT)"
            >
                {{ t('collecte.form.tab_collect') }}
            </button>
        </div>

        <p
            v-if="status"
            class="mb-6 border-2 px-4 py-3 font-mono text-sm"
            :class="
                status === 'success'
                    ? 'border-green-700 bg-green-50 text-green-800 shadow-[4px_4px_0_0_#15803d]'
                    : 'border-red-700 bg-red-50 text-red-800 shadow-[4px_4px_0_0_#b91c1c]'
            "
        >
            {{
                status === 'success'
                    ? t('collecte.form.success')
                    : t('collecte.form.error')
            }}
        </p>

        <form class="flex flex-col gap-6" @submit.prevent="submit">
            <!-- Nom -->
            <div class="flex flex-col gap-2">
                <label
                    for="name"
                    class="font-mono text-sm font-bold tracking-wide text-[#2D1B4E] uppercase"
                >
                    {{ t('collecte.form.name') }}
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="h-11 w-full border-2 border-black bg-white px-3 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] outline-none placeholder:text-gray-400 focus:ring-2 focus:ring-[#5B21B6]"
                    :placeholder="t('collecte.form.name_placeholder')"
                />
                <p
                    v-if="form.errors.name"
                    class="font-mono text-xs text-red-700"
                >
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-2">
                <label
                    for="contact_email"
                    class="font-mono text-sm font-bold tracking-wide text-[#2D1B4E] uppercase"
                >
                    {{ t('collecte.form.email') }}
                </label>
                <input
                    id="contact_email"
                    v-model="form.contact_email"
                    type="email"
                    autocomplete="email"
                    class="h-11 w-full border-2 border-black bg-white px-3 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] outline-none placeholder:text-gray-400 focus:ring-2 focus:ring-[#5B21B6]"
                    :placeholder="t('collecte.form.email_placeholder')"
                />
                <p
                    v-if="form.errors.contact_email"
                    class="font-mono text-xs text-red-700"
                >
                    {{ form.errors.contact_email }}
                </p>
            </div>

            <!-- Entreprise -->
            <div class="flex flex-col gap-2">
                <label
                    for="company_name"
                    class="font-mono text-sm font-bold tracking-wide text-[#2D1B4E] uppercase"
                >
                    {{ t('collecte.form.company') }}
                </label>
                <input
                    id="company_name"
                    v-model="form.company_name"
                    type="text"
                    :readonly="!!company"
                    class="h-11 w-full border-2 border-black bg-white px-3 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] outline-none placeholder:text-gray-400 read-only:bg-gray-100 read-only:text-gray-600 focus:ring-2 focus:ring-[#5B21B6]"
                    :placeholder="t('collecte.form.company_placeholder')"
                />
                <p
                    v-if="form.errors.company_name"
                    class="font-mono text-xs text-red-700"
                >
                    {{ form.errors.company_name }}
                </p>
            </div>

            <!-- Dates souhaitées (collecte uniquement) -->
            <div v-if="isCollect" class="flex flex-col gap-2">
                <label
                    class="font-mono text-sm font-bold tracking-wide text-[#2D1B4E] uppercase"
                >
                    {{ t('collecte.form.dates') }}
                </label>
                <MultiDatePicker
                    v-model="form.preferred_dates"
                    :min="minDate"
                />
                <p
                    v-if="form.errors.preferred_dates"
                    class="font-mono text-xs text-red-700"
                >
                    {{ form.errors.preferred_dates }}
                </p>
            </div>

            <!-- Message -->
            <div class="flex flex-col gap-2">
                <label
                    for="message"
                    class="font-mono text-sm font-bold tracking-wide text-[#2D1B4E] uppercase"
                >
                    {{ t('collecte.form.message') }}
                </label>
                <textarea
                    id="message"
                    v-model="form.message"
                    rows="5"
                    class="w-full border-2 border-black bg-white px-3 py-2 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] outline-none placeholder:text-gray-400 focus:ring-2 focus:ring-[#5B21B6]"
                    :placeholder="t('collecte.form.message_placeholder')"
                ></textarea>
                <p
                    v-if="form.errors.message"
                    class="font-mono text-xs text-red-700"
                >
                    {{ form.errors.message }}
                </p>
            </div>

            <!-- Participation au Trophée (collecte uniquement) -->
            <label
                v-if="isCollect"
                class="flex cursor-pointer items-center gap-3 font-mono text-sm font-bold tracking-wide text-[#2D1B4E] uppercase"
            >
                <Checkbox v-model="form.trophy_participation" />
                {{ t('collecte.form.trophy') }}
            </label>

            <Button
                type="submit"
                variant="cta"
                size="cta"
                class="w-fit font-mono tracking-wide uppercase"
                :disabled="form.processing"
            >
                {{ t('collecte.form.submit') }}
            </Button>
        </form>
    </div>
</template>
