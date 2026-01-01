<template>
  <div class="w-full inline-flex flex-col justify-start items-start gap-3">
    <!-- Label -->
    <div
      class="self-stretch justify-start text-[#002d45] text-sm font-medium leading-[18.20px]"
    >
      {{ $t(label) }}
      <span v-if="bracketText" class="text-[#3C95DE]">({{ $t(bracketText) }})</span>
      <span v-if="required">*</span>
    </div>

    <!-- Input -->
    <div
      v-if="!isTextarea"
      class="self-stretch px-4 bg-[#f3f6fa] rounded-2xl inline-flex justify-between items-center gap-2"
      :class="[
        { 'border-1 border-red-500': $v.model.$error && !$v.model.$pending },
        {
          'opacity-50 cursor-not-allowed': disabled,
        },
      ]"
    >
      <input
        :type="
          isPasswordToggle ? (showPassword ? 'text' : 'password') : inputType
        "
        class="flex-1 bg-transparent text-[#002d45] placeholder:text-[#7b7e7d] text-sm font-normal leading-[18.20px] outline-none border-0 ring-0 p-0 h-[60px]"
        v-model="$v.model.$model"
        @blur="$v.$touch"
        :placeholder="$t(placeholder)"
        :maxlength="props.maxLength || null"
        :readonly="readonly"
        :disabled="disabled"
      />
      <button
        v-if="isPasswordToggle"
        type="button"
        @click="togglePassword"
        class="focus:outline-none flex-shrink-0"
      >
        <EyeOpen v-if="showPassword" />
        <EyeClose v-else />
      </button>
    </div>

    <!-- Textarea -->
    <div
      v-else
      class="self-stretch px-4 bg-[#f3f6fa] rounded-2xl inline-flex justify-between items-center gap-2"
      :class="[
        { 'border-1 border-red-500': $v.model.$error && !$v.model.$pending },
        {
          'opacity-50 cursor-not-allowed': disabled,
        },
      ]"
    >
      <textarea
        ref="textarea"
        class="w-full bg-transparent text-[#7b7e7d] placeholder:text-[#7b7e7d] text-sm font-normal leading-[18.20px] outline-none border-0 ring-0 p-0 resize-none overflow-auto py-4"
        v-model="$v.model.$model"
        @input="autoResize"
        @blur="$v.$touch"
        :placeholder="placeholder"
        :maxlength="props.maxLength || null"
        :style="{ minHeight: '97px', maxHeight: '97px' }"
        :readonly="readonly"
        :disabled="disabled"
      />
    </div>
    <div class="justify-start text-[#c5cbc9] text-xs font-normal leading-none" v-if="helpText">{{ helpText }}</div>

    <!-- Validation errors (generic & message-first) -->
    <div
      v-if="$v.model.$dirty && ($v.model.$error || $v.model.$pending)"
      class="justify-start text-xs font-normal leading-none"
    >
      <!-- <div v-if="$v.model.$pending" class="text-gray-500 animate-pulse">
                {{ $t("Validating...") }}
            </div>
            <template v-else> -->
      <div v-for="err in $v.model.$errors" :key="err.$uid" class="text-red-500">
        {{ fallbackMessage(err) }}
      </div>
      <!-- </template> -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { useVuelidate } from "@vuelidate/core";
import {
  required as vRequired,
  minLength as vMinLength,
  maxLength as vMaxLength,
  email as vEmail,
  numeric as vNumeric,
  url as vUrl,
  helpers,
} from "@vuelidate/validators";
import EyeClose from "@/components/icons/EyeClose.vue";
import EyeOpen from "@/components/icons/EyeOpen.vue";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
  label: { type: String, required: true },
  placeholder: { type: String, default: "" },
  modelValue: { type: [String, Number], default: "" },
  required: { type: Boolean, default: false },
  isTextarea: { type: Boolean, default: false },
  readonly: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  validationType: { type: String, default: "text" }, // 'text' | 'email' | 'number' | 'url' | 'password' | 'regex'
  minLength: { type: Number, default: null },
  maxLength: { type: Number, default: null },
  customRegex: { type: [RegExp, String], default: null },
  customMessage: { type: String, default: "" },
  bracketText: { type: String, required: false },
  helpText: { type: String, required: false, default: "" },
  /**
   * Accept extra/custom validators to merge with the built-ins.
   * Shape: { ruleName: validatorFn } OR a factory: (ctx) => ({ ... })
   * where ctx = { helpers, validators, props }
   */
  rules: { type: [Object, Function], default: () => ({}) },
});

