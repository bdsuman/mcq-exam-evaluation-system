<template>
    <div class="flex items-center gap-3">
        <div :class="readonly ? 'pointer-events-none' : 'cursor-pointer'" @click="toggle">
            <ToggleSwitchIcon :isOn="modelValue" />
        </div>
        <label v-if="label" class="text-sm font-medium text-[#002d45] cursor-pointer" @click="toggle">
            {{ label }}
        </label>
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
    label: {
        type: String,
        default: null,
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
