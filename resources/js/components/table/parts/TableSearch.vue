<template>
    <SearchBar
        :modelValue="modelValue"
        :placeholder="placeholder"
        :aria-label="placeholder"
        :disabled="disabled"
        @update:modelValue="onInput"
        @search="() => $emit('commit', modelValue)"
    />
</template>

<script setup>
import { onBeforeUnmount } from "vue";
import SearchBar from "@/components/table/SearchBar.vue";

const props = defineProps({
    modelValue: { type: String, default: "" },
    placeholder: { type: String, default: "Search…" },
    disabled: { type: Boolean, default: false },
    debounce: { type: Number, default: 300 },
});
const emit = defineEmits(["update:modelValue", "debounced-change", "commit"]);

let t;
onBeforeUnmount(() => clearTimeout(t));

function onInput(v) {
    emit("update:modelValue", v ?? "");
    clearTimeout(t);
    t = setTimeout(() => emit("debounced-change", v ?? ""), props.debounce);
}

</script>