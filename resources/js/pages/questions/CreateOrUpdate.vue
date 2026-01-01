<template>
  <div>
    <GoBack class="mt-2 mb-[22px]" />
    <div class="flex items-center justify-between">
      <div
        class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]">
        {{ $t(isEdit ? "edit" : "add") }}
        {{ $t("question") }}
      </div>
      <LanguageDropdown
        v-if="isEdit"
        v-model="form.language"
        @changed="(v) => getDetails(v)" />
    </div>
    <div
      class="self-stretch p-8 bg-white rounded-2xl inline-flex flex-col items-end w-full !min-h-[calc(100vh-250px)] overflow-y-auto">
      <div class="w-full grid grid-cols-1 gap-4">
        
        <TextInput
          label="question"
          :placeholder="$t('enter_question')"
          required
          :maxLength="150"
          v-model="form.question"
          :helpText="$t('max_150_characters')"
          @blur="validate('question')" />
        <TextInput
          label="mark"
          :placeholder="$t('enter_mark')"
          required
          :min="1"
          :max="100"
          v-model="form.mark"
          @blur="validate('mark')" />

      </div>

      <!-- Options Section -->
      <div class="w-full mt-8">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-[#002d45]">
            {{ $t("options") }} ({{ $t("minimum_2_required") }})
          </h3>
          <Button
            title="add_option"
            :width="'w-40'"
            @click="addOption" />
        </div>

        <div
          v-for="(option, index) in form.options"
          :key="index"
          class="bg-gray-50 p-4 rounded-lg mb-3 relative">
          <div class="grid grid-cols-12 gap-4 items-center">
            <div class="col-span-8">
              <TextInput
                :label="$t('option') + ' ' + (index + 1)"
                :placeholder="$t('enter_option_text')"
                required
                :maxLength="500"
                v-model="option.option_text" />
            </div>
            <div class="col-span-3">
              <ToggleSwitch
                :label="$t('correct_answer')"
                v-model="option.is_correct" />
            </div>
            <div class="col-span-1 flex justify-end">
              <button
                v-if="form.options.length > 2"
                @click="removeOption(index)"
                class="text-red-500 hover:text-red-700 p-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div
          v-if="v$?.options?.$errors?.length"
          class="text-red-500 text-sm mt-2">
          {{ v$?.options?.$errors?.[0]?.$message }}
        </div>
      </div>

      <div class="flex gap-4 mt-[40px]">
        <OutlineButton title="reset" @click="resetForm" />
        <Button
          :title="isEdit ? 'update' : 'add'"
          :width="'w-40'"
          @click="handleSubmit" />
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import GoBack from "@/components/common/GoBack.vue";
import TextInput from "@/components/form/TextInput.vue";
import Button from "@/components/buttons/Button.vue";
import OutlineButton from "@/components/buttons/OutlineButton.vue";
import ToggleSwitch from "@/components/form/ToggleSwitch.vue";
import Form from "vform";
import { useNotify } from "@/composables/useNotification";
import { computed, onMounted, reactive, ref } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, minLength, helpers } from "@vuelidate/validators";
import { trans } from "laravel-vue-i18n";
import { useRoute, useRouter } from "vue-router";
import LanguageDropdown from "@/components/form/LanguageDropdown.vue";
import { useUserStore } from "@/stores/useUserStore";

const store = useUserStore();
const notify = useNotify();
const vRoute = useRoute();
const vRouter = useRouter();

const isEdit = vRoute.params?.id || null;

const form = reactive(
  new Form({
    type: "single_choice",
    question: "",
    mark: 5,
    published: false,
    language: store.user?.language ?? "en",
    options: [
      { option_text: "", is_correct: false },
      { option_text: "", is_correct: false },
    ],
    _method: isEdit ? "put" : "post",
  })
);

const addOption = () => {
  form.options.push({ option_text: "", is_correct: false });
};

const removeOption = (index) => {
  if (form.options.length > 2) {
    form.options.splice(index, 1);
  }
};

const validate = (fieldName) => {
  v$.value[fieldName].$touch();
};

const dynamicRules = computed(() => ({
  question: {
    required: helpers.withMessage(trans("question_is_required"), required),
  },
  mark: {
    required: helpers.withMessage(trans("mark_is_required"), required),
  },
  options: {
    required: helpers.withMessage(trans("options_are_required"), required),
    minLength: helpers.withMessage(
      trans("minimum_2_options_required"),
      minLength(2)
    ),
  },
}));

const v$ = useVuelidate(dynamicRules, form);

const resetForm = () => {
  form.type = "single_choice";
  form.question = "";
  form.mark = 5;
  form.published = false;
  form.options = [
    { option_text: "", is_correct: false },
    { option_text: "", is_correct: false },
  ];
  v$.value.$reset();
};

const handleSubmit = async () => {
  v$.value.$touch();
  if (v$.value.$invalid) {
    notify.error({ message: trans("please_fill_all_required_fields") });
    return;
  }

  // Validate options
  const hasEmptyOptions = form.options.some((opt) => !opt.option_text.trim());
  if (hasEmptyOptions) {
    notify.error({ message: trans("all_options_must_have_text") });
    return;
  }

  const correctAnswersCount = form.options.filter((opt) => opt.is_correct).length;
  
  if (correctAnswersCount === 0) {
    notify.error({ message: trans("at_least_one_correct_answer_required") });
    return;
  }

  // Automatically set type based on correct answers count
  form.type = correctAnswersCount > 1 ? "multiple_choice" : "single_choice";

  try {
    const { data } = await axios.post(
      isEdit
        ? route("questions.update", {
            question: isEdit,
          })
        : route("questions.store"),
      form
    );

    let message = isEdit ? "question_updated" : "question_created";
    notify.success({
      message: trans(message),
    });

    isEdit ? setEditData(data) : vRouter.push({ name: "QuestionIndex" });
  } catch (error) {
    if (error.response?.data?.message) {
      notify.error({ message: error.response.data.message });
    }
  }
};

// Get Data for edit
const getDetails = async (lang) => {
  try {
    const { data } = await axios.get(
      route("questions.show", { question: isEdit }),
      {
        headers: {
          "X-Request-Language": lang,
        },
      }
    );
    setEditData(data);
  } catch (error) {
    // console.log(error);
  }
};

const setEditData = (res) => {
  form.type = res.data?.type;
  form.question = res.data?.question;
  form.mark = res.data?.mark;
  form.published = res.data?.published;
  
  // Set options
  if (res.data?.options && Array.isArray(res.data.options)) {
    form.options = res.data.options.map((opt) => ({
      option_text: opt.option_text,
      is_correct: opt.is_correct,
    }));
  }
};

onMounted(async () => {
  if (isEdit) {
    await getDetails(form.language);
  }
});
</script>
