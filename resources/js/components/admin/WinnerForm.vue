<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import FormField from '@/components/admin/FormField.vue';
import FormSection from '@/components/admin/FormSection.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/vainqueurs';

const { t } = useI18n();

const props = defineProps({
    // Vainqueur (trophée) à éditer, ou null en mode création.
    trophee: {
        type: Object,
        default: null,
    },
    companies: {
        type: Array,
        default: () => [],
    },
    submitUrl: {
        type: String,
        required: true,
    },
    submitLabel: {
        type: String,
        required: true,
    },
    // 'post' (création) ou 'put' (édition, via spoofing _method).
    method: {
        type: String,
        default: 'post',
    },
});

const isEdit = computed(() => props.method === 'put');

const form = useForm({
    ...(isEdit.value ? { _method: 'put' } : {}),
    company_id: props.trophee?.company_id ?? '',
    year_of: props.trophee?.year_of ?? new Date().getFullYear(),
    rank: props.trophee?.rank ?? '',
    description: props.trophee?.description ?? '',
});

const ranks = [
    { value: 1, label: '1er' },
    { value: 2, label: '2ème' },
    { value: 3, label: '3ème' },
];

const selectClass =
    'h-auto w-full rounded-none border-2 border-gray-900 bg-white py-2 px-3 text-gray-900 focus-visible:border-[var(--brand)] focus-visible:ring-0 focus:outline-none';

function submit() {
    form.post(props.submitUrl);
}

const hasError = computed(() => Object.keys(form.errors).length > 0);
</script>

<template>
    <form class="max-w-2xl space-y-6" @submit.prevent="submit">
        <div
            v-if="hasError"
            class="border-2 border-red-600 bg-red-50 px-4 py-3 text-sm font-medium text-red-800"
        >
            {{ t('collecte.form.error') }}
        </div>

        <FormSection
            :title="t('admin.vainqueurs.form.section_podium')"
            body-class="sm:grid-cols-2"
        >
            <FormField
                v-model.number="form.year_of"
                type="number"
                :label="t('admin.vainqueurs.form.year_of')"
                :placeholder="t('admin.vainqueurs.form.year_of_placeholder')"
                :error="form.errors.year_of"
            />

            <FormField
                :label="t('admin.vainqueurs.form.rank')"
                :error="form.errors.rank"
            >
                <select v-model.number="form.rank" :class="selectClass">
                    <option value="" disabled>
                        {{ t('admin.vainqueurs.form.rank_placeholder') }}
                    </option>
                    <option v-for="r in ranks" :key="r.value" :value="r.value">
                        {{ r.label }}
                    </option>
                </select>
            </FormField>

            <FormField
                class="sm:col-span-2"
                :label="t('admin.vainqueurs.form.company')"
                :error="form.errors.company_id"
            >
                <select v-model="form.company_id" :class="selectClass">
                    <option value="" disabled>
                        {{ t('admin.vainqueurs.form.company_placeholder') }}
                    </option>
                    <option
                        v-for="company in companies"
                        :key="company.id"
                        :value="company.id"
                    >
                        {{ company.name }}
                    </option>
                </select>
            </FormField>
        </FormSection>

        <FormSection :title="t('admin.vainqueurs.form.section_details')">
            <FormField
                :label="t('admin.vainqueurs.form.description')"
                :error="form.errors.description"
            >
                <textarea
                    v-model="form.description"
                    rows="3"
                    :placeholder="
                        t('admin.vainqueurs.form.description_placeholder')
                    "
                    :class="selectClass"
                />
            </FormField>
        </FormSection>

        <div class="flex items-center justify-end gap-3">
            <Button as-child variant="outline">
                <Link :href="index.url()">
                    {{ t('admin.vainqueurs.form.cancel') }}
                </Link>
            </Button>
            <Button
                type="submit"
                :disabled="form.processing"
                class="border-2 border-gray-900 bg-[var(--brand)] text-white hover:bg-[var(--brand-hover)]"
            >
                {{ submitLabel }}
            </Button>
        </div>
    </form>
</template>
