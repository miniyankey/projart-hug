<script setup>
import { Clock } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';

const { t } = useI18n();

const props = defineProps({
    placeholder: {
        type: String,
        default: '--:--',
    },
    // uniquement par tranches de 15m
    minuteStep: {
        type: Number,
        default: 15,
    },
});

const model = defineModel({ default: '' });

const hours = Array.from({ length: 24 }, (_, h) => String(h).padStart(2, '0'));
const minutes = computed(() =>
    Array.from({ length: Math.ceil(60 / props.minuteStep) }, (_, i) =>
        String(i * props.minuteStep).padStart(2, '0'),
    ),
);

const selectedHour = computed(() => model.value.split(':')[0] ?? '');
const selectedMinute = computed(() => model.value.split(':')[1] ?? '');

function selectHour(hour) {
    model.value = `${hour}:${selectedMinute.value || '00'}`;
}

function selectMinute(minute) {
    model.value = `${selectedHour.value || '00'}:${minute}`;
}

// Cellule façon calendrier : carré, bordure au survol, fond de marque si sélectionné.
function cellClass(active) {
    return [
        'flex h-9 w-12 cursor-pointer items-center justify-center border-2 text-sm transition-colors',
        active
            ? 'border-black bg-[var(--brand)] font-bold text-white'
            : 'border-transparent hover:border-black',
    ];
}
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <button
                type="button"
                class="flex h-auto w-full items-center gap-2 rounded-none border-2 border-gray-900 bg-white px-3 py-2 text-left text-gray-900 focus:outline-none focus-visible:border-[var(--brand)]"
            >
                <Clock class="size-4 shrink-0" />
                <span :class="{ 'text-gray-400': !model }">
                    {{ model || placeholder }}
                </span>
            </button>
        </PopoverTrigger>
        <PopoverContent align="start" class="p-3">
            <div class="flex gap-3 font-mono">
                <div class="flex flex-col">
                    <p
                        class="mb-2 text-center text-xs font-bold tracking-wide text-[#2D1B4E] uppercase"
                    >
                        {{ t('ui.time_picker.hours') }}
                    </p>
                    <div
                        class="flex max-h-56 flex-col gap-1 overflow-y-auto pr-1"
                    >
                        <button
                            v-for="hour in hours"
                            :key="hour"
                            type="button"
                            :class="cellClass(hour === selectedHour)"
                            @click="selectHour(hour)"
                        >
                            {{ hour }}
                        </button>
                    </div>
                </div>

                <div class="flex flex-col">
                    <p
                        class="mb-2 text-center text-xs font-bold tracking-wide text-[#2D1B4E] uppercase"
                    >
                        {{ t('ui.time_picker.minutes') }}
                    </p>
                    <div
                        class="flex max-h-56 flex-col gap-1 overflow-y-auto pr-1"
                    >
                        <button
                            v-for="minute in minutes"
                            :key="minute"
                            type="button"
                            :class="cellClass(minute === selectedMinute)"
                            @click="selectMinute(minute)"
                        >
                            {{ minute }}
                        </button>
                    </div>
                </div>
            </div>
        </PopoverContent>
    </Popover>
</template>
