<template>
  <div class="tooltip-container">
    <div
      class="tooltip-trigger"
      @mouseenter="showTooltip = true"
      @mouseleave="showTooltip = false"
    >
      <slot />
    </div>
    <div
      v-if="showTooltip"
      class="tooltip-content"
    >
      <slot name="content" />
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const showTooltip = ref(false);
</script>

<style scoped>
.tooltip-container {
  position: relative;
  display: inline-block;
}

.tooltip-content {
  position: absolute;
  top: -5px;
  left: 50%;
  transform: translateX(-50%) translateY(-100%);
  background-color: white;
  color: black;
  padding: 0.5rem;
  border-radius: 0.25rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  z-index: 50;
  min-width: 150px;
  text-align: center;
}

.tooltip-content::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: white transparent transparent transparent;
}
</style>
