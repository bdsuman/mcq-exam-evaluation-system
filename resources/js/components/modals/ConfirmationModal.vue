<template>
    <ModalBackDrop v-if="show">
        <div
            class="w-[423px] p-6 bg-white rounded-[20px] inline-flex flex-col justify-start items-start gap-6"
        >
            <div class="self-stretch inline-flex justify-between items-center">
                <div
                    class="justify-start text-[#002d45] text-2xl font-semibold leading-[31.20px]"
                >
                    {{ $t(title) }}
                </div>
                <div class="size-6 relative overflow-hidden cursor-pointer">
                    <CrossIcon @click="emit('close')" />
                </div>
            </div>
            <div class="size- flex flex-col justify-start items-start gap-6">
                <div
                    class="w-[377.78px] justify-start text-[#002d45] text-base font-normal leading-tight"
                >
                    {{ $t(message) }}
                </div>
                <div class="self-stretch w-full">
                    <div
                        class="inline-flex justify-center items-end gap-[15px]"
                    >
                        <OutlineButton
                            :show="true"
                            :title="cancelText || 'Cancel'"
                            :width="'w-45'"
                            :height="'h-[48px]'"
                            @click="emit('close')"
                        />
                        <Button
                            :show="true"
                            :title="confirmText || 'Confirm'"
                            :width="'w-45'"
                            :height="'h-[48px]'"
                            :bgClass="'bg-[#005766]'"
                            @click="emit('confirm')"
                        />
                    </div>
                </div>
            </div>
        </div>
    </ModalBackDrop>
</template>
<script setup>
import ModalBackDrop from "@/components/modals/ModalBackDrop.vue";
import CrossIcon from "@/components/icons/CrossIcon.vue";
import Button from "@/components/buttons/Button.vue";
import OutlineButton from "@/components/buttons/OutlineButton.vue";

defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmText: String,
    cancelText: String,
});

const emit = defineEmits(["close", "confirm"]);
</script>
