<template>
  <div>
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-[#002d45] mb-2">
        {{ $t("welcome") }}, {{ userStore.user?.full_name }}!
      </h1>
      <p class="text-gray-600">{{ $t("admin_dashboard") }}</p>
    </div>

    <!-- Admin Dashboard Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Questions Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("total_questions") }}</p>
            <p class="text-3xl font-bold text-[#002d45]">{{ stats.totalQuestions }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <router-link
          :to="{ name: 'QuestionIndex' }"
          class="text-purple-600 hover:text-purple-700 text-sm font-medium">
          {{ $t("manage_questions") }} →
        </router-link>
      </div>

      <!-- Quick Stats Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("system_status") }}</p>
            <p class="text-3xl font-bold text-green-600">{{ $t("active") }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <p class="text-gray-600 text-xs">{{ $t("all_systems_operational") }}</p>
      </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
      <h2 class="text-xl font-bold text-[#002d45] mb-4">{{ $t("quick_actions") }}</h2>
      <div class="flex gap-4 flex-wrap">
        <router-link
          :to="{ name: 'QuestionCreate' }"
          class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ $t("add_question") }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, onMounted } from "vue";
import axios from "axios";
import { useUserStore } from "@/stores/useUserStore";

const userStore = useUserStore();

const stats = reactive({
  totalQuestions: 0,
});

const fetchStats = async () => {
  try {
    const { data } = await axios.get(route("questions.index"), { params: { per_page: 1 } });
    stats.totalQuestions = data?.meta?.total || 0;
  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
};

onMounted(() => {
  fetchStats();
});
</script>
