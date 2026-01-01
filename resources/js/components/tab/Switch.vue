<template>
    <div
        ref="wrapRef"
        class="relative flex bg-gray-100 rounded-full p-2 w-fit shadow-sm"
    >
        <!-- Animated pill -->
        <span
            v-show="activeIndex >= 0"
            ref="indicatorRef"
            class="absolute z-0 rounded-full shadow-md transition-all duration-300 ease-out will-change-transform"
            :style="indicatorStyle"
            aria-hidden="true"
        />

        <!-- Tabs -->
        <button
            v-for="(tab, i) in tabs"
            :key="tab.route"
            :ref="(el) => (btnRefs[i] = el)"
            @click="goTo(tab.route)"
            class="relative z-10 px-8 py-3 rounded-full text-sm font-medium transition-all duration-200 cursor-pointer group"
            :class="
                isActive(tab.route)
                    ? 'text-white'
                    : 'text-gray-600 hover:text-gray-800'
            "
        >
            <span class="relative">
                {{ $t(tab.name) }}
            </span>
        </button>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    nextTick,
    onMounted,
    onBeforeUnmount,
} from "vue";
import { useRoute, useRouter } from "vue-router";

const props = defineProps({ tabs: { type: Array, required: true } });

const router = useRouter();
const route = useRoute();

const wrapRef = ref(null);
const btnRefs = ref([]);
const indicatorRef = ref(null);
const indicatorStyle = ref({
    top: "8px",
    height: "0px",
    left: "0px",
    width: "0px",
    background: "#005766",
});

const activeIndex = computed(() =>
    props.tabs.findIndex((t) => route.path.startsWith(t.route))
);

const goTo = (r) => {
    router.push({
        path: r,
        query: { page: 1, pageSize: 10, sortBy: "", sortDir: "", search: "" },
    });
};

const isActive = (r) => route.path.startsWith(r);

function recalcIndicator() {
    const wrap = wrapRef.value;
    if (!wrap) return;

    const idx = activeIndex.value;
    const btn = btnRefs.value[idx];
    if (!btn) return;

    const wrapRect = wrap.getBoundingClientRect();
    const btnRect = btn.getBoundingClientRect();

    // Match the button’s size/position (account for container padding p-2 = 8px)
    const top = 8; // px
    const height = Math.max(0, wrapRect.height - 16); // inset-y-2 equivalent

    indicatorStyle.value = {
        top: `${top}px`,
        height: `${height}px`,
        left: `${btnRect.left - wrapRect.left}px`,
        width: `${btnRect.width}px`,
        background: "#005766",
        // smooth motion
        transition:
            "left 300ms ease, width 300ms ease, top 300ms ease, height 300ms ease",
    };
}

function handleResize() {
    // recalc while fonts/layout might change
    nextTick(recalcIndicator);
}

watch(
    () => route.path,
    async () => {
        await nextTick();
        recalcIndicator();
    }
);

watch(
    () => props.tabs,
    async () => {
        await nextTick();
        recalcIndicator();
    },
    { deep: true }
);

onMounted(() => {
    nextTick(recalcIndicator);
    window.addEventListener("resize", handleResize, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});
</script>
