<template>
<svg xmlns="http://www.w3.org/2000/svg"
     width="100" height="60" viewBox="0 0 1000 600"
     role="img" aria-label="Flag of Bangladesh">
  <title>Flag of Bangladesh</title>
  <circle cx="450" cy="300" r="200" :fill="fill"/>
</svg>
</template>
<script setup>

defineProps({
  fill: {
    type: String,
    default: '#000000',
  },
});
</script>