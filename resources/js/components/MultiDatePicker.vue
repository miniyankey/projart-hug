<script setup>
import { getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { CalendarDays, X } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:modelValue']);

const { t, locale } = useI18n();

// Le back-end exige des dates strictement après aujourd'hui.
const minValue = today(getLocalTimeZone()).add({ days: 1 });

// Pont entre les chaînes 'YYYY-MM-DD' (form / back-end) et les DateValue du calendrier.
const calendarValue = computed({
    get: () => props.modelValue.map((date) => parseDate(date)),
    set: (values) =>
        emit(
            'update:modelValue',
            (values ?? []).map((date) => date.toString()),
        ),
});

const sortedDates = computed(() => [...props.modelValue].sort());

function removeDate(value) {
    emit(
        'update:modelValue',
        props.modelValue.filter((date) => date !== value),
    );
}

function formatDate(value) {
    return parseDate(value)
        .toDate(getLocalTimeZone())
        .toLocaleDateString(locale.value, {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        });
}
</script>

<template>
    <div class="flex flex-col gap-3">
        <Popover>
            <PopoverTrigger as-child>
                <button
                    type="button"
                    class="flex h-11 w-full items-center gap-2 border-2 border-black bg-white px-3 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] outline-none transition-transform hover:bg-gray-50 focus-visible:ring-2 focus-visible:ring-[#5B21B6] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0_0_#000]"
                >
                    <CalendarDays class="size-4 shrink-0" />
                    <span :class="{ 'text-gray-400': !modelValue.length }">
                        {{
                            modelValue.length
                                ? t('collecte.form.dates_selected', modelValue.length)
                                : t('collecte.form.dates_placeholder')
                        }}
                    </span>
                </button>
            </PopoverTrigger>
            <PopoverContent align="start" class="p-3">
                <Calendar
                    v-model="calendarValue"
                    multiple
                    :min-value="minValue"
                    :locale="locale"
                    class="border-0 shadow-none"
                />
            </PopoverContent>
        </Popover>

        <ul v-if="sortedDates.length" class="flex flex-wrap gap-2">
            <li
                v-for="date in sortedDates"
                :key="date"
                class="flex items-center gap-2 border-2 border-black bg-white px-2 py-1 font-mono text-xs text-[#2D1B4E] shadow-[2px_2px_0_0_#000]"
            >
                {{ formatDate(date) }}
                <button
                    type="button"
                    class="text-[#2D1B4E] hover:text-red-700"
                    :aria-label="t('collecte.form.remove_date')"
                    @click="removeDate(date)"
                >
                    <X class="size-3.5 stroke-[3]" />
                </button>
            </li>
        </ul>
    </div>
</template>
