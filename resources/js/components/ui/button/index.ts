import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Button } from "./Button.vue"

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all duration-150 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
  {
    variants: {
      variant: {
        default:
          "bg-primary text-primary-foreground hover:bg-primary/90",
        destructive:
          "bg-destructive text-white hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60",
        outline:
          "border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50",
        secondary:
          "bg-secondary text-secondary-foreground hover:bg-secondary/80",
        ghost:
          "hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50",
        link: "text-primary underline-offset-4 hover:underline",
        pixel_violet:
          "bg-[var(--brand)] text-white rounded-none shadow-[4px_4px_0px_0px_var(--brand-shadow)] hover:bg-[var(--brand-hover)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_var(--brand-shadow)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none",
        pixel_blue:
          "bg-blue-600 text-white rounded-none shadow-[4px_4px_0px_0px_#3b82f6] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#3b82f6] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none",
        pixel_white:
          "bg-white text-[var(--brand)] rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,0.35)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,0.35)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none",
        pixel_yellow:
          "bg-yellow-300 text-gray-900 rounded-none shadow-[4px_4px_0px_0px_#a16207] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#a16207] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none",
        cta:
          "rounded-none bg-[var(--brand)] text-white shadow-[4px_4px_0_0_var(--brand-shadow)] hover:bg-[var(--brand-hover)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0_0_var(--brand-shadow)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none",
      },
      size: {
        "default": "h-9 px-4 py-2 has-[>svg]:px-3",
        "sm": "h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5",
        "lg": "h-10 rounded-md px-6 has-[>svg]:px-4",
        "cta": "h-11 px-6 py-2.5",
        "icon": "size-9",
        "icon-sm": "size-8",
        "icon-lg": "size-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
)
export type ButtonVariants = VariantProps<typeof buttonVariants>
