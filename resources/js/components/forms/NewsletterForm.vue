<script setup>
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { readXsrfToken } from '@/lib/http';
import { subscribe, unsubscribe } from '@/routes/newsletter';

const props = defineProps({
    mode: {
        type: String,
        default: 'subscribe',
        validator: (v) => ['subscribe', 'unsubscribe'].includes(v),
    },
});

const { t } = useI18n();

const email = ref('');
const consent = ref(false);
const processing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const emailError = ref('');

const title = computed(() => {
    if (props.mode === 'unsubscribe') {
        return t('newsletter.unsubscribe_title');
    } else {
        return t('newsletter.title');
    }
});

const subtitle = computed(() => {
    if (props.mode === 'unsubscribe') {
        return t('newsletter.unsubscribe_subtitle');
    } else {
        return t('newsletter.subtitle');
    }
});

const submitLabel = computed(() => {
    if (props.mode === 'unsubscribe') {
        return t('newsletter.unsubscribe_submit');
    } else {
        return t('newsletter.submit');
    }
});

async function submit() {
    successMessage.value = '';
    errorMessage.value = '';
    emailError.value = '';
    processing.value = true;

    let url;

    if (props.mode === 'unsubscribe') {
        url = unsubscribe.url();
    } else {
        url = subscribe.url();
    }

    let response;

    try {
        response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': readXsrfToken() ?? '',
            },
            body: JSON.stringify({ email: email.value }),
        });
    } catch {
        // Erreur réseau (pas de connexion, timeout, etc.)
        if (props.mode === 'unsubscribe') {
            errorMessage.value = t('newsletter.unsubscribe_error');
        } else {
            errorMessage.value = t('newsletter.error');
        }

        processing.value = false;

        return;
    }

    processing.value = false;
    const data = await response.json();

    if (response.ok) {
        if (props.mode === 'unsubscribe') {
            successMessage.value = t('newsletter.unsubscribe_success');
        } else {
            successMessage.value = t('newsletter.success');
        }

        email.value = '';
        consent.value = false;
    } else if (response.status === 422) {
        // Erreur de validation Laravel : { errors: { email: ['...'] } }
        if (data.errors?.email) {
            if (Array.isArray(data.errors.email)) {
                emailError.value = data.errors.email[0];
            } else {
                emailError.value = data.errors.email;
            }
        } else {
            if (props.mode === 'unsubscribe') {
                errorMessage.value = t('newsletter.unsubscribe_error');
            } else {
                errorMessage.value = t('newsletter.error');
            }
        }
    } else {
        if (props.mode === 'unsubscribe') {
            errorMessage.value = t('newsletter.unsubscribe_error');
        } else {
            errorMessage.value = t('newsletter.error');
        }
    }
}
</script>

<template>
    <div
        class="border-2 border-black bg-white p-6 shadow-[8px_8px_0_0_#111] sm:p-8"
    >
        <h2
            class="mb-2 font-mono text-lg font-bold tracking-wide text-gray-900 uppercase"
        >
            {{ title }}
        </h2>
        <p class="mb-6 text-sm text-gray-600">
            {{ subtitle }}
        </p>

        <p
            v-if="successMessage"
            class="mb-6 border-2 border-green-700 bg-green-50 px-4 py-3 font-mono text-sm text-green-800 shadow-[4px_4px_0_0_#15803d]"
        >
            {{ successMessage }}
        </p>

        <p
            v-if="errorMessage"
            class="mb-6 border-2 border-red-700 bg-red-50 px-4 py-3 font-mono text-sm text-red-800 shadow-[4px_4px_0_0_#b91c1c]"
        >
            {{ errorMessage }}
        </p>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label
                    for="newsletter_email"
                    class="font-mono text-sm font-bold tracking-wide text-gray-900 uppercase"
                >
                    {{ t('newsletter.email_label') }}
                </label>

                <input
                    id="newsletter_email"
                    v-model="email"
                    type="email"
                    autocomplete="email"
                    required
                    class="h-11 w-full border-2 border-black bg-white px-3 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] outline-none placeholder:text-gray-400 focus:ring-2 focus:ring-black"
                    :placeholder="t('newsletter.email_placeholder')"
                />

                <p v-if="emailError" class="font-mono text-xs text-red-700">
                    {{ emailError }}
                </p>
            </div>

            <label
                v-if="mode === 'subscribe'"
                class="flex cursor-pointer items-start gap-3 text-sm text-gray-600"
            >
                <Checkbox v-model="consent" class="mt-0.5 shrink-0" />
                {{ t('newsletter.consent_label') }}
            </label>

            <Button
                type="submit"
                variant="pixel_violet"
                class="w-fit font-mono tracking-wide uppercase"
                :disabled="processing || (mode === 'subscribe' && !consent)"
            >
                {{ submitLabel }}
            </Button>
        </form>
    </div>
</template>
