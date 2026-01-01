<template>
    <div
        class="self-stretch h-14 py-3 flex justify-between items-center border rounded-xl p-2 text-sm leading-tight"
        :style="{ borderColor: borderColor }"
    >
        <div :style="{ color: labelColor }" class="font-normal">
            {{ $t(label) }}
        </div>
        <div :style="{ color: statusColor }" class="font-medium">
            {{ $t(status) }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    status: String,
    borderColor: { type: String, default: "#C5CBC9" },
    labelColor: { type: String, default: "#002D45" },
    statusColor: { type: String, default: "#2B8C5F" },
});
</script>
