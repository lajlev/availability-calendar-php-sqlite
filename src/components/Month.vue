<template>
  <div class="bg-card rounded-lg shadow-sm p-4">
    <h3 class="text-lg font-medium mb-4">{{ monthName }} {{ year }}</h3>

    <!-- Week day headers with extra column for week numbers when in admin view -->
    <div :class="[isAdmin ? 'grid-cols-8' : 'grid-cols-7', 'grid gap-1 mb-2']">
      <!-- Week number header (only visible in admin view) -->
      <div
        v-if="isAdmin"
        class="text-xs text-center font-medium text-gray-500"
      >
        Uge
      </div>

      <div
        v-for="day in weekDays"
        :key="day"
        class="text-xs text-center font-medium text-gray-500"
      >
        {{ day }}
      </div>
    </div>

    <!-- Calendar grid with week numbers -->
    <div :class="[isAdmin ? 'grid-cols-8' : 'grid-cols-7', 'grid gap-1']">
      <!-- Render each week -->
      <template
        v-for="(week, weekIndex) in weeklyDates"
        :key="weekIndex"
      >
        <!-- Week number (only visible in admin view) -->
        <div
          v-if="isAdmin"
          class="text-xs flex items-center justify-center font-medium text-gray-500 bg-gray-100 rounded cursor-pointer hover:bg-gray-200"
          @click="showWeekStatusOptions(week, weekIndex, $event)"
        >
          {{ getWeekNumber(week) }}
        </div>

        <!-- Week status options dropdown -->
        <div
          v-if="showingWeekOptions && selectedWeek === weekIndex"
          class="fixed bg-white border border-gray-200 rounded-md shadow-lg z-20 w-32"
          :style="{
            top: dropdownPosition.y + 'px',
            left: dropdownPosition.x + 'px',
          }"
        >
          <button
            v-for="option in statusOptions"
            :key="option.value"
            @click.stop="updateWeekStatus(week, option.value)"
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

        <!-- Days in the week -->
        <Day
          v-for="(dateInfo, dayIndex) in week"
          :key="`${weekIndex}-${dayIndex}`"
          :date="dateInfo.date"
          :status="dateInfo.status"
          :isAdmin="isAdmin"
          @update="updateDate"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import Day from "./Day.vue";
import { getMonthName } from "../lib/utils";

const props = defineProps({
  year: {
    type: Number,
    required: true,
  },
  month: {
    type: Number,
    required: true,
  },
  dates: {
    type: Array,
    required: true,
  },
  isAdmin: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update-date"]);

// State for week status options dropdown
const showingWeekOptions = ref(false);
const selectedWeek = ref(null);
const dropdownPosition = ref({ x: 0, y: 0 });

// Status options for dropdown
const statusOptions = [
  { value: "available", label: "Ledig" },
  { value: "booked", label: "Optaget" },
  { value: "half-booked", label: "Skifte dag" },
];

// Show week status options
function showWeekStatusOptions(week, weekIndex, event) {
  // Toggle dropdown for the clicked week
  if (showingWeekOptions.value && selectedWeek.value === weekIndex) {
    showingWeekOptions.value = false;
    selectedWeek.value = null;
  } else {
    showingWeekOptions.value = true;
    selectedWeek.value = weekIndex;

    // Store the position of the clicked week number
    if (event) {
      // Position the dropdown below the week number
      dropdownPosition.value = {
        x: event.clientX - 16, // Center the dropdown horizontally
        y: event.clientY + 10, // Position below the week number
      };
    }
  }

  // Stop event propagation to prevent immediate closing
  if (event) {
    event.stopPropagation();
  }
}

// Update status for all days in a week
function updateWeekStatus(week, status) {
  console.log("Updating week status:", week, status);

  // Update each valid date in the week
  week.forEach((dateInfo) => {
    if (dateInfo.date) {
      console.log("Updating date:", dateInfo.date, "to status:", status);
      emit("update-date", dateInfo.date, status);
    }
  });

  // Close the dropdown
  showingWeekOptions.value = false;
}

// Close dropdown when clicking outside
function handleClickOutside(event) {
  if (showingWeekOptions.value) {
    showingWeekOptions.value = false;
  }
}

// Add/remove event listeners
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});

const monthName = computed(() => {
  return getMonthName(new Date(props.year, props.month, 1));
});

const weekDays = ["Man", "Tir", "Ons", "Tor", "Fre", "Lør", "Søn"];

// Group dates by week for easier rendering with week numbers
const weeklyDates = computed(() => {
  const weeks = [];
  let currentWeek = [];

  // Process each date
  props.dates.forEach((dateInfo, index) => {
    // Add date to current week
    currentWeek.push(dateInfo);

    // If we've reached the end of a week (7 days) or the end of the array
    if (currentWeek.length === 7 || index === props.dates.length - 1) {
      // If it's the last week and not complete, pad with null dates
      while (currentWeek.length < 7) {
        currentWeek.push({ date: null, status: null });
      }

      // Add the completed week to our weeks array
      weeks.push([...currentWeek]);

      // Reset for the next week
      currentWeek = [];
    }
  });

  return weeks;
});

// Calculate the ISO week number for a given week
function getWeekNumber(week) {
  // Find the middle date in the week (Thursday) to ensure correct week number
  // This is more reliable than using the first day of the week
  const thursdayIndex = 3; // Thursday is the 4th day (index 3) in our week array
  const thursdayDate = week[thursdayIndex];

  // If Thursday is not available, try to find any valid date in the week
  let validDate =
    thursdayDate && thursdayDate.date
      ? thursdayDate
      : week.find((day) => day.date !== null);

  if (!validDate || !validDate.date) {
    return "-";
  }

  const date = new Date(validDate.date);

  // Adjust to get the Thursday of the ISO week
  const dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
  const thursdayDiff = dayOfWeek === 0 ? -3 : 4 - dayOfWeek; // Adjust to Thursday

  // Create a new date object for the Thursday of this ISO week
  const thursday = new Date(date);
  thursday.setDate(date.getDate() + thursdayDiff);

  // The year of this Thursday is the ISO year
  const isoYear = thursday.getFullYear();

  // Get the first Thursday of the ISO year
  const firstThursday = new Date(isoYear, 0, 1);
  while (firstThursday.getDay() !== 4) {
    firstThursday.setDate(firstThursday.getDate() + 1);
  }

  // Calculate the week number
  const diff = thursday - firstThursday;
  const weekNum = Math.floor(diff / (7 * 24 * 60 * 60 * 1000)) + 1;

  return weekNum;
}

function updateDate(date, status) {
  emit("update-date", date, status);
}
</script>
