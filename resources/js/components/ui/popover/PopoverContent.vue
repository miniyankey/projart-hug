<script setup lang="ts">
import type { PopoverContentProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { PopoverContent, PopoverPortal } from "reka-ui"
import { cn } from "@/lib/utils"

const {
  align = "center",
  sideOffset = 6,
  class: className,
} = defineProps<{
  align?: PopoverContentProps["align"]
  sideOffset?: number
  class?: HTMLAttributes["class"]
}>()
</script>

<template>
  <PopoverPortal>
    <PopoverContent
      data-slot="popover-content"
      :align="align"
      :side-offset="sideOffset"
      :class="cn(
        'z-50 w-auto border-2 border-black bg-white p-0 text-black shadow-[6px_6px_0_0_#000] outline-none',
        'data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95',
        className,
      )"
    >
      <slot />
    </PopoverContent>
  </PopoverPortal>
</template>
