<template>
  <GuestLayout>
    <div
      class="p-10 bg-white rounded-3xl inline-flex flex-col justify-center items-center gap-14 z-50">
      <div class="self-stretch flex flex-col justify-start items-center gap-5">
        <div
          class="self-stretch text-center justify-start text-[#002d45] text-[32px] font-semibold capitalize">
          {{ $t("Log In") }}
        </div>
      </div>
      <form
        @submit.prevent="handleLogin"
        class="w-[400.46px] grid grid-cols-1 gap-4"
        autocomplete="off">
        <TextInput
          label="Email"
          placeholder="Enter email"
          required
          validationType="email"
          v-model="form.email"
          :isTextarea="false" />
        <TextInput
          label="password"
          placeholder="enter_password"
          required
          validationType="password"
          v-model="form.password"
          :isTextarea="false" />
        <div class="text-xs font-normal text-red-500" v-if="errorMessage">
          {{ errorMessage }}
        </div>
        <Button
          :show="true"
          title="Login"
          :width="'w-full'"
          :disabled="isDisabled"
          :class="{ 'opacity-50 !cursor-not-allowed': isDisabled }" />
        <div class="text-center text-sm text-gray-600">
          {{ $t("Dont have an account?") }}
          <router-link to="/signup" class="text-primary hover:text-primary-hover font-semibold">
            {{ $t("Sign Up") }}
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
    email: "",
    password: "",
  })
);
let error = ref("");
let errorMessage = ref("");
let fromError = ref(false);
const isDisabled = computed(() => {
  return form.email.trim() === "" || form.password.trim() === "";
});

const showPassword = ref(false);

const handleLogin = async () => {
  if (isDisabled.value) {
    return;
  }

  try {
    await store.login(form.email, form.password);
    router.push({ name: "home" });
  } catch (error) {
    errorMessage.value = trans("Wrong credentials!");

    setTimeout(() => {
      errorMessage.value = ""; // clear after 5 seconds
    }, 5000);

    fromError.value = true;
  }
};
</script>
