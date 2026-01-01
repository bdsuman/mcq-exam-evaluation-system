<template>
    <div class="inline-block">
        <div v-if="label" class="mb-3 size-lf-stretch justify-start text-[#585b5a] text-sm font-normal leading-[18.20px]">
            {{ $t(label) }}
        </div>

        <div class="relative inline-block">
            <img
                :src="src"
                class="h-[64px] max-h-[64px] rounded-[10px] cursor-pointer"
                @click="openPreview"
            />

            <FileExpandIcon
                v-if="showIcon"
                class="absolute bottom-1 right-1 h-4 w-4 text-white bg-opacity-50 p-[2px] rounded-full cursor-pointer"
                @click.stop="openPreview"
            />
        </div>
        <ImagePreviewModal
            :show="showModal"
            :image="src"
            @remove="closePreview"
        />
    </div>
</template>

<script setup>
import { ref, defineEmits } from "vue";
import ImagePreviewModal from "@/components/modals/ImagePreviewModal.vue";
import FileExpandIcon from "@/components/icons/FileExpandIcon.vue";

const props = defineProps({
    src: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: "",
    },
    showIcon: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["open", "close"]);

const showModal = ref(false);

function openPreview() {
    showModal.value = true;
    emit("open", props.src);
}

function closePreview() {
    showModal.value = false;
    emit("close", props.src);
}
</script>
