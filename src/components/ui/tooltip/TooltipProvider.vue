<template>
  <RadixTooltipProvider
    :delay-duration="delayDuration"
    :skip-delay-duration="skipDelayDuration"
    :disable-hoverable-content="disableHoverableContent"
  >
    <slot />
  </RadixTooltipProvider>
</template>

<script setup>
import { TooltipProvider as RadixTooltipProvider } from "radix-vue";

defineProps({
  delayDuration: {
    type: Number,
    default: 300,
  },
  skipDelayDuration: {
    type: Number,
    default: 300,
  },
  disableHoverableContent: {
    type: Boolean,
    default: false,
  },
});
</script>
