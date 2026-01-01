<template>
	<div
		class="w-full min-h-screen relative bg-gradient-to-b from-[#005766] to-[#3ab57c] overflow-hidden"
	>
		<div
			class="size-[497.34px] left-[102.07px] top-[74.91px] absolute bg-[#4fc681] rounded-full blur-[150px]"
		></div>
		<div
			class="size-[322.56px] left-[948.08px] top-[610.12px] absolute bg-[#74da9f] rounded-full blur-[125px]"
		></div>

		<!-- Centered Slot -->
		<div class="flex items-center justify-center min-h-screen">
			<slot></slot>
		</div>

		<GlobalLoader />
	</div>
</template>

<script setup>
import GlobalLoader from "@/components/common/Loader.vue";
</script>