const emit = defineEmits(["update:modelValue", "update:valid"]);
const model = ref(props.modelValue);
watch(
  () => props.modelValue,
  (val) => {
    if (val !== model.value) model.value = val;
  }
);
watch(model, (val) => emit("update:modelValue", val));

// Eye toggle
const showPassword = ref(false);
const togglePassword = () => (showPassword.value = !showPassword.value);
const isPasswordToggle = computed(() => props.validationType === "password");

// Input type
const inputType = computed(() => {
  if (props.isTextarea) return "text";
  switch (props.validationType) {
    case "email":
      return "email";
    case "number":
      return "text"; // keep numeric validation, but allow +/- etc.
    case "url":
      return "url";
    case "password":
      return "password";
    default:
      return "text";
  }
});

// Built-in rules from props
const baseRules = computed(() => {
  const r = {};
  if (props.required) r.required = vRequired;
  if (typeof props.minLength === "number" && props.minLength >= 0)
    r.minLength = vMinLength(props.minLength);
  if (typeof props.maxLength === "number" && props.maxLength >= 0)
    r.maxLength = vMaxLength(props.maxLength);

  switch (props.validationType) {
    case "email":
      r.email = vEmail;
      break;
    case "number":
      r.numeric = vNumeric;
      break;
    case "url":
      r.url = vUrl;
      break;
    case "regex":
      if (props.customRegex) {
        const re =
          props.customRegex instanceof RegExp
            ? props.customRegex
            : new RegExp(props.customRegex);
        // allow custom message override for regex, but still compatible with $errors loop
        r.custom = props.customMessage
          ? helpers.withMessage(
              props.customMessage,
              helpers.regex("custom", re)
            )
          : helpers.regex("custom", re);
      }
      break;
  }
  return r;
});

// Normalize and merge external rules
const externalRules = computed(() => {
  if (typeof props.rules === "function") {
    return (
      props.rules({
        helpers,
        validators: {
          vRequired,
          vMinLength,
          vMaxLength,
          vEmail,
          vNumeric,
          vUrl,
        },
        props,
      }) || {}
    );
  }
  return props.rules || {};
});

const validationRules = computed(() => ({
  model: { ...baseRules.value, ...externalRules.value },
}));

const $v = useVuelidate(validationRules, { model });

// Expose for parent (optional but handy)
defineExpose({
  validate: () => $v.value.$validate(),
  reset: () => $v.value.$reset(),
});

// Keep parent in sync with validity if desired
watch(
  () => $v.value.model.$invalid,
  (inv) => emit("update:valid", !inv),
  { immediate: true }
);

// Textarea auto-resize
const textarea = ref(null);
const autoResize = () => {
  if (!textarea.value) return;
  textarea.value.style.height = "auto";
  const newHeight = Math.min(textarea.value.scrollHeight, 97);
  textarea.value.style.height = newHeight + "px";
  textarea.value.style.overflowY =
    textarea.value.scrollHeight > 97 ? "auto" : "hidden";
};
onMounted(() => {
  if (props.isTextarea && textarea.value) autoResize();
});
watch(model, () => {
  if (props.isTextarea && textarea.value) autoResize();
});

// Fallback messages for built-ins if validator didn’t set $message
function fallbackMessage(err) {
  const t = (s) => trans(s); // or use $t if you want translations here too
  switch (err.$validator) {
    case "required":
      return t("This field cannot be empty");
    case "minLength":
      return trans("Minimum :min Characters", { min: props.minLength });
    case "maxLength":
      return trans("Maximum :max Characters", { max: props.maxLength });
    case "email":
      return t("Invalid email address");
    case "password":
      return t("Invalid password");
    case "numeric":
      return t("Only numbers are allowed");
    case "url":
      return t("Invalid URL");
    case "custom":
      return props.customMessage || t("Invalid format");
    default:
      return t("Invalid value");
  }
}
</script>
