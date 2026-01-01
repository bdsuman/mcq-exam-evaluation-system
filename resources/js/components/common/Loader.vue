<template>
	<div v-if="isLoading" class="fixed top-0 w-full h-full z-100 flex justify-center items-center backdrop-blur fill-indigo-500 drop-shadow-lg drop-shadow-indigo-900">
		<LogoFull class="!h-12" fill="grey" />
	</div>
</template>

<script setup>
import { isLoading } from "@/globalLoader";
import LogoFull from "@/components/common/Logo.vue";
</script>
