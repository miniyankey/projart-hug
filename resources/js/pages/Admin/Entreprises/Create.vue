<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Upload, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import CompanyBrandPreview from '@/components/admin/CompanyBrandPreview.vue';
import FormField from '@/components/admin/FormField.vue';
import FormSection from '@/components/admin/FormSection.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index, store } from '@/routes/admin/entreprises';

const { t } = useI18n();

const form = useForm({
    name: '',
    email_contact: '',
    contact_name: '',
    contact_phone: '',
    street: '',
    postal_code: '',
    city: '',
    country: 'CH',
    color: '#8b2cf1',
    color_secondary: '',
    logo: null,
    is_labelled: false,
    labelled_at: '',
});

const logoPreview = ref(null);
const logoInput = ref(null);

function onLogoChange(event) {
    const file = event.target.files?.[0] ?? null;
    form.logo = file;
    logoPreview.value = file ? URL.createObjectURL(file) : null;
}

function removeLogo() {
    form.logo = null;
    logoPreview.value = null;

    if (logoInput.value) {
        logoInput.value.value = '';
    }
}

function submit() {
    form.transform((data) => ({
        ...data,
        labelled_at: data.is_labelled ? data.labelled_at : '',
    })).post(store.url(), { forceFormData: true });
}

const hasError = computed(() => Object.keys(form.errors).length > 0);
</script>

<template>
    <AdminLayout :title="t('admin.entreprises.create.title')">
        <template #subtitle>{{
            t('admin.entreprises.create.subtitle')
        }}</template>

        <template #actions>
            <Button as-child variant="outline">
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.entreprises.create.cancel') }}
                </Link>
            </Button>
        </template>

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
                            t(
                                'admin.entreprises.create.contact_name_placeholder',
                            )
                        "
                        :error="form.errors.contact_name"
                    />
                    <FormField
                        v-model="form.contact_phone"
                        :label="t('admin.entreprises.create.contact_phone')"
                        :placeholder="
                            t(
                                'admin.entreprises.create.contact_phone_placeholder',
                            )
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
                            t(
                                'admin.entreprises.create.postal_code_placeholder',
                            )
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
                            <input
                                v-model="form.color"
                                type="text"
                                class="w-full rounded-none border-2 border-gray-900 bg-white px-3 py-2 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:border-[var(--brand)] dark:bg-white dark:text-gray-900"
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
                                @input="
                                    form.color_secondary = $event.target.value
                                "
                            />
                            <input
                                v-model="form.color_secondary"
                                type="text"
                                class="w-full rounded-none border-2 border-gray-900 bg-white px-3 py-2 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:border-[var(--brand)] dark:bg-white dark:text-gray-900"
                                placeholder="#facc15"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            {{
                                t(
                                    'admin.entreprises.create.color_secondary_hint',
                                )
                            }}
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
                                        v-if="logoPreview"
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
                                    {{
                                        t('admin.entreprises.create.logo_hint')
                                    }}
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

                <FormSection
                    :title="t('admin.entreprises.create.section_label')"
                >
                    <Label
                        class="flex cursor-pointer items-center gap-3 font-semibold text-gray-900"
                    >
                        <input
                            v-model="form.is_labelled"
                            type="checkbox"
                            class="size-5 cursor-pointer accent-[var(--brand)]"
                        />
                        {{ t('admin.entreprises.create.is_labelled') }}
                    </Label>
                    <FormField
                        v-if="form.is_labelled"
                        v-model="form.labelled_at"
                        class="max-w-xs"
                        type="date"
                        :label="t('admin.entreprises.create.labelled_at')"
                        :error="form.errors.labelled_at"
                    />
                </FormSection>

                <p
                    class="border-l-4 border-[var(--brand)] bg-[var(--brand-tint)] px-4 py-3 text-sm text-gray-700"
                >
                    {{ t('admin.entreprises.create.link_note') }}
                </p>

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
                        {{ t('admin.entreprises.create.submit') }}
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
    </AdminLayout>
</template>
