<template>
  <RadixTooltipTrigger
    v-bind="$attrs"
    :as-child="asChild"
  >
    <slot />
  </RadixTooltipTrigger>
</template>

<script setup>
import { TooltipTrigger as RadixTooltipTrigger } from "radix-vue";

defineProps({
  asChild: {
    type: Boolean,
    default: false,
  },
});
</script>
