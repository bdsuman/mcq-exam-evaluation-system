<template>
  <div>
    <GoBack class="mt-2 mb-[22px]" />
    <div class="flex items-center justify-between">
      <div
        class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]">
        {{ $t("question_details") }}
      </div>
    </div>
    <div class="self-stretch p-8 bg-white rounded-2xl w-full">
      <div class="form-row-grid">
        <FormValue label="type" :value="formatType(data.type)" />
        <FormValue label="mark" :value="data.mark" />
        <FormValue
          label="published"
          :value="data.published ? $t('yes') : $t('no')" />
        <div class="col-span-2">
          <FormValue label="question" :value="data.question" />
        </div>
      </div>

      <!-- Options Section -->
      <div class="mt-8">
        <h3 class="text-xl font-semibold text-[#002d45] mb-4">
          {{ $t("options") }} ({{ data.options?.length || 0 }})
        </h3>
        <div class="space-y-3">
          <div
            v-for="(option, index) in data.options"
            :key="index"
            class="bg-gray-50 p-4 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="font-medium text-gray-600">
                {{ index + 1 }}.
              </span>
              <span class="text-gray-800">
                {{ option.option_text }}
              </span>
            </div>
            <div v-if="option.is_correct" class="flex items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-green-600"
                viewBox="0 0 20 20"
                fill="currentColor">
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd" />
              </svg>
              <span class="text-green-600 font-medium text-sm">
                {{ $t("correct") }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end !items-end h-full mt-6">
        <Button
          title="edit"
          :width="'w-40'"
          :to="{ name: 'QuestionUpdate', params: { id: data.id } }"
          :bgClass="'bg-[#C1FDDA]'"
          :textColor="'text-[#2C6456]'" />
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import GoBack from "@/components/common/GoBack.vue";
import FormValue from "@/components/form/FormValue.vue";
import { onBeforeMount, reactive } from "vue";
import { useRoute } from "vue-router";
import Button from "@/components/buttons/Button.vue";

const vRoute = useRoute();

const data = reactive({
  options: [],
});

const formatType = (type) => {
  if (type === "single_choice") return "Single Choice";
  if (type === "multiple_choice") return "Multiple Choice";
  return type;
};

const getDetails = async () => {
  try {
    const res = await axios.get(
      route("questions.show", {
        question: vRoute.params.id,
      })
    );
    Object.assign(data, res.data?.data);
  } catch (error) {
    // console.log(error);
  }
};

onBeforeMount(async () => {
  await getDetails();
});
</script>
