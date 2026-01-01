<template>
  <div>
    <div class="flex items-center justify-between mb-6 mt-2">
      <div class="text-[#002d45] text-[32px] font-semibold">
        {{ $t("mcq_practice") }}
      </div>
    </div>

    <div v-if="loading" class="text-gray-500">{{ $t("loading") }}...</div>
    <div v-else-if="error" class="text-red-500">{{ $t(error) }}</div>

    <div class="mb-3" v-else>
      <div v-if="questions.length === 0" class="text-gray-500">{{ $t("no_questions_available") }}</div>

      <div v-else class="grid gap-4">
        <div
          v-for="question in questions"
          :key="question.id"
          class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between"
        >
          <div class="pr-4">
            <div class="flex items-center gap-2">
              <p class="text-xs uppercase text-gray-500">{{ formatType(question.type) }}</p>
              <span
                v-if="question.is_submitted"
                class="text-[11px] uppercase font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700"
              >
                {{ $t("submitted") }}
              </span>
            </div>
            <h3 class="text-lg font-semibold text-[#002d45] line-clamp-2">{{ question.question }}</h3>
            <p class="text-sm text-gray-500">{{ $t("marks") }}: {{ question.mark }}</p>
          </div>
          <div class="flex items-center gap-3">
            <Button :title="'view'" :width="'w-28'" @click="openModal(question)" />
          </div>
        </div>
      </div>
    </div>

    <BaseModal :show="showModal" :title="'mcq_practice'" :width="720" :height="560" @close="closeModal">
      <template #body>
        <div v-if="activeQuestion" class="w-full space-y-4">
          <div>
            <p class="text-xs uppercase text-gray-500">{{ formatType(activeQuestion.type) }}</p>
            <h3 class="text-xl font-semibold text-[#002d45]">{{ activeQuestion.question }}</h3>
            <p class="text-sm text-gray-500">{{ $t("marks") }}: {{ activeQuestion.mark }}</p>
          </div>

          <div
            v-if="isSubmitted(activeQuestion.id)"
            class="p-3 rounded-xl border border-amber-200 bg-amber-50 text-amber-700"
          >
            {{ $t("already_submitted") }}
          </div>

          <div class="space-y-3">
            <label
              v-for="option in activeQuestion.options"
              :key="option.id"
              class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50"
            >
              <input
                :type="activeQuestion.type === 'single_choice' ? 'radio' : 'checkbox'"
                :name="'q-modal-' + activeQuestion.id"
                :value="option.id"
                :checked="isSelected(option.id)"
                :disabled="isSubmitted(activeQuestion.id)"
                @change="toggleSelection(option.id)"
                class="h-5 w-5 text-green-600 border-gray-300 focus:ring-green-500"
              />
              <span class="text-gray-800">{{ option.option_text }}</span>
            </label>
          </div>

          <div class="flex gap-3 pt-2">
            <Button
              :title="'submit'"
              :width="'w-32'"
              @click="handleSubmit"
              :disabled="submitting || isSubmitted(activeQuestion.id)"
            />
            <OutlineButton
              title="reset"
              :width="'w-32'"
              @click="resetSelection"
              :disabled="submitting || isSubmitted(activeQuestion.id)"
            />
          </div>

          <div
            v-if="resultSummary"
            class="mt-2 p-4 rounded-xl"
            :class="resultSummary.is_correct ? 'bg-green-50 border border-green-100 text-green-700' : 'bg-red-50 border border-red-100 text-red-700'"
          >
            <div class="font-semibold">{{ $t("result") }}</div>
            <div class="text-sm">
              {{ $t("obtained_marks") }}: {{ resultSummary.obtained_marks }} / {{ resultSummary.total_marks }}
            </div>
            <div class="text-sm">
              {{ resultSummary.is_correct ? $t("correct") : $t("incorrect") }}
            </div>
          </div>
        </div>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import axios from "axios";
