<template>
    <div class="relative flex items-center">
        <SearchIcon class="absolute left-3 size-5" />
        <input
            :value="modelValue"
            @input="onInput"
            @keyup.enter="!disabled && emit('search', modelValue)"
            type="search"
            class="w-full sm:w-80 rounded-full bg-white border border-slate-200 pl-10 pr-3 py-2 outline-none focus:bg-white focus:ring-0 placeholder:text-[#c5cbc9]"
            :placeholder="$t(placeholder) || $t('Search…')"
            :aria-label="ariaLabel || placeholder || 'Search'"
            :readonly="disabled"
            :aria-disabled="disabled"
        />
    </div>
</template>

<script setup>
import SearchIcon from "@/components/icons/SearchIcon.vue";

defineProps({
    modelValue: { type: String, default: "" },
    placeholder: { type: String, default: "Search…" },
    ariaLabel: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
});
const emit = defineEmits(["update:modelValue", "search"]);

function onInput(e) {
    emit("update:modelValue", e.target.value);
}

</script>
