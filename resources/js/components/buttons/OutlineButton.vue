<template>
    <!-- Render as router-link if 'to' is provided -->
    <router-link
        v-if="to"
        :to="to"
        :aria-label="title"
        :disabled="disabled"
        class="cursor-pointer inline-flex justify-center items-center rounded-2xl font-medium transition duration-200 focus:ring-0"
        :class="[
            width,
            height,
            outlineClass,
            textColor,
            extraClasses,
            hoverClass,
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

    <!-- Render as a button if 'to' is not provided -->
    <button
        v-if="show && !to"
        :aria-label="title"
        :disabled="disabled"
        class="cursor-pointer inline-flex justify-center items-center rounded-2xl font-medium transition duration-200 focus:ring-0"
        :class="[
            width,
            height,
            outlineClass,
            textColor,
            extraClasses,
            hoverClass,
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
const props = defineProps({
    show: { type: Boolean, default: true },
    title: { type: String, default: "Cancel" },
    width: { type: String, default: "w-40" },
    height: { type: String, default: "h-14" },
    outlineClass: {
        type: String,
        default: "outline outline-1 outline-offset-[-1px] outline-[#005766]",
    },
    textColor: { type: String, default: "text-[#005766]" },
    extraClasses: { type: String, default: "p-2 gap-2" },
    disabled: { type: Boolean, default: false },
    hoverClass: { type: String, default: "hover:opacity-90 hover:shadow-lg" },
    to: { type: [String, Object], default: null }, // router-link support
    icon: { type: [String, Object, Function], default: null }, // 👈 new icon prop
});

defineEmits(["click"]);
</script>
