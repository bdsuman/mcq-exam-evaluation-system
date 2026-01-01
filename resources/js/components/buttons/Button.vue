<template>
    <!-- Router Link -->
    <router-link
        v-if="to"
        :to="to"
        :aria-label="title"
        :disabled="disabled"
        v-bind="attrs"
        class="cursor-pointer inline-flex justify-center items-center rounded-2xl font-medium transition duration-200 focus:outline-none focus:ring-0"
        :class="[
            bgClass,
            width,
            height,
            hoverClass,
            textColor,
            extraClasses,
            disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '',
        ]"
    >
        <!-- Icon -->
        <span v-if="icon" class="flex-shrink-0">
            <component :is="icon" class="w-5 h-5" />
        </span>
        <!-- Label -->
        <span class="truncate">{{ $t(title) }}</span>
    </router-link>

    <!-- Button -->
    <button
        v-else
        :aria-label="title"
        :disabled="disabled"
        v-bind="attrs"
        class="cursor-pointer inline-flex justify-center items-center rounded-2xl font-medium transition duration-200 focus:outline-none focus:ring-0"
        :class="[
            bgClass,
            width,
            height,
            hoverClass,
            textColor,
            extraClasses,
            disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '',
        ]"
        @click="$emit('click')"
    >
        <!-- Icon -->
        <span v-if="icon" class="flex-shrink-0">
            <component :is="icon" class="w-5 h-5" />
        </span>
        <!-- Label -->
        <span class="truncate">{{ $t(title) }}</span>
    </button>
</template>

<script setup>
import { useAttrs } from "vue";

const attrs = useAttrs();

const props = defineProps({
    show: { type: Boolean, default: true },
    title: { type: String, default: "Button" },
    bgClass: {
        type: String,
        default: "bg-gradient-to-r from-[#005766] to-[#3ab57c]",
    },
    width: { type: String, default: "w-[260px]" },
    height: { type: String, default: "h-[56px]" },
    hoverClass: { type: String, default: "hover:opacity-90 hover:shadow-lg" },
    textColor: { type: String, default: "text-white" },
    extraClasses: { type: String, default: "p-2 gap-2" },
    disabled: { type: Boolean, default: false },
    to: { type: [String, Object], default: null },
    icon: { type: [String, Object, Function], default: null },
});

defineEmits(["click"]);
</script>
