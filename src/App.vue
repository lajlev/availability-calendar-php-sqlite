<template>
  <div class="min-h-screen bg-background">
    <header class="container py-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Landsbyidyl</h1>
      </div>
    </header>

    <main class="container">
      <Calendar :isAdmin="isAdmin" />
      <div v-if="isAdmin">
        <button
          @click="logout"
          class="text-sm text-red-500 hover:text-red-700"
        >
          Log ud
        </button>
      </div>
      <div v-else>
        <button
          @click="showLoginModal = true"
          class="text-sm text-blue-500 hover:text-blue-700"
        >
          Log ind
        </button>
      </div>
    </main>

    <!-- Admin Login Modal -->
    <div
      v-if="showLoginModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Admin login</h2>
        <form @submit.prevent="login">
          <div class="mb-4">
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Adgangskode</label
            >
            <input
              type="password"
              id="password"
              v-model="password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md"
              required
            />
          </div>
          <div class="flex justify-end space-x-2">
            <button
              type="button"
              @click="showLoginModal = false"
              class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
            >
              Annuller
            </button>
            <button
              type="submit"
              class="px-4 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600"
            >
              Log ind
            </button>
          </div>
        </form>
        <p
          v-if="loginError"
          class="mt-2 text-sm text-red-500"
        >
          {{ loginError }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Calendar from "./components/Calendar.vue";

const isAdmin = ref(false);
const showLoginModal = ref(false);
const password = ref("");
const loginError = ref("");

// Check if admin session exists on load
onMounted(async () => {
  try {
    const response = await axios.get("/api/auth/check");
    if (response.data.authenticated) {
      isAdmin.value = true;
    }
  } catch (error) {
    console.error("Error checking authentication:", error);
  }
});

// Login function
async function login() {
  try {
    loginError.value = "";

    // Make sure password is not empty
    if (!password.value) {
      loginError.value = "Adgangskode kan ikke være tom";
      return;
    }

    const response = await axios.post(
      "/api/auth/login",
      {
        password: password.value,
      },
      {
        headers: {
          "Content-Type": "application/json",
        },
        withCredentials: true,
      }
    );

    if (response.data.authenticated) {
      isAdmin.value = true;
      showLoginModal.value = false;
      password.value = "";

      // Refresh the calendar with admin privileges
      window.location.reload();
    }
  } catch (error) {
    console.error("Login error:", error);
    loginError.value = "Ugyldig adgangskode";
  }
}

// Logout function
async function logout() {
  try {
    await axios.post("/api/auth/logout");
    isAdmin.value = false;
  } catch (error) {
    console.error("Logout error:", error);
  }
}
</script>
