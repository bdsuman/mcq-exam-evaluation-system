<template>
    <div class="flex items-center" v-if="show">
        <ToolTipArrowIcon />
        <div class="h-[30px] px-3 py-1 ms-[-2px] bg-[#3F8B77] rounded-md shadow justify-center items-center gap-2 inline-flex">
            <div class="text-white text-sm leading-tight">{{ $t(label) }}</div>
        </div>
    </div>
</template>
<script setup>
import ToolTipArrowIcon from "@/components/icons/sidebar/ToolTipArrowIcon.vue";

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: 'false',
    }
});
</script>
