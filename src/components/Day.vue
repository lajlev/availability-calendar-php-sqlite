<template>
  <div
    v-if="date"
    ref="dayElement"
    :class="['calendar-day', statusClass, { past: isPast, admin: isAdmin }]"
    @click="handleDayClick"
    :title="statusLabel"
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
        :class="[
          'block w-full text-left px-4 py-2 text-sm hover:bg-gray-100',
          option.value === 'available'
            ? 'text-green-600'
            : option.value === 'booked'
            ? 'text-red-600'
            : option.value === 'half-booked'
            ? 'text-amber-600'
            : '',
        ]"
      >
        {{ option.label }}
      </button>
    </div>
  </div>
  <div
    v-else
    class="calendar-day opacity-0 pointer-events-none"
  ></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { isPastDate, getStatusClass, formatDate } from "../lib/utils";

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

// Create a static variable to track the currently open dropdown
const dayElement = ref(null);

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  // If the dropdown is open and the click is outside the day element
  if (
    showStatusOptions.value &&
    dayElement.value &&
    !dayElement.value.contains(event.target)
  ) {
    showStatusOptions.value = false;
  }
};

// Close all other dropdowns when this one is opened
const handleDayClick = (event) => {
  // Stop propagation to prevent the document click handler from immediately closing the dropdown
  event.stopPropagation();

  if (props.isAdmin && !isPast.value) {
    // If we're opening this dropdown, dispatch a custom event to close others
    if (!showStatusOptions.value) {
      window.dispatchEvent(new CustomEvent("close-day-dropdowns"));
    }
    showStatusOptions.value = !showStatusOptions.value;
  }
};

// Listen for the custom event to close this dropdown
const handleCloseDropdowns = () => {
  showStatusOptions.value = false;
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  window.addEventListener("close-day-dropdowns", handleCloseDropdowns);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
  window.removeEventListener("close-day-dropdowns", handleCloseDropdowns);
});

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
