<template>
	<svg v-if="collapsed" xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
		<path d="M20.6926 4.99884H8.69263" stroke="#005766" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		<path d="M20.6926 11.9988H4.69263" stroke="#005766" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		<path d="M20.6926 18.9988H12.6926" stroke="#005766" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
	</svg>
	<svg v-else xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
		<path d="M4.84961 5.05029H16.8496" stroke="#005766" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		<path d="M4.84961 12.0503H20.8496" stroke="#005766" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		<path d="M4.84961 19.0503H12.8496" stroke="#005766" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
	</svg>
</template>

<script setup>
defineProps({
	collapsed: Boolean,
});
</script>
