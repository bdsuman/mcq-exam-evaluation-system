<template>
    <div :class="readonly ? 'pointer-events-none' : 'cursor-pointer'" @click="toggle">
        <ToggleSwitchIcon :isOn="modelValue" />
    </div>
</template>

<script setup>
import ToggleSwitchIcon from "@/components/icons/ToggleSwitchIcon.vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

function toggle() {
    if (!props.readonly) {
        emit("change", !props.modelValue);
        emit("update:modelValue", !props.modelValue);
    }
}
</script>