import Button from "@/components/buttons/Button.vue";
import OutlineButton from "@/components/buttons/OutlineButton.vue";
import BaseModal from "@/components/modals/BaseModal.vue";
import { ref, onMounted } from "vue";
import { useNotify } from "@/composables/useNotification";
import { trans } from "laravel-vue-i18n";

const questions = ref([]);
const loading = ref(false);
const error = ref("");

const showModal = ref(false);
const activeQuestion = ref(null);
const selectedOptions = ref([]);
const submitting = ref(false);
const resultSummary = ref(null);
const submissions = ref({});

const notify = useNotify();

const fetchQuestions = async () => {
  loading.value = true;
  error.value = "";
  try {
    const res = await axios.get(route("student.questions.index"));
    questions.value = Array.isArray(res.data?.data) ? res.data.data : [];
  } catch (e) {
    error.value = "failed_to_fetch_items";
  } finally {
    loading.value = false;
  }
};

const openModal = async (question) => {
  activeQuestion.value = question;
  selectedOptions.value = question.is_submitted ? [...(question.selected_option_ids || [])] : [];
  resultSummary.value = null;
  showModal.value = true;

  await fetchSubmission(question.id);
};

const closeModal = () => {
  showModal.value = false;
  activeQuestion.value = null;
  selectedOptions.value = [];
  resultSummary.value = null;
};

const isSelected = (optionId) => selectedOptions.value.includes(optionId);

const toggleSelection = (optionId) => {
  if (activeQuestion.value && isSubmitted(activeQuestion.value.id)) return;
  if (!activeQuestion.value) return;
  if (activeQuestion.value.type === "single_choice") {
    selectedOptions.value = [optionId];
  } else {
    if (selectedOptions.value.includes(optionId)) {
      selectedOptions.value = selectedOptions.value.filter((id) => id !== optionId);
    } else {
      selectedOptions.value = [...selectedOptions.value, optionId];
    }
  }
};

const handleSubmit = async () => {
  if (!activeQuestion.value) return;
  if (isSubmitted(activeQuestion.value.id)) return;
  if (selectedOptions.value.length === 0) {
    notify.error({ message: trans("please_select_an_option") });
    return;
  }

  submitting.value = true;
  try {
    const { data } = await axios.post(route("student.questions.submit"), {
      responses: [
        {
          question_id: activeQuestion.value.id,
          option_ids: selectedOptions.value,
        },
      ],
    });

    const detail = data.data.details?.[0];
    resultSummary.value = {
      total_marks: data.data.total_marks,
      obtained_marks: data.data.obtained_marks,
      is_correct: detail?.is_correct ?? false,
    };

    submissions.value[activeQuestion.value.id] = {
      question_id: activeQuestion.value.id,
      selected_option_ids: selectedOptions.value,
      correct_option_ids: detail?.correct_option_ids || [],
      is_correct: detail?.is_correct ?? false,
      obtained_marks: data.data.obtained_marks,
      total_marks: data.data.total_marks,
    };

    notify.success({ message: trans("submission_successful") });
  } catch (e) {
    notify.error({ message: e.response?.data?.message || trans("something_went_wrong") });
  } finally {
    submitting.value = false;
  }
};

const resetSelection = () => {
  selectedOptions.value = [];
  resultSummary.value = null;
};

const formatType = (type) => {
  if (type === "single_choice") return trans("single_choice");
  if (type === "multiple_choice") return trans("multiple_choice");
  return type;
};

const isSubmitted = (questionId) => !!submissions.value[questionId];

const fetchSubmission = async (questionId) => {
  if (!questionId) return;
  try {
    const { data } = await axios.get(route("student.questions.submission", questionId));
    if (!data.data?.is_submitted) return;
    submissions.value[questionId] = data.data;
    selectedOptions.value = data.data.selected_option_ids || [];
    resultSummary.value = {
      total_marks: data.data.total_marks,
      obtained_marks: data.data.obtained_marks,
      is_correct: data.data.is_correct,
    };
  } catch (e) {
    // Silently ignore missing submissions
  }
};

onMounted(fetchQuestions);
</script>
