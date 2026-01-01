<template>
    <div
        v-if="modelValue"
        class="fixed top-0 left-0 w-screen h-screen z-50 flex items-center justify-center bg-black/30"
    >
        <div
            class="w-full max-w-md bg-white rounded-lg shadow-lg p-5 border border-gray-200"
        >
            <div class="flex items-center justify-between mb-4">
                <h3
                    class="text-[#454545] text-lg font-semibold"
                    v-trans="title"
                ></h3>
                <CloseIcon class="cursor-pointer" @click="closeModal" />
            </div>

            <div class="text-[#919fa1] text-sm mb-4" v-trans="text"></div>
            <slot name="body"></slot>

            <div class="flex justify-end gap-2.5">
                <button
                    class="w-24 h-9 text-[#6D6D6D] font-semibold rounded-lg border border-[#6D6D6D]"
                    @click.prevent="onNoClick"
                >
                    {{ $t("No") }}
                </button>
                <button
                    class="w-24 h-9 bg-[#E73238] text-white font-semibold rounded-lg"
                    @click.prevent="onYesClick"
                >
                    {{ $t("Yes") }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";
import CloseIcon from "@/components/icons/modal/CloseIcon.vue";

const props = defineProps({
    modelValue: Boolean,
    title: [String, Object],
    text: [String, Object],
});

const emit = defineEmits(["yesClick", "noClick", "update:modelValue"]);

const closeModal = () => emit("update:modelValue", false);
const onYesClick = () => {
    emit("yesClick");
    closeModal();
};
const onNoClick = () => {
    emit("noClick");
    closeModal();
};
</script>
