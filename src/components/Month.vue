<template>
  <div class="bg-card rounded-lg shadow-sm p-4">
    <h3 class="text-lg font-medium mb-4">{{ monthName }} {{ year }}</h3>

    <div class="grid grid-cols-7 gap-1 mb-2">
      <div
        v-for="day in weekDays"
        :key="day"
        class="text-xs text-center font-medium text-gray-500"
      >
        {{ day }}
      </div>
    </div>

    <div class="grid grid-cols-7 gap-1">
      <Day
        v-for="(dateInfo, index) in dates"
        :key="index"
        :date="dateInfo.date"
        :status="dateInfo.status"
        :isAdmin="isAdmin"
        @update="updateDate"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
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

const monthName = computed(() => {
  return getMonthName(new Date(props.year, props.month, 1));
});

const weekDays = ["Man", "Tir", "Ons", "Tor", "Fre", "Lør", "Søn"];

function updateDate(date, status) {
  emit("update-date", date, status);
}
</script>
