<template>
    <div :class="classes" @click="handleClick">
        {{ $t(label) }}
    </div>
</template>
<script setup>
import { computed } from "vue";

const props = defineProps({
    label: {
        type: String,
        default: "--",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["click"]);

const handleClick = () => {
    if (!props.disabled) {
        emit("click");
    }
};

const classes = computed(() =>
    [
        "font-medium text-sm leading-[130%] tracking-[0] underline underline-offset-4 decoration-solid [text-decoration-skip-ink:auto]",
        props.disabled
            ? "text-gray-400 cursor-not-allowed no-underline"
            : "cursor-pointer text-emerald-700 hover:text-emerald-900",
    ].join(" ")
);
</script>
