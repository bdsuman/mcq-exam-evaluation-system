<template>
  <div>
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-[#002d45] mb-2">
        {{ $t("welcome") }}, {{ userStore.user?.full_name }}!
      </h1>
      <p class="text-gray-600">{{ $t("user_dashboard") }}</p>
    </div>

    <!-- User Dashboard Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Profile Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("Profile") }}</p>
            <p class="text-2xl font-bold text-[#002d45]">{{ userStore.user?.full_name }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
        </div>
        <router-link
          :to="{ name: 'profile' }"
          class="text-blue-600 hover:text-blue-700 text-sm font-medium">
          {{ $t("view_profile") }} →
        </router-link>
      </div>

      <!-- Email Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("email") }}</p>
            <p class="text-lg font-bold text-[#002d45] break-all">{{ userStore.user?.email }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Language Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("language") }}</p>
            <p class="text-2xl font-bold text-[#002d45]">{{ userStore.user?.language?.toUpperCase() }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948-.684l1.498-4.493a1 1 0 011.502-.684l1.498 4.493a1 1 0 00.948.684H19a2 2 0 012 2v1M3 5h18v12a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm3 4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm4-4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm4-4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Account Management Section -->
    <div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
      <h2 class="text-xl font-bold text-[#002d45] mb-4">{{ $t("account_management") }}</h2>
      <div class="flex gap-4 flex-wrap">
        <router-link
          :to="{ name: 'profile' }"
          class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          {{ $t("edit_profile") }}
        </router-link>
        <router-link
          :to="{ name: 'changePassword' }"
          class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          {{ $t("change_password") }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useUserStore } from "@/stores/useUserStore";

const userStore = useUserStore();
</script>
