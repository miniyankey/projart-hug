<script setup lang="ts">
import type { CheckedState } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { Check } from "lucide-vue-next"
import { CheckboxIndicator, CheckboxRoot } from "reka-ui"
import { cn } from "@/lib/utils"

const modelValue = defineModel<CheckedState>()
const props = defineProps<{ class?: HTMLAttributes["class"] }>()
</script>

<template>
  <CheckboxRoot
    v-model="modelValue"
    data-slot="checkbox"
    :class="cn(
      'peer flex size-7 shrink-0 items-center justify-center border-2 border-black bg-white shadow-[3px_3px_0_0_#000] outline-none transition-transform',
      'focus-visible:ring-2 focus-visible:ring-[var(--brand)] disabled:cursor-not-allowed disabled:opacity-50',
      'data-[state=checked]:bg-white active:translate-x-0.5 active:translate-y-0.5 active:shadow-[1px_1px_0_0_#000]',
      props.class,
    )"
  >
    <CheckboxIndicator
      data-slot="checkbox-indicator"
      class="flex items-center justify-center text-black"
    >
      <Check class="size-5 stroke-[3]" />
    </CheckboxIndicator>
  </CheckboxRoot>
</template>
