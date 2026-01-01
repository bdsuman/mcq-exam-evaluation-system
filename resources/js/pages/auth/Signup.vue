<template>
  <GuestLayout>
    <div
      class="p-10 bg-white rounded-3xl inline-flex flex-col justify-center items-center gap-14 z-50">
      <div class="self-stretch flex flex-col justify-start items-center gap-5">
        <div
          class="self-stretch text-center justify-start text-[#002d45] text-[32px] font-semibold capitalize">
          {{ $t("Sign Up") }}
        </div>
      </div>
      <form
        @submit.prevent="handleSignup"
        class="w-[400.46px] grid grid-cols-1 gap-4"
        autocomplete="off">
        <TextInput
          label="full_name"
          placeholder="enter_full_name"
          required
          validationType="text"
          v-model="form.full_name"
          :isTextarea="false" />
        <TextInput
          label="Email"
          placeholder="Enter email"
          required
          validationType="email"
          v-model="form.email"
          :isTextarea="false" />
        <TextInput
          label="Password"
          placeholder="Enter password"
          required
          validationType="password"
          v-model="form.password"
          :isTextarea="false" />
        <TextInput
          label="confirm_password"
          placeholder="confirm_password"
          required
          validationType="password"
          v-model="form.password_confirmation"
          :isTextarea="false" />
        <div class="text-xs font-normal text-red-500" v-if="errorMessage">
          {{ errorMessage }}
        </div>
        <Button
          :show="true"
          title="Sign Up"
          :width="'w-full'"
          :disabled="isDisabled"
          :class="{ 'opacity-50 !cursor-not-allowed': isDisabled }" />
        <div class="text-center text-sm text-gray-600">
          {{ $t("Already have an account?") }}
          <router-link
            to="/login"
            class="text-primary hover:text-primary-hover font-semibold">
            {{ $t("Login") }}
          </router-link>
        </div>
      </form>
    </div>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/layouts/GuestLayout.vue";
import TextInput from "@/components/form/TextInput.vue";
import Button from "@/components/buttons/Button.vue";

/* import starts */
import { ref, reactive, computed } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "@/stores/useUserStore";
import { trans } from "laravel-vue-i18n";
import Form from "vform";
/* import ends */

/* initialization starts */
const router = useRouter();
const store = useUserStore();

const form = reactive(
  new Form({
    full_name: "",
    email: "",
    password: "",
    password_confirmation: "",
  })
);
let error = ref("");
let errorMessage = ref("");
let fromError = ref(false);

const isDisabled = computed(() => {
  return (
    form.full_name.trim() === "" ||
    form.email.trim() === "" ||
    form.password.trim() === "" ||
    form.password_confirmation.trim() === ""
  );
});

const handleSignup = async () => {
  if (isDisabled.value) {
    return;
  }

  // Check if passwords match
  if (form.password !== form.password_confirmation) {
    errorMessage.value = trans("passwords_do_not_match");
    setTimeout(() => {
      errorMessage.value = "";
    }, 5000);
    return;
  }

  try {
    await store.register(
      form.full_name,
      form.email,
      form.password,
      form.password_confirmation
    );
    router.push({ name: "home" });
  } catch (error) {
    // Check for specific error messages
    if (error.response?.data?.message === "email_already_exists") {
      errorMessage.value = trans("Email already exists!");
    } else if (error.response?.data?.errors) {
      // Handle validation errors
      const errors = error.response.data.errors;
      const firstError = Object.values(errors)[0];
      errorMessage.value = Array.isArray(firstError)
        ? firstError[0]
        : firstError;
    } else {
      errorMessage.value = trans("Registration failed! Please try again.");
    }

    setTimeout(() => {
      errorMessage.value = "";
    }, 5000);

    fromError.value = true;
  }
};
</script>
