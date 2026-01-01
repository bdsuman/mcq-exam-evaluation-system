<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    :stroke="stroke"
    :fill="fill"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
    class="w-full h-full"
  >
    <rect x="3.5" y="4" width="17" height="16" rx="2.5" ry="2.5" :fill="fill === 'none' ? 'none' : fill" :stroke="stroke || currentColor" />
    <path d="M7.5 9.75h6" />
    <path d="M7.5 13.25h3.5" />
    <circle cx="16" cy="9.75" r="0.9" :fill="stroke || '#8BEE82'" />
    <path d="M15.2 12.7l0.95 1.05 1.9-2.1" :stroke="stroke || '#8BEE82'" />
  </svg>
</template>

<script setup>
defineProps({
  stroke: {
    type: String,
    default: "", // outline color
  },
  fill: {
    type: String,
    default: "none", // allow solid if desired
  },
});
</script>
