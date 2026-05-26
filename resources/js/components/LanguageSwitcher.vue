<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import LocaleController from '../actions/App/Http/Controllers/LocaleController';
import { SUPPORTED_LOCALES } from '../i18n.js';

const page = usePage();
const { t } = useI18n();

const currentLocale = computed(() => page.props.locale);

function switchTo(locale) {
    if (locale === currentLocale.value) {
        return;
    }

    router.post(
        LocaleController.update.url(),
        { locale },
        { preserveScroll: true, preserveState: false },
    );
}
</script>

<template>
    <div
        class="inline-flex items-center gap-1 text-sm"
        role="group"
        :aria-label="t('languageSwitcher.label')"
    >
        <button
            v-for="locale in SUPPORTED_LOCALES"
            :key="locale"
            type="button"
            class="cursor-pointer rounded border border-gray-300 px-2 py-1 uppercase transition-colors disabled:cursor-default"
            :class="
                locale === currentLocale
                    ? 'bg-gray-900 text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-100'
            "
            :disabled="locale === currentLocale"
            :aria-pressed="locale === currentLocale"
            :title="
                t('languageSwitcher.switchTo', {
                    language: t(`common.languages.${locale}`),
                })
            "
            @click="switchTo(locale)"
        >
            {{ locale }}
        </button>
    </div>
</template>
