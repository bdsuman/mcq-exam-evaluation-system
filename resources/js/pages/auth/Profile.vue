<template>
  <div>
    <GoBack class="mt-2 mb-[22px]" />
    <div class="flex items-center justify-between">
      <div
        class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]">
        {{ $t("Profile") }}
      </div>
    </div>
    <div
      class="self-stretch p-8 bg-white rounded-2xl inline-flex flex-col items-end w-full !min-h-[calc(100vh-250px)] overflow-y-auto">
      <div class="w-full grid grid-cols-2 gap-4">
        <div
          class="w-full inline-flex flex-col justify-start items-start gap-3">
          <TextInput
            label="full_name"
            :placeholder="$t('enter_full_name')"
            required
            :maxLength="100"
            v-model="form.full_name"
            @blur="validate('full_name')" />
        </div>
        <div
          class="w-full inline-flex flex-col justify-start items-start gap-3">
          <TextInput
            label="email"
            :placeholder="$t('enter_email')"
            required
            validationType="email"
            v-model="form.email"
            disabled />
        </div>
      </div>

      <div class="w-full grid grid-cols-2 gap-4 mt-4">
        <div class="flex-col justify-start items-start flex">
          <SingleSelect
            v-model="form.gender"
            label="gender"
            :placeholder="$t('Select gender')"
            optionLabel="label"
            optionValue="value"
            endpoint="v1/admin/enums/gender"
            :searchable="false"
            :showClear="true"
            :translateOptions="true"
            />
        </div>
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
            :extraClass="v$.avatar?.$error ? '!border-red-500' : ''"
            @blur="validate('avatar')" />
          <div
            v-if="v$.avatar?.$errors"
            class="justify-start text-xs font-normal leading-none text-red-500">
            {{ v$.avatar?.$errors?.[0]?.$message }}
          </div>
        </div>
      </div>

      <div
        v-if="errorMessage"
        class="w-full mt-4 p-3 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
        {{ errorMessage }}
      </div>

      <div class="flex gap-4 mt-[40px]">
        <Button
          title="update"
          :width="'w-40'"
          @click="handleSubmit"
          :disabled="isSubmitting" />
      </div>
    </div>
  </div>
</template>

<script setup>
import GoBack from "@/components/common/GoBack.vue";
import TextInput from "@/components/form/TextInput.vue";
import Button from "@/components/buttons/Button.vue";
import DragAndDropUpload from "@/components/form/DragAndDropSingleUpload.vue";
import Form from "vform";
import { useNotify } from "@/composables/useNotification";
import { computed, onMounted, reactive, ref } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, minLength, helpers } from "@vuelidate/validators";
import { trans } from "laravel-vue-i18n";
import { useRouter } from "vue-router";
import LanguageDropdown from "@/components/form/LanguageDropdown.vue";
import SingleSelect from "@/components/form/SingleSelect.vue";
import { useUserStore } from "@/stores/useUserStore";
import { method } from "lodash";

const store = useUserStore();
const notify = useNotify();
const router = useRouter();

const isSubmitting = ref(false);
const errorMessage = ref("");

const form = reactive(
  new Form({
    full_name: store.user?.full_name || "",
    email: store.user?.email || "",
    language: store.user?.language ?? "en",
    gender: store.user?.gender || null,
    avatar: null,
    _method: "put",
  })
);

const validate = (fieldName) => {
  v$.value[fieldName].$touch();
};

const dynamicRules = computed(() => ({}));

const v$ = useVuelidate(dynamicRules, form);

const handleSubmit = async () => {
  v$.value.$touch();
  if (v$.value.$invalid) {
    return;
  }
  form.avatar = resetIfUrlObject(form.avatar);

  isSubmitting.value = true;
  errorMessage.value = "";

  try {
    const res = await form.post(route("update-profile"), {
      headers: { "Content-Type": "multipart/form-data" },
    });

    // Update user store with new data
    await store.setUser(res.data.data);

    notify.success({
      message: trans("profile_updated"),
    });
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      errorMessage.value = Object.values(errors).flat().join(", ");
    } else if (error.response?.data?.message) {
      errorMessage.value = trans(error.response.data.message);
    } else {
      errorMessage.value = trans("something_went_wrong");
    }
  } finally {
    isSubmitting.value = false;
  }
};
const resetIfUrlObject = (field) => {
  if (typeof field === "object" && /^https?:\/\//.test(field?.url)) {
    return null;
  }
  return field;
};
onMounted(() => {
  // Load current user data
  if (store.user) {
    form.full_name = store.user.full_name;
    form.email = store.user.email;
    form.language = store.user.language || "en";
    form.gender = store.user.gender || null;
    form.avatar = store.user.avatar_url
      ? { url: store.user.avatar_url, file_type: "image" }
      : null;
  }
});
</script>
