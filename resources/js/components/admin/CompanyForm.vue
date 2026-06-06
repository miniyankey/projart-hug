<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Upload, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import CompanyBrandPreview from '@/components/admin/CompanyBrandPreview.vue';
import FormField from '@/components/admin/FormField.vue';
import FormSection from '@/components/admin/FormSection.vue';
import SingleDatePicker from '@/components/forms/SingleDatePicker.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/admin/entreprises';

const { t } = useI18n();

const props = defineProps({
    // Entreprise à éditer, ou null en mode création.
    company: {
        type: Object,
        default: null,
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
    name: props.company?.name ?? '',
    email_contact: props.company?.email_contact ?? '',
    contact_name: props.company?.contact_name ?? '',
    contact_phone: props.company?.contact_phone ?? '',
    street: props.company?.street ?? '',
    postal_code: props.company?.postal_code ?? '',
    city: props.company?.city ?? '',
    country: props.company?.country ?? 'CH',
    color: props.company?.color ?? '#8b2cf1',
    color_secondary: props.company?.color_secondary ?? '',
    logo: null,
    is_labelled: props.company?.is_labelled ?? false,
    labelled_at: props.company?.labelled_at
        ? props.company.labelled_at.slice(0, 10)
        : '',
});

const logoPreview = ref(props.company?.logo_url ?? null);
const logoInput = ref(null);

function onLogoChange(event) {
    const file = event.target.files?.[0] ?? null;
    form.logo = file;
    logoPreview.value = file
        ? URL.createObjectURL(file)
        : (props.company?.logo_url ?? null);
}

function removeLogo() {
    form.logo = null;
    logoPreview.value = props.company?.logo_url ?? null;

    if (logoInput.value) {
        logoInput.value.value = '';
    }
}

function submit() {
    form.transform((data) => ({
        ...data,
        labelled_at: data.is_labelled ? data.labelled_at : '',
    })).post(props.submitUrl, { forceFormData: true });
}

const hasError = computed(() => Object.keys(form.errors).length > 0);
</script>

<template>
    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1fr)_340px]">
        <form class="space-y-6" @submit.prevent="submit">
            <div
                v-if="hasError"
                class="border-2 border-red-600 bg-red-50 px-4 py-3 text-sm font-medium text-red-800"
            >
                {{ t('collecte.form.error') }}
            </div>

            <FormSection
                :title="t('admin.entreprises.create.section_identity')"
            >
                <FormField
                    v-model="form.name"
                    :label="t('admin.entreprises.create.name')"
                    :placeholder="
                        t('admin.entreprises.create.name_placeholder')
                    "
                    :error="form.errors.name"
                />
            </FormSection>

            <FormSection
                :title="t('admin.entreprises.create.section_contact')"
                body-class="sm:grid-cols-2"
            >
                <FormField
                    v-model="form.contact_name"
                    :label="t('admin.entreprises.create.contact_name')"
                    :placeholder="
                        t('admin.entreprises.create.contact_name_placeholder')
                    "
                    :error="form.errors.contact_name"
                />
                <FormField
                    v-model="form.contact_phone"
                    :label="t('admin.entreprises.create.contact_phone')"
                    :placeholder="
                        t('admin.entreprises.create.contact_phone_placeholder')
                    "
                    :error="form.errors.contact_phone"
                />
                <FormField
                    v-model="form.email_contact"
                    class="sm:col-span-2"
                    type="email"
                    :label="t('admin.entreprises.create.email')"
                    :placeholder="
                        t('admin.entreprises.create.email_placeholder')
                    "
                    :error="form.errors.email_contact"
                />
            </FormSection>

            <FormSection
                :title="t('admin.entreprises.create.section_address')"
                body-class="sm:grid-cols-6"
            >
                <FormField
                    v-model="form.street"
                    class="sm:col-span-6"
                    :label="t('admin.entreprises.create.street')"
                    :placeholder="
                        t('admin.entreprises.create.street_placeholder')
                    "
                    :error="form.errors.street"
                />
                <FormField
                    v-model="form.postal_code"
                    class="sm:col-span-2"
                    :label="t('admin.entreprises.create.postal_code')"
                    :placeholder="
                        t('admin.entreprises.create.postal_code_placeholder')
                    "
                    :error="form.errors.postal_code"
                />
                <FormField
                    v-model="form.city"
                    class="sm:col-span-3"
                    :label="t('admin.entreprises.create.city')"
                    :placeholder="
                        t('admin.entreprises.create.city_placeholder')
                    "
                    :error="form.errors.city"
                />
                <FormField
                    v-model="form.country"
                    class="sm:col-span-1"
                    :label="t('admin.entreprises.create.country')"
                    :maxlength="2"
                    :error="form.errors.country"
                />
            </FormSection>

            <FormSection
                :title="t('admin.entreprises.create.section_branding')"
                body-class="sm:grid-cols-2"
            >
                <FormField
                    v-model="form.color"
                    :label="t('admin.entreprises.create.color')"
                    :error="form.errors.color"
                >
                    <div class="flex items-center gap-3">
                        <input
                            v-model="form.color"
                            type="color"
                            class="size-10 cursor-pointer border-2 border-gray-900 bg-white p-0.5"
                        />
                        <Input
                            v-model="form.color"
                            variant="pixel"
                            type="text"
                            class="h-auto py-2 text-gray-900"
                            placeholder="#8b2cf1"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ t('admin.entreprises.create.color_hint') }}
                    </p>
                </FormField>

                <FormField
                    v-model="form.color_secondary"
                    :label="t('admin.entreprises.create.color_secondary')"
                    :error="form.errors.color_secondary"
                >
                    <div class="flex items-center gap-3">
                        <input
                            :value="form.color_secondary || '#ffffff'"
                            type="color"
                            class="size-10 cursor-pointer border-2 border-gray-900 bg-white p-0.5"
                            @input="form.color_secondary = $event.target.value"
                        />
                        <Input
                            v-model="form.color_secondary"
                            variant="pixel"
                            type="text"
                            class="h-auto py-2 text-gray-900"
                            placeholder="#facc15"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ t('admin.entreprises.create.color_secondary_hint') }}
                    </p>
                </FormField>

                <FormField
                    class="sm:col-span-2"
                    :label="t('admin.entreprises.create.logo')"
                    :error="form.errors.logo"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex size-20 shrink-0 items-center justify-center border-2 border-gray-900 bg-gray-50"
                        >
                            <img
                                v-if="logoPreview"
                                :src="logoPreview"
                                alt=""
                                class="size-full object-contain p-1"
                            />
                            <Upload v-else class="size-6 text-gray-400" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="logoInput?.click()"
                                >
                                    <Upload class="size-4" />
                                    {{
                                        t(
                                            'admin.entreprises.create.logo_choose',
                                        )
                                    }}
                                </Button>
                                <button
                                    v-if="form.logo"
                                    type="button"
                                    class="flex items-center gap-1 px-2 py-1.5 text-sm font-medium text-gray-500 transition-colors hover:text-red-600"
                                    @click="removeLogo"
                                >
                                    <X class="size-4" />
                                    {{
                                        t(
                                            'admin.entreprises.create.logo_remove',
                                        )
                                    }}
                                </button>
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ t('admin.entreprises.create.logo_hint') }}
                            </p>
                        </div>
                        <input
                            ref="logoInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="onLogoChange"
                        />
                    </div>
                </FormField>
            </FormSection>

            <FormSection :title="t('admin.entreprises.create.section_label')">
                <Label
                    for="is_labelled"
                    class="cursor-pointer items-center gap-3 font-semibold text-gray-900"
                >
                    <Checkbox id="is_labelled" v-model="form.is_labelled" />
                    {{ t('admin.entreprises.create.is_labelled') }}
                </Label>
                <SingleDatePicker
                    v-if="form.is_labelled"
                    v-model="form.labelled_at"
                    class="max-w-xs"
                    :error="form.errors.labelled_at"
                />
            </FormSection>

            <div class="flex items-center justify-end gap-3">
                <Button as-child variant="outline">
                    <Link :href="index.url()">
                        {{ t('admin.entreprises.create.cancel') }}
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

        <aside class="hidden xl:block">
            <div class="sticky top-8">
                <CompanyBrandPreview
                    :name="form.name"
                    :color="form.color"
                    :color-secondary="form.color_secondary"
                    :logo-url="logoPreview"
                    :contact-name="form.contact_name"
                    :contact-phone="form.contact_phone"
                    :email="form.email_contact"
                    :city="form.city"
                />
            </div>
        </aside>
    </div>
</template>
