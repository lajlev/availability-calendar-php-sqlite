<template>
  <div>
    <div class="flex justify-center items-center mb-6">
      <div class="flex items-center space-x-4">
        <button
          @click="previousYear"
          class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 flex items-center"
          :disabled="yearOffset <= 0"
          :class="{ 'opacity-50 cursor-not-allowed': yearOffset <= 0 }"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
        </button>

        <span class="font-medium">{{ currentYearDisplay }}</span>

        <button
          @click="nextYear"
          class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"
            />
          </svg>
        </button>
      </div>
    </div>

    <div
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
    >
      <Month
        v-for="(monthData, index) in months"
        :key="`${monthData.year}-${monthData.month}`"
        :year="monthData.year"
        :month="monthData.month"
        :dates="getDatesForMonth(monthData.year, monthData.month)"
        :isAdmin="isAdmin"
        @update-date="updateDateStatus"
      />
    </div>
    <div class="flex justify-center items-center mb-6">
      <div class="flex items-center space-x-4">
        <button
          @click="previousYear"
          class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 flex items-center"
          :disabled="yearOffset <= 0"
          :class="{ 'opacity-50 cursor-not-allowed': yearOffset <= 0 }"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
        </button>

        <span class="font-medium">{{ currentYearDisplay }}</span>

        <button
          @click="nextYear"
          class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import Month from "./Month.vue";
import { getMonths, formatDate } from "../lib/utils";

const props = defineProps({
  isAdmin: {
    type: Boolean,
    default: false,
  },
});

const yearOffset = ref(0);
const currentYearDisplay = computed(() => {
  const today = new Date();
  return today.getFullYear() + yearOffset.value;
});

const months = computed(() => getMonths(yearOffset.value));

function previousYear() {
  if (yearOffset.value > 0) {
    yearOffset.value--;
  }
}

function nextYear() {
  yearOffset.value++;
}
const dateStatuses = ref({});
const loading = ref(true);

// Fetch all date statuses on component mount
onMounted(async () => {
  try {
    loading.value = true;
    const response = await axios.get("/api/dates");

    // Convert array of date objects to a map for easier lookup
    const statusMap = {};
    response.data.forEach((date) => {
      statusMap[date.date] = date.status;
    });

    dateStatuses.value = statusMap;
  } catch (error) {
    console.error("Error fetching dates:", error);
  } finally {
    loading.value = false;
  }
});

// Get dates with their statuses for a specific month
function getDatesForMonth(year, month) {
  const date = new Date(year, month, 1);
  const dates = [];

  // Add empty slots for days before the first day of the month
  // Convert Sunday (0) to 6, and other days to day-1 to make Monday (1) the first day of the week
  let firstDayOfMonth = date.getDay();
  firstDayOfMonth = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;

  for (let i = 0; i < firstDayOfMonth; i++) {
    dates.push({ date: null, status: null });
  }

  // Add all days in the month
  while (date.getMonth() === month) {
    const dateString = formatDate(date);
    dates.push({
      date: new Date(date),
      status: dateStatuses.value[dateString] || "available",
    });
    date.setDate(date.getDate() + 1);
  }

  return dates;
}

// Update the status of a date
async function updateDateStatus(date, status) {
  if (!props.isAdmin) return;

  try {
    const dateString = formatDate(date);
    const response = await axios.post(`/api/dates/${dateString}`, { status });

    // Update local state
    dateStatuses.value[dateString] = status;
  } catch (error) {
    console.error("Error updating date status:", error);
    // TODO: Show error notification
  }
}
</script>
