<template>
    <svg
        v-if="modelValue"
        class="cursor-pointer"
        xmlns="http://www.w3.org/2000/svg"
        width="33"
        height="32"
        viewBox="0 0 33 32"
        fill="none"
    >
        <path
            d="M21.8334 8H11.1667C6.74841 8 3.16669 11.5817 3.16669 16C3.16669 20.4183 6.74841 24 11.1667 24H21.8334C26.2516 24 29.8334 20.4183 29.8334 16C29.8334 11.5817 26.2516 8 21.8334 8Z"
            fill="#3AB57C"
        />
        <path
            d="M25.8333 16C25.8333 18.2092 24.0425 20 21.8333 20C19.6241 20 17.8333 18.2092 17.8333 16C17.8333 13.7908 19.6241 12 21.8333 12C24.0425 12 25.8333 13.7908 25.8333 16Z"
            fill="white"
        />
    </svg>
    <svg
        v-else
        class="cursor-pointer"
        xmlns="http://www.w3.org/2000/svg"
        width="33"
        height="32"
        viewBox="0 0 33 32"
        fill="none"
    >
        <path
            d="M11.1666 8H21.8333C26.2516 8 29.8333 11.5817 29.8333 16C29.8333 20.4183 26.2516 24 21.8333 24H11.1666C6.74838 24 3.16665 20.4183 3.16665 16C3.16665 11.5817 6.74838 8 11.1666 8Z"
            fill="#7B7E7D"
        />
        <path
            d="M7.16669 16C7.16669 18.2092 8.95749 20 11.1667 20C13.3759 20 15.1667 18.2092 15.1667 16C15.1667 13.7908 13.3759 12 11.1667 12C8.95749 12 7.16669 13.7908 7.16669 16Z"
            fill="white"
        />
    </svg>
</template>
<script setup>
import { ref } from "vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const toggle = () => {
    emit("update:modelValue", !props.modelValue);
};
</script>