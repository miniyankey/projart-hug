<script setup>
import { getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { CalendarDays } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { useDateFormatter } from '@/composables/useDates';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const { t, locale } = useI18n();
const { formatLongDate } = useDateFormatter();

// Le back-end exige des dates strictement après aujourd'hui.
const minValue = today(getLocalTimeZone()).add({ days: 1 });

// Pont entre la chaîne 'YYYY-MM-DD' (form / back-end) et le DateValue du calendrier.
const calendarValue = computed({
    get: () => (props.modelValue ? parseDate(props.modelValue) : undefined),
    set: (value) => emit('update:modelValue', value ? value.toString() : ''),
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <button
                type="button"
                class="flex h-11 w-full items-center gap-2 border-2 border-black bg-white px-3 font-mono text-sm text-black shadow-[4px_4px_0_0_#000] transition-transform outline-none hover:bg-gray-50 focus-visible:ring-2 focus-visible:ring-[var(--brand)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0_0_#000]"
            >
                <CalendarDays class="size-4 shrink-0" />
                <span :class="{ 'text-gray-400': !modelValue }">
                    {{
                        modelValue
                            ? formatLongDate(modelValue)
                            : placeholder || t('ui.date_picker.placeholder')
                    }}
                </span>
            </button>
        </PopoverTrigger>
        <PopoverContent align="start" class="p-3">
            <Calendar
                v-model="calendarValue"
                :min-value="minValue"
                :locale="locale"
                class="border-0 shadow-none"
            />
        </PopoverContent>
    </Popover>
    <p v-if="error" class="mt-1 font-mono text-xs text-red-700">
        {{ error }}
    </p>
</template>
