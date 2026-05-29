import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Card } from "./Card.vue"
export { default as CardContent } from "./CardContent.vue"
export { default as CardHeader } from "./CardHeader.vue"
export { default as CardTitle } from "./CardTitle.vue"

export const cardVariants = cva("flex flex-col gap-6 py-6", {
  variants: {
    variant: {
      default: "bg-card text-card-foreground rounded-xl border shadow-sm",
      //variante pixel like
          pixel:
        "bg-white text-gray-900 rounded-none border-[3px] border-black shadow-[8px_8px_0_0_rgba(0,0,0,0.45)]",
    },
  },
  defaultVariants: {
    variant: "default",
  },
})
export type CardVariants = VariantProps<typeof cardVariants>
