<template>
    <div
        class="progress-circle"
        :style="{ width: size + 'px', height: size + 'px' }"
    >
        <svg class="circle" viewBox="0 0 36 36">
            <defs>
                <!-- Gradient from Figma -->
                <linearGradient
                    id="progressGradient"
                    x1="18"
                    y1="0"
                    x2="18"
                    y2="36"
                    gradientUnits="userSpaceOnUse"
                >
                    <stop offset="0%" stop-color="#005766" />
                    <stop offset="100%" stop-color="#8BEE82" />
                </linearGradient>
            </defs>

            <!-- background circle -->
            <circle
                class="bg"
                cx="18"
                cy="18"
                r="15.9155"
                :stroke-width="strokeWidth"
            />

            <!-- progress circle -->
            <circle
                class="progress"
                cx="18"
                cy="18"
                r="15.9155"
                :stroke-dasharray="`${value}, 100`"
                :stroke-width="strokeWidth"
            />
        </svg>

        <!-- dynamic font size -->
        <div class="text" :style="{ fontSize: fontSize + 'px' }">
            {{ value }}%
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    value: {
        type: Number,
        required: true,
        default: 0,
    },
    size: {
        type: Number,
        default: 150, // controls height & width
    },
    strokeWidth: {
        type: Number,
        default: 3, // controls circle thickness
    },
});

// auto-scale font size (adjust multiplier if needed)
const fontSize = computed(() => Math.round(props.size * 0.25));
</script>

<style scoped>
.progress-circle {
    position: relative;
}

.circle {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg); /* ensures start at top (12 o’clock) */
}

.bg {
    fill: none;
    stroke: #e6ebf5;
}

.progress {
    fill: none;
    stroke: url(#progressGradient);
    stroke-linecap: round;
    transition: stroke-dasharray 0.5s ease;
}

.text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-weight: 500;
    color: #0c2d3e;
    word-wrap: break-word;
}
</style>
