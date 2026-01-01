<template>
    <div>
        <GoBack class="mt-2 mb-[22px]" />
        <div class="flex items-center justify-between">
            <div
                class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]"
            >
                {{ $t("Change Password") }}
            </div>
        </div>
        <div
            class="self-stretch p-8 bg-white rounded-2xl inline-flex flex-col items-end w-full !min-h-[calc(100vh-160px)] overflow-y-auto"
        >
            <div class="w-full">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="w-full inline-flex flex-col justify-start items-start gap-3">
                        <TextInput
                            label="current_password"
                            :placeholder="$t('enter_current_password')"
                            required
                            validationType="password"
                            v-model="form.current_password"
                            @blur="validate('current_password')"
                        />
                        <div
                            v-if="v$.current_password.$errors"
                            class="justify-start text-xs font-normal leading-none text-red-500"
                        >
                            {{ v$.current_password.$errors?.[0]?.$message }}
                        </div>
                    </div>

                    <div class="w-full inline-flex flex-col justify-start items-start gap-3">
                        <TextInput
                            label="password"
                            :placeholder="$t('enter_new_password')"
                            required
                            validationType="password"
                            v-model="form.password"
                            @blur="validate('password')"
                        />
                        <div
                            v-if="v$.password.$errors"
                            class="justify-start text-xs font-normal leading-none text-red-500"
                        >
                            {{ v$.password.$errors?.[0]?.$message }}
                        </div>
                    </div>

                    <div class="w-full inline-flex flex-col justify-start items-start gap-3">
                        <TextInput
                            label="password_confirmation"
                            :placeholder="$t('confirm_new_password')"
                            required
                            validationType="password"
                            v-model="form.password_confirmation"
                            @blur="validate('password_confirmation')"
                        />
                        <div
                            v-if="v$.password_confirmation.$errors"
                            class="justify-start text-xs font-normal leading-none text-red-500"
                        >
                            {{ v$.password_confirmation.$errors?.[0]?.$message }}
                        </div>
                    </div>

                    <div
                        v-if="errorMessage"
                        class="w-full p-3 bg-red-50 border border-red-200 rounded text-red-600 text-sm"
                    >
                        {{ errorMessage }}
                    </div>

                    <div
                        v-if="successMessage"
                        class="w-full p-3 bg-green-50 border border-green-200 rounded text-green-600 text-sm"
                    >
                        {{ successMessage }}
                    </div>

                    <div class="flex gap-4 pt-6">
                        <OutlineButton :show="true" title="reset" @click="resetForm" />
                        <Button
                            title="save"
                            :width="'w-40'"
                            @click="handleSubmit"
                            :disabled="isSubmitting"
                        />
                    </div>
                </form>
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
import Form from "vform";
import { useNotify } from "@/composables/useNotification";
import { computed, reactive, ref } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, minLength, helpers } from "@vuelidate/validators";
import { trans } from "laravel-vue-i18n";

const notify = useNotify();

const isSubmitting = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const form = reactive(
    new Form({
        current_password: "",
        password: "",
        password_confirmation: "",
    })
);

const validate = (fieldName) => {
    v$.value[fieldName].$touch();
};

const dynamicRules = computed(() => ({
    current_password: {

    },
    password: {
    },
    password_confirmation: {
        sameAs: helpers.withMessage(
            trans("passwords_do_not_match"),
            (value) => value === form.password
        ),
    },
}));

const v$ = useVuelidate(dynamicRules, form);

const resetForm = () => {
    form.current_password = "";
    form.password = "";
    form.password_confirmation = "";
    v$.value.$reset();
    errorMessage.value = "";
    successMessage.value = "";
};

const handleSubmit = async () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        await axios.put(route("change-password"), {
            current_password: form.current_password,
            password: form.password,
            password_confirmation: form.password_confirmation,
        });

        notify.success({ message: trans("password_changed") });

        // Clear form after successful change
        setTimeout(() => {
            resetForm();
        }, 2000);

    } catch (error) {
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            errorMessage.value = Object.values(errors)
                .flat()
                .join(", ");
        } else if (error.response?.data?.message) {
            errorMessage.value = trans(error.response.data.message);
        } else {
            errorMessage.value = trans("something_went_wrong");
        }

        notify.error({ message: errorMessage.value });

        // Auto-hide error message after 5 seconds
        setTimeout(() => {
            errorMessage.value = "";
        }, 5000);
    } finally {
        isSubmitting.value = false;
    }
};
</script>
