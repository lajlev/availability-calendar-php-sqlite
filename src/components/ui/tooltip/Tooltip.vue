<template>
  <RadixTooltip
    :default-open="defaultOpen"
    :delay-duration="delayDuration"
    :open="open"
    :onOpenChange="onOpenChange"
  >
    <slot />
  </RadixTooltip>
</template>

<script setup>
import { Tooltip as RadixTooltip } from "radix-vue";

defineProps({
  defaultOpen: {
    type: Boolean,
    default: false,
  },
  delayDuration: {
    type: Number,
    default: 300,
  },
  open: {
    type: Boolean,
    default: undefined,
  },
  onOpenChange: {
    type: Function,
    default: undefined,
  },
});
</script>
