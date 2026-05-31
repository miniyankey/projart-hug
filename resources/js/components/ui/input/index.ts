import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Input } from "./Input.vue"

export const inputVariants = cva(
  "file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 h-9 w-full min-w-0 px-3 py-1 text-base transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm aria-invalid:border-destructive",
  {
    variants: {
      variant: {
        default:
          "border-input rounded-md border bg-transparent shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40",
          //variante pixel like
        pixel:
          "rounded-none border-2 border-black bg-white shadow-[2px_2px_0_0_rgba(0,0,0,0.25)] focus-visible:border-violet-600 focus-visible:ring-0",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
)
export type InputVariants = VariantProps<typeof inputVariants>
