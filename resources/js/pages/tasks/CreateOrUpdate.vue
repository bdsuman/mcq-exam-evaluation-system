<template>
  <div>
    <GoBack class="mt-2 mb-[22px]" />
    <div class="flex items-center justify-between">
      <div
        class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]">
        {{ $t(isEdit ? "edit" : "add") }}
        {{ $t("task") }}
      </div>
      <LanguageDropdown
        v-if="isEdit"
        v-model="form.language"
        @changed="(v) => getDetails(v)" />
    </div>
    <div
      class="self-stretch p-8 bg-white rounded-2xl inline-flex flex-col items-end w-full !min-h-[calc(100vh-250px)] overflow-y-auto">
      <div class="w-full grid grid-cols-2 gap-4">
        <TextInput
          label="title"
          :placeholder="$t('enter_title')"
          required
          :maxLength="100"
          v-model="form.title" />
        <div class="flex-col justify-start items-start flex">
          <DragAndDropUpload
            v-model="form.avatar"
            class="w-full"
            label="avatar"
            attachmentLabelClass="!py-10"
            :placeholder="$t('click_or_drag_&_drop_files_here_to_upload')"
            :show-file-names="true"
            :maxFiles="1"
            :maxFileSize="20"
            :allowedExtensions="['.jpg', '.JPEG', '.png']"
            :extraClass="v$?.avatar?.$error ? '!border-red-500' : ''"
            @blur="validate('avatar')" />
          <div
            v-if="v$?.avatar?.$errors"
            class="justify-start text-xs font-normal leading-none text-red-500">
            {{ v$?.avatar?.$errors?.[0]?.$message }}
          </div>
        </div>
        <TextInput
          label="description"
          :placeholder="$t('write')"
          required
          :maxLength="200"
          v-model="form.description"
          :isTextarea="true"
          :helpText="$t('max_200_characters')" />
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
import DragAndDropUpload from "@/components/form/DragAndDropSingleUpload.vue";
import Form from "vform";
import { useNotify } from "@/composables/useNotification";
import { computed, onMounted, reactive } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";
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
    title: "",
    description: "",
    language: store.user?.language ?? "en",
    avatar: null,
    _method: isEdit ? "put" : "post",
  })
);

const validate = (fieldName) => {
  v$.value[fieldName].$touch();
};

const dynamicRules = computed(() => ({
  // avatar: {
  //   required: helpers.withMessage(trans("field_input_is_missing"), required),
  // },
}));

const v$ = useVuelidate(dynamicRules, form);
const resetForm = () => {
  form.reset();
  form.clear();
  v$.value.$reset();
};
const handleSubmit = async () => {
  v$.value.$touch();
  if (v$.value.$invalid) {
    return;
  }
  try {
    form.avatar = isEdit ? resetIfUrlObject(form.avatar) : form.avatar;

    const { data } = await axios.post(
      isEdit
        ? route("tasks.update", {
            task: isEdit,
          })
        : route("tasks.store"),
      form,
      { headers: { "Content-Type": "multipart/form-data" } }
    );
    let message = isEdit ? "task_updated" : "task_created";
    notify.success({
      message: trans(message),
    });
    isEdit ? setEditData(data) : vRouter.push({ name: "TaskIndex" });
  } catch (error) {
    // console.error(error);
  }
};
const resetIfUrlObject = (field) => {
  if (typeof field === "object" && /^https?:\/\//.test(field?.url)) {
    return null;
  }
  return field;
};
// Get Data for edit
const getDetails = async (lang) => {
  try {
    const { data } = await axios.get(route("tasks.show", { task: isEdit }), {
      headers: {
        "X-Request-Language": lang,
      },
    });
    setEditData(data);
  } catch (error) {
    // console.error("error_fetching_data_for_edit:", error);
  }
};

function setEditData(data) {
  form.title = data.data.title;
  form.description = data.data.description;
  form.avatar = data?.data?.avatar_url
    ? { url: data.data.avatar_url, file_type: "image" }
    : null;
}

onMounted(async () => {
  if (isEdit) {
    await getDetails(form.language);
  }
});
</script>
