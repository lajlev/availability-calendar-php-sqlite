import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

/**
 * Combines multiple class names and merges Tailwind CSS classes
 */
export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

/**
 * Formats a date as YYYY-MM-DD for API calls
 * or as a readable format for display
 */
export function formatDate(date, displayFormat = false) {
  if (displayFormat) {
    const options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    };
    return date.toLocaleDateString("da-DK", options);
  }
  return date.toISOString().split("T")[0];
}

/**
 * Checks if a date is in the past
 */
export function isPastDate(date) {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return date < today;
}

/**
 * Gets the month name from a date
 */
export function getMonthName(date) {
  return date.toLocaleString("da-DK", { month: "long" });
}

/**
 * Gets all dates for a specific month
 */
export function getDatesInMonth(year, month) {
  const dates = [];
  const date = new Date(year, month, 1);

  // Add empty slots for days before the first day of the month
  // Convert Sunday (0) to 6, and other days to day-1 to make Monday (1) the first day of the week
  let firstDayOfMonth = date.getDay();
  firstDayOfMonth = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;

  for (let i = 0; i < firstDayOfMonth; i++) {
    dates.push(null);
  }

  // Add all days in the month
  while (date.getMonth() === month) {
    dates.push(new Date(date));
    date.setDate(date.getDate() + 1);
  }

  return dates;
}

/**
 * Gets all months for the specified year offset
 * @param {number} yearOffset - Offset from current year (0 = current year, 1 = next year, etc.)
 */
export function getMonths(yearOffset = 0) {
  const months = [];
  const today = new Date();
  const currentYear = today.getFullYear();
  const currentMonth = today.getMonth();

  // If we're in the current year (offset = 0), only show current and future months
  if (yearOffset === 0) {
    // Current year - remaining months
    for (let month = currentMonth; month < 12; month++) {
      months.push({
        year: currentYear,
        month: month,
      });
    }

    // Next year - months up to current month
    for (let month = 0; month < currentMonth; month++) {
      months.push({
        year: currentYear + 1,
        month: month,
      });
    }
  } else {
    // For other years, show all 12 months
    const targetYear = currentYear + yearOffset;

    for (let month = 0; month < 12; month++) {
      months.push({
        year: targetYear,
        month: month,
      });
    }
  }

  return months;
}

/**
 * Gets the status class for a date
 */
export function getStatusClass(status) {
  switch (status) {
    case "available":
      return "available";
    case "booked":
      return "booked";
    case "half-booked":
      return "half-booked";
    default:
      return "";
  }
}
