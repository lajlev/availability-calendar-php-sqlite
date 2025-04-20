<template>
  <RadixTooltipPortal>
    <RadixTooltipContent
      v-bind="$attrs"
      :class="
        cn(
          'z-50 overflow-hidden rounded-md border bg-popover px-3 py-1.5 text-sm text-popover-foreground shadow-md animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
          className
        )
      "
      :side-offset="sideOffset"
      :align="align"
      :side="side"
      :align-offset="alignOffset"
      :avoid-collisions="avoidCollisions"
      :collision-boundary="collisionBoundary"
      :collision-padding="collisionPadding"
      :arrow-padding="arrowPadding"
      :sticky="sticky"
      :hide-when-detached="hideWhenDetached"
    >
      <slot />
    </RadixTooltipContent>
  </RadixTooltipPortal>
</template>

<script setup>
import {
  TooltipContent as RadixTooltipContent,
  TooltipPortal as RadixTooltipPortal,
} from "radix-vue";
import { cn } from "@/lib/utils";

const props = defineProps({
  className: {
    type: String,
    default: "",
  },
  sideOffset: {
    type: Number,
    default: 4,
  },
  align: {
    type: String,
    default: "center",
  },
  side: {
    type: String,
    default: "top",
  },
  alignOffset: {
    type: Number,
    default: 0,
  },
  avoidCollisions: {
    type: Boolean,
    default: true,
  },
  collisionBoundary: {
    type: [Element, Array, null],
    default: null,
  },
  collisionPadding: {
    type: [Number, Object],
    default: 8,
  },
  arrowPadding: {
    type: Number,
    default: 0,
  },
  sticky: {
    type: String,
    default: "partial",
  },
  hideWhenDetached: {
    type: Boolean,
    default: false,
  },
});
</script>
