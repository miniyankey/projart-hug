<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Building2, CalendarDays, Clock, Link2, MapPin } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import FormField from '@/components/admin/FormField.vue';
import FormSection from '@/components/admin/FormSection.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { useCalendarDate, useDateFormatter } from '@/composables/useDates';
import { index } from '@/routes/admin/collectes';

const { t, locale } = useI18n();
const { formatLongDate } = useDateFormatter();

const props = defineProps({
    // Collecte à éditer, ou null en mode création.
    collect: {
        type: Object,
        default: null,
    },
    companies: {
        type: Array,
        default: () => [],
    },
    places: {
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
    company_id: props.collect?.company_id ?? '',
    place_id: props.collect?.place_id ?? '',
    day: props.collect?.day ? props.collect.day.slice(0, 10) : '',
    start_time: props.collect?.start_time
        ? props.collect.start_time.slice(0, 5)
        : '',
    end_time: props.collect?.end_time ? props.collect.end_time.slice(0, 5) : '',
    link_appointment: props.collect?.link_appointment ?? '',
    is_active: props.collect?.is_active ?? true,
    // Champs d'un nouveau lieu, saisis seulement quand place_id === 'new'.
    place: {
        name: '',
        address: '',
        locality: '',
        city: '',
        room: '',
    },
});

const isNewPlace = computed(() => form.place_id === 'new');

const selectClass =
    'h-auto w-full rounded-none border-2 border-gray-900 bg-white py-2 px-3 text-gray-900 focus-visible:border-[var(--brand)] focus-visible:ring-0 focus:outline-none';

const dayValue = useCalendarDate(
    () => form.day,
    (value) => {
        form.day = value;
    },
);

const formattedDay = computed(() => formatLongDate(form.day));

// Entités sélectionnées, pour l'aperçu live à droite.
const selectedCompany = computed(() =>
    props.companies.find((company) => company.id === Number(form.company_id)),
);

const selectedPlace = computed(() =>
    props.places.find((place) => place.id === Number(form.place_id)),
);

const previewPlaceLabel = computed(() => {
    const place = isNewPlace.value ? form.place : selectedPlace.value;

    if (!place?.name) {
        return t('admin.collectes.create.preview_empty');
    }

    return `${place.name}${place.city ? ` - ${place.city}` : ''}`;
});

const timeRange = computed(() => {
    if (!form.start_time && !form.end_time) {
        return '';
    }

    return `${form.start_time}${form.end_time ? ` - ${form.end_time}` : ''}`;
});

const previewColor = computed(() => selectedCompany.value?.color || '#9ca3af');

function submit() {
    form.transform((data) => ({
        ...data,
        place_mode: data.place_id === 'new' ? 'new' : 'existing',
        place_id: data.place_id === 'new' ? null : data.place_id,
    })).post(props.submitUrl);
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

            <FormSection :title="t('admin.collectes.create.section_general')">
                <FormField
                    :label="t('admin.collectes.create.company')"
                    :error="form.errors.company_id"
                >
                    <select v-model="form.company_id" :class="selectClass">
                        <option value="" disabled>
                            {{
                                t('admin.collectes.create.company_placeholder')
                            }}
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

                <FormField
                    :label="t('admin.collectes.create.place')"
                    :error="form.errors.place_id"
                >
                    <select v-model="form.place_id" :class="selectClass">
                        <option value="" disabled>
                            {{ t('admin.collectes.create.place_placeholder') }}
                        </option>
                        <option
                            v-for="place in places"
                            :key="place.id"
                            :value="place.id"
                        >
                            {{ place.name
                            }}{{ place.city ? ` - ${place.city}` : '' }}
                        </option>
                        <option value="new">
                            {{ t('admin.collectes.create.place_new_option') }}
                        </option>
                    </select>
                </FormField>

                <div
                    v-if="isNewPlace"
                    class="space-y-4 border-2 border-dashed border-gray-300 bg-gray-50 p-4"
                >
                    <p
                        class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                    >
                        {{ t('admin.collectes.create.new_place_title') }}
                    </p>
                    <FormField
                        v-model="form.place.name"
                        :label="t('admin.collectes.create.place_name')"
                        :placeholder="
                            t('admin.collectes.create.place_name_placeholder')
                        "
                        :error="form.errors['place.name']"
                    />
                    <FormField
                        v-model="form.place.address"
                        :label="t('admin.collectes.create.place_address')"
                        :placeholder="
                            t(
                                'admin.collectes.create.place_address_placeholder',
                            )
                        "
                        :error="form.errors['place.address']"
                    />
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <FormField
                            v-model="form.place.locality"
                            type="number"
                            :label="t('admin.collectes.create.place_locality')"
                            :placeholder="
                                t(
                                    'admin.collectes.create.place_locality_placeholder',
                                )
                            "
                            :error="form.errors['place.locality']"
                        />
                        <FormField
                            v-model="form.place.city"
                            class="sm:col-span-2"
                            :label="t('admin.collectes.create.place_city')"
                            :placeholder="
                                t(
                                    'admin.collectes.create.place_city_placeholder',
                                )
                            "
                            :error="form.errors['place.city']"
                        />
                    </div>
                    <FormField
                        v-model="form.place.room"
                        :label="t('admin.collectes.create.place_room')"
                        :placeholder="
                            t('admin.collectes.create.place_room_placeholder')
                        "
                        :error="form.errors['place.room']"
                    />
                </div>
            </FormSection>

            <FormSection
                :title="t('admin.collectes.create.section_schedule')"
                body-class="sm:grid-cols-2"
            >
                <FormField
                    class="max-w-xs sm:col-span-2"
                    :label="t('admin.collectes.create.day')"
                    :error="form.errors.day"
                >
                    <Popover>
                        <PopoverTrigger as-child>
                            <button
                                type="button"
                                :class="[
                                    selectClass,
                                    'flex items-center gap-2 text-left',
                                ]"
                            >
                                <CalendarDays class="size-4 shrink-0" />
                                <span :class="{ 'text-gray-400': !form.day }">
                                    {{
                                        form.day
                                            ? formattedDay
                                            : t(
                                                  'admin.collectes.create.day_placeholder',
                                              )
                                    }}
                                </span>
                            </button>
                        </PopoverTrigger>
                        <PopoverContent align="start" class="w-auto p-0">
                            <Calendar
                                v-model="dayValue"
                                :locale="locale"
                                class="border-0 shadow-none"
                            />
                        </PopoverContent>
                    </Popover>
                </FormField>
                <FormField
                    v-model="form.start_time"
                    type="time"
                    :label="t('admin.collectes.create.start_time')"
                    :error="form.errors.start_time"
                />
                <FormField
                    v-model="form.end_time"
                    type="time"
                    :label="t('admin.collectes.create.end_time')"
                    :error="form.errors.end_time"
                />
            </FormSection>

            <FormSection :title="t('admin.collectes.create.section_options')">
                <FormField
                    v-model="form.link_appointment"
                    type="url"
                    :label="t('admin.collectes.create.link_appointment')"
                    :placeholder="
                        t('admin.collectes.create.link_appointment_placeholder')
                    "
                    :error="form.errors.link_appointment"
                />
                <Label
                    for="is_active"
                    class="cursor-pointer items-center gap-3 font-semibold text-gray-900"
                >
                    <Checkbox id="is_active" v-model="form.is_active" />
                    {{ t('admin.collectes.create.is_active') }}
                </Label>
            </FormSection>

            <div class="flex items-center justify-end gap-3">
                <Button as-child variant="outline">
                    <Link :href="index.url()">
                        {{ t('admin.collectes.create.cancel') }}
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
                <p
                    class="mb-3 text-xs font-bold tracking-wide text-gray-500 uppercase"
                >
                    {{ t('admin.collectes.create.preview_title') }}
                </p>

                <div class="overflow-hidden border-2 border-gray-900 bg-white">
                    <!-- En-tête teinté par la couleur de l'entreprise -->
                    <div
                        class="flex items-center gap-3 p-4 text-white"
                        :style="{ backgroundColor: previewColor }"
                    >
                        <span
                            class="flex size-12 shrink-0 items-center justify-center overflow-hidden border-2 border-white/40 bg-white"
                        >
                            <img
                                v-if="selectedCompany?.logo_url"
                                :src="selectedCompany.logo_url"
                                alt=""
                                class="size-full object-contain p-0.5"
                            />
                            <Building2 v-else class="size-6 text-gray-400" />
                        </span>
                        <p
                            class="min-w-0 truncate text-base leading-tight font-bold"
                        >
                            {{
                                selectedCompany?.name ||
                                t('admin.collectes.create.preview_company')
                            }}
                        </p>
                    </div>

                    <!-- Détails de la collecte -->
                    <div class="space-y-3 p-4 text-sm text-gray-700">
                        <p class="flex items-center gap-2">
                            <MapPin class="size-4 shrink-0 text-gray-400" />
                            <span class="truncate">{{
                                previewPlaceLabel
                            }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <CalendarDays
                                class="size-4 shrink-0 text-gray-400"
                            />
                            <span>{{
                                formattedDay ||
                                t('admin.collectes.create.preview_empty')
                            }}</span>
                        </p>
                        <p v-if="timeRange" class="flex items-center gap-2">
                            <Clock class="size-4 shrink-0 text-gray-400" />
                            <span>{{ timeRange }}</span>
                        </p>
                        <p
                            v-if="form.link_appointment"
                            class="flex items-center gap-2"
                        >
                            <Link2 class="size-4 shrink-0 text-gray-400" />
                            <span class="truncate">{{
                                form.link_appointment
                            }}</span>
                        </p>

                        <span
                            v-if="form.is_active"
                            class="inline-flex items-center border-2 border-emerald-500 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700"
                        >
                            {{ t('admin.collectes.status_active') }}
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center border-2 border-gray-300 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-500"
                        >
                            {{ t('admin.collectes.status_inactive') }}
                        </span>
                    </div>
                </div>

                <p class="mt-3 text-xs leading-relaxed text-gray-500">
                    {{ t('admin.collectes.create.preview_hint') }}
                </p>
            </div>
        </aside>
    </div>
</template>
