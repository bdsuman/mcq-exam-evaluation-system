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

      <!-- Users Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("total_users") }}</p>
            <p class="text-3xl font-bold text-[#002d45]">{{ stats.totalUsers }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A3 3 0 018 17h8a3 3 0 012.879 2.197M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Submitted Students Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("submitted_students") }}</p>
            <p class="text-3xl font-bold text-[#002d45]">{{ stats.submittedStudents }}</p>
          </div>
          <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2a3 3 0 00-.176-1H7.176A3 3 0 007 18v2m10 0a3 3 0 00-3-3H7a3 3 0 00-3 3m13-11a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Submissions Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("total_submissions") }}</p>
            <p class="text-3xl font-bold text-[#002d45]">{{ stats.questionsAnswered }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Correct Submissions Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("correct_submissions") }}</p>
            <p class="text-3xl font-bold text-[#002d45]">{{ stats.correctSubmissions }}</p>
          </div>
          <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m4 4a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Incorrect Submissions Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("incorrect_submissions") }}</p>
            <p class="text-3xl font-bold text-[#002d45]">{{ stats.incorrectSubmissions }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Accuracy Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-gray-600 text-sm">{{ $t("accuracy_percent") }}</p>
            <p class="text-3xl font-bold text-[#002d45]">{{ stats.accuracyPercent }}%</p>
          </div>
          <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
            </svg>
          </div>
        </div>
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

        <router-link
          :to="{ name: 'profile' }"
          class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A3 3 0 018 17h8a3 3 0 012.879 2.197M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {{ $t("view_profile") }}
        </router-link>

        <router-link
          :to="{ name: 'changePassword' }"
          class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm0 0v8m-7-4h14" />
          </svg>
          {{ $t("change_password") }}
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
  totalUsers: 0,
  questionsAnswered: 0,
  submittedStudents: 0,
  correctSubmissions: 0,
  incorrectSubmissions: 0,
  accuracyPercent: 0,
});

const fetchStats = async () => {
  try {
    const { data } = await axios.get(route("admin.dashboard.stats"));
    stats.totalQuestions = data?.data?.total_questions || 0;
    stats.totalUsers = data?.data?.total_users || 0;
    stats.questionsAnswered = data?.data?.questions_answered || 0;
    stats.submittedStudents = data?.data?.submitted_students || 0;
    stats.correctSubmissions = data?.data?.correct_submissions || 0;
    stats.incorrectSubmissions = data?.data?.incorrect_submissions || 0;
    stats.accuracyPercent = data?.data?.accuracy_percent || 0;
  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
};

onMounted(() => {
  fetchStats();
});
</script>
