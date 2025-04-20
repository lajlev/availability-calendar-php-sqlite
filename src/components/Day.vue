<template>
  <Tooltip v-if="date">
    <div
      :class="['calendar-day', statusClass, { past: isPast, admin: isAdmin }]"
      @click="
        isAdmin && !isPast ? (showStatusOptions = !showStatusOptions) : null
      "
    >
      <span class="text-sm">{{ date.getDate() }}</span>

      <!-- Status options dropdown for admin -->
      <div
        v-if="showStatusOptions && isAdmin && !isPast"
        class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-10 w-32"
      >
        <button
          v-for="option in statusOptions"
          :key="option.value"
          @click.stop="updateStatus(option.value)"
          class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
        >
          {{ option.label }}
        </button>
      </div>
    </div>

    <template #content>
      <div class="flex flex-col">
        <span class="text-sm">{{ statusLabel }}</span>
      </div>
    </template>
  </Tooltip>
  <div
    v-else
    class="calendar-day opacity-0 pointer-events-none"
  ></div>
</template>

<script setup>
import { ref, computed } from "vue";
import { isPastDate, getStatusClass, formatDate } from "../lib/utils";
import Tooltip from "./ui/Tooltip.vue";

const props = defineProps({
  date: {
    type: Date,
    default: null,
  },
  status: {
    type: String,
    default: "available",
  },
  isAdmin: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update"]);

const showStatusOptions = ref(false);

const isPast = computed(() => {
  if (!props.date) return false;
  return isPastDate(props.date);
});

const statusClass = computed(() => {
  return getStatusClass(props.status);
});

const statusLabel = computed(() => {
  switch (props.status) {
    case "available":
      return "Ledig";
    case "booked":
      return "Optaget";
    case "half-booked":
      return "Skifte dag";
    default:
      return "";
  }
});

const statusOptions = [
  { value: "available", label: "Ledig" },
  { value: "booked", label: "Optaget" },
  { value: "half-booked", label: "Skifte dag" },
];

function updateStatus(status) {
  emit("update", props.date, status);
  showStatusOptions.value = false;
}
</script>
