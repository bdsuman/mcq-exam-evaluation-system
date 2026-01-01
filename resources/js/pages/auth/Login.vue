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
        <div class="flex items-center gap-3 text-gray-400 text-xs">
          <span class="h-px bg-gray-200 w-full"></span>
          <span class="uppercase">{{ $t("or") }}</span>
          <span class="h-px bg-gray-200 w-full"></span>
        </div>
        <button
          type="button"
          @click="startGoogleLogin"
          :disabled="googleLoading"
          class="w-full h-[56px] flex items-center justify-center gap-3 border border-gray-200 rounded-2xl bg-white text-gray-800 hover:shadow-sm transition disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
          <svg
            aria-hidden="true"
            focusable="false"
            class="w-5 h-5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M23.5 12.27c0-.82-.07-1.64-.22-2.44H12v4.63h6.46a5.52 5.52 0 0 1-2.39 3.62v2.99h3.86c2.26-2.08 3.57-5.15 3.57-8.8Z"
              fill="#4285F4" />
            <path
              d="M12 24c3.24 0 5.97-1.07 7.96-2.93l-3.86-2.99c-1.07.73-2.45 1.15-4.1 1.15-3.16 0-5.83-2.13-6.79-4.99H1.23v3.13A12 12 0 0 0 12 24Z"
              fill="#34A853" />
            <path
              d="M5.21 14.23A7.18 7.18 0 0 1 4.83 12c0-.77.13-1.52.38-2.23V6.64H1.23A12 12 0 0 0 0 12c0 1.94.46 3.77 1.23 5.36l3.98-3.13Z"
              fill="#FBBC05" />
            <path
              d="M12 4.75c1.77 0 3.36.61 4.61 1.81l3.45-3.45C17.95 1.12 15.22 0 12 0 7.31 0 3.3 2.69 1.23 6.64l3.98 3.13C6.17 6.88 8.84 4.75 12 4.75Z"
              fill="#EA4335" />
          </svg>
          <span class="text-sm font-medium">
            {{
              googleLoading
                ? $t("Signing in with Google...")
                : $t("Continue with Google")
            }}
          </span>
        </button>
        <div class="text-center text-sm text-gray-600">
          {{ $t("Dont have an account?") }}
          <router-link
            to="/signup"
            class="text-primary hover:text-primary-hover font-semibold">
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
import { ref, reactive, computed, onMounted, onBeforeUnmount } from "vue";
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
let errorMessage = ref("");
const isDisabled = computed(() => {
  return form.email.trim() === "" || form.password.trim() === "";
});
const googleLoading = ref(false);
const googleInitialized = ref(false);
const GOOGLE_CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID;
const GOOGLE_SCRIPT_ID = "google-identity-services";
let googleTokenClient = null;

const loadGoogleScript = () => {
  return new Promise((resolve, reject) => {
    if (document.getElementById(GOOGLE_SCRIPT_ID)) {
      resolve();
      return;
    }

    const script = document.createElement("script");
    script.src = "https://accounts.google.com/gsi/client";
    script.async = true;
    script.defer = true;
    script.id = GOOGLE_SCRIPT_ID;
    script.onload = () => resolve();
    script.onerror = () => reject(new Error("Failed to load Google SDK"));
    document.head.appendChild(script);
  });
};

const handleGoogleCallback = async (tokenResponse) => {
  const token = tokenResponse?.access_token || tokenResponse?.accessToken;

  if (!token) {
    errorMessage.value = trans("Google login failed. Please try again.");
    setTimeout(() => {
      errorMessage.value = "";
    }, 5000);
    googleLoading.value = false;
    return;
  }

  try {
    await store.googleLogin(token, localStorage.getItem("language") || "en");
    router.push({ name: "home" });
  } catch (err) {
    console.error("Google login error", err);
    errorMessage.value = trans("Google login failed. Please try again.");

    setTimeout(() => {
      errorMessage.value = "";
    }, 5000);
  } finally {
    googleLoading.value = false;
  }
};

const initGoogleClient = async () => {
  if (googleInitialized.value || !GOOGLE_CLIENT_ID) return;

  try {
    await loadGoogleScript();

    if (window.google?.accounts?.oauth2) {
      googleTokenClient = window.google.accounts.oauth2.initTokenClient({
        client_id: GOOGLE_CLIENT_ID,
        scope: "openid email profile",
        callback: handleGoogleCallback,
      });

      googleInitialized.value = true;
    }
  } catch (err) {
    console.error("Failed to initialize Google auth", err);
  }
};

const startGoogleLogin = async () => {
  errorMessage.value = "";
  console.log(import.meta.env);

  console.log("GOOGLE_CLIENT_ID:", GOOGLE_CLIENT_ID);
  if (!GOOGLE_CLIENT_ID) {
    errorMessage.value = trans("Google login is not configured.");

    setTimeout(() => {
      errorMessage.value = "";
    }, 5000);

    return;
  }

  googleLoading.value = true;

  await initGoogleClient();

  if (!googleTokenClient) {
    errorMessage.value = trans("Google login is temporarily unavailable.");
    googleLoading.value = false;

    setTimeout(() => {
      errorMessage.value = "";
    }, 5000);

    return;
  }

  try {
    googleTokenClient.requestAccessToken({ prompt: "consent" });
  } catch (err) {
    console.error("Google login request failed", err);
    googleLoading.value = false;
    errorMessage.value = trans("Google login failed. Please try again.");

    setTimeout(() => {
      errorMessage.value = "";
    }, 5000);
  }
};

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
  }
};

onMounted(() => {
  initGoogleClient();
});

onBeforeUnmount(() => {
  try {
    window.google?.accounts?.id?.cancel();
  } catch (_) {
    // ignore cleanup errors
  }
});
</script>
