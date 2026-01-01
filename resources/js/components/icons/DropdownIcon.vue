<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="13"
        height="8"
        viewBox="0 0 13 8"
        fill="none"
        :class="[{ 'rotate-180': isOpen }, 'transition-transform duration-300']"
    >
        <path
            d="M11.5 1.50004C11.5 1.50004 7.81758 6.5 6.5 6.5C5.18233 6.5 1.5 1.5 1.5 1.5"
            stroke="#005766"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>

<script setup>
const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});
</script>
