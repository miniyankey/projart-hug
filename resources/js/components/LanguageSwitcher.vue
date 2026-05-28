<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import LocaleController from '../actions/App/Http/Controllers/LocaleController';
import { SUPPORTED_LOCALES } from '../i18n.js';

const page = usePage();
const { t } = useI18n();

const currentLocale = computed(() => page.props.locale);
const open = ref(false);
const root = ref(null);

function toggle() {
    open.value = !open.value;
}

function close() {
    open.value = false;
}

function switchTo(locale) {
    close();
    if (locale === currentLocale.value) {
        return;
    }

    router.post(
        LocaleController.update.url(),
        { locale },
        { preserveScroll: true, preserveState: false },
    );
}

function onDocClick(e) {
    if (root.value && !root.value.contains(e.target)) {
        close();
    }
}

onMounted(() => document.addEventListener('click', onDocClick));
onBeforeUnmount(() => document.removeEventListener('click', onDocClick));
</script>

<template>
    <div ref="root" class="relative text-sm">
        <button
            type="button"
            class="inline-flex cursor-pointer items-center gap-2 border border-[#7C3AED] px-3 py-1.5 font-medium text-[#7C3AED] uppercase transition-colors hover:bg-[#7C3AED]/5"
            :aria-label="t('languageSwitcher.label')"
            :aria-expanded="open"
            aria-haspopup="listbox"
            @click="toggle"
        >
            <span>{{ currentLocale }}</span>
            <svg
                class="h-3 w-3"
                viewBox="0 0 12 12"
                fill="none"
                aria-hidden="true"
            >
                <path
                    d="M3 4.5L6 7.5L9 4.5"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </button>

        <ul
            v-if="open"
            role="listbox"
            class="absolute right-0 z-20 mt-1 min-w-full border border-[#7C3AED] bg-white shadow-sm"
        >
            <li v-for="locale in SUPPORTED_LOCALES" :key="locale" role="option">
                <button
                    type="button"
                    class="block w-full cursor-pointer px-3 py-1.5 text-left uppercase transition-colors"
                    :class="
                        locale === currentLocale
                            ? 'bg-[#7C3AED]/10 text-[#7C3AED]'
                            : 'text-gray-700 hover:bg-gray-50'
                    "
                    :aria-selected="locale === currentLocale"
                    :title="
                        t('languageSwitcher.switchTo', {
                            language: t(`common.languages.${locale}`),
                        })
                    "
                    @click="switchTo(locale)"
                >
                    {{ locale }}
                </button>
            </li>
        </ul>
    </div>
</template>
