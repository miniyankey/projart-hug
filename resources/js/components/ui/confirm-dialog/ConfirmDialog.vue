<script setup lang="ts">
import {
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogOverlay,
    AlertDialogPortal,
    AlertDialogRoot,
    AlertDialogTitle,
} from 'reka-ui';
import { AlertTriangle } from 'lucide-vue-next';

const {
    open = false,
    title,
    description = '',
    confirmLabel,
    cancelLabel,
    destructive = false,
    processing = false,
} = defineProps<{
    open?: boolean;
    title: string;
    description?: string;
    confirmLabel: string;
    cancelLabel: string;
    destructive?: boolean;
    processing?: boolean;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
    confirm: [];
}>();

function close() {
    emit('update:open', false);
}
</script>

<template>
    <AlertDialogRoot
        :open="open"
        @update:open="emit('update:open', $event)"
    >
        <AlertDialogPortal>
            <AlertDialogOverlay
                class="fixed inset-0 z-50 bg-black/50 data-[state=closed]:animate-out data-[state=open]:animate-in data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
            />
            <AlertDialogContent
                class="fixed top-1/2 left-1/2 z-50 grid w-[calc(100%-2rem)] max-w-md -translate-x-1/2 -translate-y-1/2 gap-4 border-2 border-black bg-white p-6 text-black shadow-[8px_8px_0_0_#000] outline-none data-[state=closed]:animate-out data-[state=open]:animate-in data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95"
            >
                <div class="flex items-start gap-3">
                    <div
                        v-if="destructive"
                        class="flex size-10 shrink-0 items-center justify-center border-2 border-gray-900 bg-red-50 text-red-600"
                    >
                        <AlertTriangle class="size-5" />
                    </div>
                    <div class="space-y-1.5">
                        <AlertDialogTitle
                            class="font-bold text-gray-900"
                        >
                            {{ title }}
                        </AlertDialogTitle>
                        <AlertDialogDescription
                            v-if="description"
                            class="text-sm leading-relaxed text-gray-600"
                        >
                            {{ description }}
                        </AlertDialogDescription>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        :disabled="processing"
                        class="inline-flex h-9 items-center justify-center border-2 border-gray-900 bg-white px-4 text-sm font-semibold text-gray-900 transition-colors outline-none hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-50"
                        @click="close"
                    >
                        {{ cancelLabel }}
                    </button>
                    <button
                        type="button"
                        :disabled="processing"
                        :class="[
                            'inline-flex h-9 items-center justify-center rounded-none border-2 border-gray-900 px-4 text-sm font-semibold text-white transition-all duration-150 outline-none disabled:pointer-events-none disabled:opacity-50',
                            destructive
                                ? 'bg-red-600 shadow-[4px_4px_0px_0px_#7f1d1d] hover:bg-red-700 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#7f1d1d] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none'
                                : 'bg-[var(--brand)] shadow-[4px_4px_0px_0px_var(--brand-shadow)] hover:bg-[var(--brand-hover)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_var(--brand-shadow)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none',
                        ]"
                        @click="emit('confirm')"
                    >
                        {{ confirmLabel }}
                    </button>
                </div>
            </AlertDialogContent>
        </AlertDialogPortal>
    </AlertDialogRoot>
</template>
