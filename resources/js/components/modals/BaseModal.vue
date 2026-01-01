<template>
    <ModalBackDrop v-if="show">
        <div
            class="bg-white rounded-[20px] inline-flex flex-col justify-start items-start gap-6 p-[24px]"
            :style="{ width: width + 'px' }"
        >
            <!-- Header -->
            <div
                class="self-stretch pb-6 border-b border-[#d9d9d9] inline-flex justify-between items-center"
            >
                <div
                    class="text-[#002d45] text-2xl font-semibold font-['Poppins'] leading-[31.20px]"
                >
                    {{ $t(title) }}
                </div>
                <div class="size-6 relative overflow-hidden cursor-pointer">
                    <CrossIcon @click="emit('close')" />
                </div>
            </div>

            <!-- Body slot -->
            <div class="flex flex-col justify-start items-start gap-6 w-full text-sm font-normal leading-tight overflow-y-auto overflow-x-hidden"  :style="{ height: height + 'px' }">
                <slot name="body" />
            </div>
        </div>
    </ModalBackDrop>
</template>
<script setup>
import ModalBackDrop from "@/components/modals/ModalBackDrop.vue";
import CrossIcon from "@/components/icons/CrossIcon.vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
    },
    width: {
        type: Number,
        default: 665, // default card width
    },
    height: {
        type: Number,
        default: 600, // default card height
    },
});

const emit = defineEmits(["close"]);
</script>
