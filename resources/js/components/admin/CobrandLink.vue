<script setup>
import { Check, Copy } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { collecte } from '@/routes/cobrand';

// Affiche le lien co-brandé d'une collecte (slug entreprise + slug collecte)
// avec un bouton de copie. Le lien renvoie 404 si la collecte est désactivée.
const props = defineProps({
    companySlug: {
        type: String,
        required: true,
    },
    collectSlug: {
        type: String,
        required: true,
    },
});

const { t } = useI18n();
const copied = ref(false);

const path = computed(
    () =>
        collecte({
            brandName: props.companySlug,
            collect: props.collectSlug,
        }).url,
);
const fullUrl = computed(() => window.location.origin + path.value);

async function copy() {
    await navigator.clipboard.writeText(fullUrl.value);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
}
</script>

<template>
    <div class="flex items-center gap-2">
        <code
            class="max-w-[220px] truncate border-2 border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-700"
            :title="fullUrl"
        >
            {{ path }}
        </code>
        <button
            type="button"
            class="flex items-center gap-1 border-2 border-gray-900 bg-white px-2 py-1 text-xs font-semibold text-gray-900 transition-colors hover:bg-gray-100"
            :title="t('admin.entreprises.copy')"
            @click="copy"
        >
            <component :is="copied ? Check : Copy" class="size-3.5" />
            {{
                copied
                    ? t('admin.entreprises.copied')
                    : t('admin.entreprises.copy')
            }}
        </button>
    </div>
</template>
