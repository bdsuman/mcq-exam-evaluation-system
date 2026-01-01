<template>
    <div
        class="px-1.5 py-1 rounded-sm outline outline-1 outline-offset-[-1px] inline-flex justify-center items-center gap-2"
        :class="badgeClasses"
    >
        <div
            class="justify-start text-sm font-normal leading-[18.20px]"
            :class="textClasses"
        >
            {{ $t(label) }}
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

// Literal class map (keeps Tailwind from purging these)
const STYLES = {
    success: {
        badge: "bg-[#22c55e]/10 outline-[#22c55e]",
        text: "text-[#22c55e]",
    },
    warning: {
        badge: "bg-[#facc15]/10 outline-[#facc15]",
        text: "text-[#facc15]",
    },
    info: {
        badge: "bg-[#3b82f6]/10 outline-[#3b82f6]",
        text: "text-[#3b82f6]",
    },
    active: {
        badge: "bg-[#4ade80]/10 outline-[#4ade80]",
        text: "text-[#4ade80]",
    },
    inactive: {
        badge: "bg-[#e2e8f0]/10 outline-[#e2e8f0]",
        text: "text-[#9bafca]",
    },
    expired: {
        badge: "bg-[#eab308]/10 outline-[#eab308]",
        text: "text-[#eab308]",
    },
    completed: {
        badge: "bg-[#34d399]/10 outline-[#34d399]",
        text: "text-[#34d399]",
    },
    failed: {
        badge: "bg-[#e53535]/10 outline-[#e53535]",
        text: "text-[#e53535]",
    },
    not_completed: {
        badge: "bg-[#e53535]/10 outline-[#e53535]",
        text: "text-[#e53535]",
    },
    draft: {
        badge: "bg-[#6366f1]/10 outline-[#6366f1]",
        text: "text-[#6366f1]",
    },
    pending: {
        badge: "bg-[#f97316]/10 outline-[#f97316]",
        text: "text-[#f97316]",
    },
    paid: {
        badge: "bg-[#22c55e]/10 outline-[#22c55e]",
        text: "text-[#22c55e]",
    },
    unpaid: {
        badge: "bg-[#eab308]/10 outline-[#eab308]",
        text: "text-[#eab308]",
    },
    archive: {
        badge: "bg-[#6b7280]/10 outline-[#6b7280]",
        text: "text-[#6b7280]",
    },
    invited: {
        badge: "bg-[#6366f1]/10 outline-[#6366f1]",
        text: "text-[#6366f1]",
    },
    deleted: {
        badge: "bg-[#e53535]/10 outline-[#e53535]",
        text: "text-[#e53535]",
    },
    error: {
        badge: "bg-[#e53535]/10 outline-[#e53535]",
        text: "text-[#e53535]",
    }, // default
};

const props = defineProps({
    label: { type: String, default: "Badge" },
    type: { type: String, default: "error" }, // accepts any case; spaces/hyphens normalized
});

// Normalize: case-insensitive; spaces/hyphens -> underscores
const normalizedType = computed(() =>
    String(props.type ?? "error")
        .trim()
        .toLowerCase()
        .replace(/[\s-]+/g, "_")
);

// Pick style; fallback to 'error' if unknown
const picked = computed(() => STYLES[normalizedType.value] ?? STYLES.error);

const badgeClasses = computed(() => picked.value.badge);
const textClasses = computed(() => picked.value.text);
</script>
