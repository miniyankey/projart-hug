<script setup lang="ts">
import type { DateValue } from "@internationalized/date"
import type { HTMLAttributes } from "vue"
import { ChevronLeft, ChevronRight } from "lucide-vue-next"
import {
  CalendarCell,
  CalendarCellTrigger,
  CalendarGrid,
  CalendarGridBody,
  CalendarGridHead,
  CalendarGridRow,
  CalendarHeadCell,
  CalendarHeader,
  CalendarHeading,
  CalendarNext,
  CalendarPrev,
  CalendarRoot,
} from "reka-ui"
import { cn } from "@/lib/utils"

const modelValue = defineModel<DateValue | DateValue[] | undefined>()
const props = defineProps<{ class?: HTMLAttributes["class"] }>()

const navButton =
  "flex size-8 items-center justify-center border-2 border-black bg-white text-[#2D1B4E] shadow-[2px_2px_0_0_#000] transition-transform hover:bg-gray-100 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none"
</script>

<template>
  <CalendarRoot
    v-slot="{ weekDays, grid }"
    v-model="modelValue"
    :class="cn(
      'w-fit border-2 border-black bg-white p-3 font-mono text-black shadow-[4px_4px_0_0_#000]',
      props.class,
    )"
  >
    <CalendarHeader class="flex items-center justify-between">
      <CalendarPrev :class="navButton">
        <ChevronLeft class="size-4 stroke-[3]" />
      </CalendarPrev>
      <CalendarHeading class="text-sm font-bold uppercase tracking-wide text-[#2D1B4E]" />
      <CalendarNext :class="navButton">
        <ChevronRight class="size-4 stroke-[3]" />
      </CalendarNext>
    </CalendarHeader>

    <div class="mt-3 flex flex-col gap-4">
      <CalendarGrid
        v-for="month in grid"
        :key="month.value.toString()"
        class="w-full border-collapse select-none"
      >
        <CalendarGridHead>
          <CalendarGridRow class="flex">
            <CalendarHeadCell
              v-for="day in weekDays"
              :key="day"
              class="flex size-9 items-center justify-center text-xs font-bold uppercase text-gray-500"
            >
              {{ day }}
            </CalendarHeadCell>
          </CalendarGridRow>
        </CalendarGridHead>
        <CalendarGridBody class="flex flex-col gap-1 pt-1">
          <CalendarGridRow
            v-for="(weekDates, index) in month.rows"
            :key="`week-${index}`"
            class="flex"
          >
            <CalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
              class="p-0 text-center"
            >
              <CalendarCellTrigger
                :day="weekDate"
                :month="month.value"
                class="flex size-9 cursor-pointer items-center justify-center border-2 border-transparent text-sm transition-colors hover:border-black data-[disabled]:pointer-events-none data-[disabled]:text-gray-300 data-[outside-view]:text-gray-300 data-[selected]:border-black data-[selected]:bg-[#5B21B6] data-[selected]:font-bold data-[selected]:text-white data-[today]:border-[#5B21B6] data-[unavailable]:pointer-events-none data-[unavailable]:text-gray-300 data-[unavailable]:line-through"
              />
            </CalendarCell>
          </CalendarGridRow>
        </CalendarGridBody>
      </CalendarGrid>
    </div>
  </CalendarRoot>
</template>
