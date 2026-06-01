<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import type { InputVariants } from "."
import { cn } from "@/lib/utils"
import { inputVariants } from "."

const props = defineProps<{
  variant?: InputVariants["variant"]
  class?: HTMLAttributes["class"]
}>()

const modelValue = defineModel()
</script>

<template>
  <input
    v-model="modelValue"
    data-slot="input"
    :data-variant="variant"
    :class="cn(inputVariants({ variant }), props.class)"
  >
</template>
