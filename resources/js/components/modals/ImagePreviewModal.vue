<template>
    <ModalBackDrop v-if="show">
        <!-- image wrapper -->
        <div class="relative inline-block">
            <!-- preview image -->
            <img :src="image" :class="[radius, width, height]" />

            <!-- close/remove button (relative to image) -->
            <button
                @click="$emit('remove')"
                class="absolute top-2 right-2 inline-flex items-center justify-center cursor-pointer rounded-full p-1"
            >
                <CrossCircleIcon class="h-6 w-6 text-red-600" />
            </button>
        </div>
    </ModalBackDrop>
</template>

<script setup>
import ModalBackDrop from "@/components/modals/ModalBackDrop.vue";
import CrossCircleIcon from "@/components/icons/CrossCircleIcon.vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    image: { type: String, default: "" },

    // ✅ make these valid Tailwind classes
    width: { type: String, default: "auto" },
    height: { type: String, default: "h-[500px]" },
    radius: { type: String, default: "rounded-[10px]" },
});

// emit an event so parent can remove the file
defineEmits(["remove"]);
</script>
