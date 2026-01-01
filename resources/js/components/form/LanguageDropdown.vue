<template>
    <div ref="wrap" class="relative inline-block">
        <!-- Trigger -->
        <button
            ref="btn"
            type="button"
            @click="toggle"
            class="min-w-[10.5rem] inline-flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-2.5 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300"
        >
            <span class="flex items-center gap-2.5">
                <!-- straight-edge flag image -->
                <img
                    v-if="selected"
                    :src="flagPath(selected)"
                    alt=""
                    class="h-4 w-6 object-contain align-middle"
                    loading="lazy"
                    decoding="async"
                />
                <span class="text-[15px] font-semibold text-gray-800 truncate">
                    {{ selected?.name }}
                </span>
            </span>

            <svg
                class="h-4 w-4 shrink-0 transition-transform"
                :class="{ 'rotate-180': open }"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>

        <!-- Dropdown -->
        <div
            v-if="open"
            ref="panel"
            class="absolute left-0 z-50 mt-2 w-[15rem] rounded-xl border border-gray-100 bg-white shadow-lg"
            :style="{ marginLeft: panelOffset + 'px' }"
        >
            <div v-if="showSearch" class="p-2">
                <input
                    v-model="q"
                    type="text"
                    :placeholder="$t('Search language...')"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gray-300"
                />
            </div>

            <ul class="max-h-60 overflow-y-auto py-1.5 px-2">
                <li
                    v-for="lang in filtered"
                    :key="lang.code"
                    @click="select(lang)"
                    class="flex cursor-pointer items-center gap-3 px-3 py-2.5 text-[15px] rounded-lg"
                    :class="
                        lang.code === modelValue
                            ? 'bg-green-100 font-semibold my-1  pr-3'
                            : 'text-gray-800 hover:bg-gray-50'
                    "
                >
                    <img
                        :src="flagPath(lang)"
                        alt=""
                        class="h-4 w-6 object-contain align-middle"
                        loading="lazy"
                        decoding="async"
                    />
                    <span class="flex-1">{{ lang.name }}</span>
                    <!-- checkmark optional
                    <svg v-if="lang.code === modelValue" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg> -->
                </li>

                <li
                    v-if="!filtered.length"
                    class="px-3 py-3 text-sm text-gray-500"
                >
                    {{ $t("No results") }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
    modelValue: { type: String, default: "en" },
    /**
     * Provide language list; if flagSrc is omitted, component will resolve from /flags/<file>.svg.
     * For English, it maps to 'us.svg' by default.
     */
    languages: {
        type: Array,
        default: () => [
            { code: "bn", name: "Bengali" },
            { code: "en", name: "English" },
            // { code: "fr", name: "Français" },
            // { code: "es", name: "Español" },
            // { code: "it", name: "Italiano" },
        ],
    },
    showSearch: { type: Boolean, default: false },
    /**
     * Override the auto alignment offset (px).
     * If null, it will use the button's padding-left.
     */
    fixedOffset: { type: Number, default: -70 },
});
const emit = defineEmits(["update:modelValue", "changed"]);

const open = ref(false);
const q = ref("");
const wrap = ref(null);
const btn = ref(null);
const panel = ref(null);
const panelOffset = ref(0);

const filtered = computed(() => {
    const s = q.value.trim().toLowerCase();
    return s
        ? props.languages.filter((l) => l.name.toLowerCase().includes(s))
        : props.languages;
});
const selected = computed(() =>
    props.languages.find((l) => l.code === props.modelValue)
);

/** Resolve a flag path:
 * 1) If lang.flagSrc exists, use it as-is.
 * 2) Else derive from code (en -> us.svg, otherwise code.svg).
 */
function flagPath(lang) {
    if (lang.flagSrc) return lang.flagSrc;
    const file = lang.code === "en" ? "us" : lang.code; // map 'en' -> 'us.svg'
    return `/flags/${file}.svg`;
}

function toggle() {
    open.value = !open.value;
    if (open.value) positionPanel();
}
function select(lang) {
    emit("update:modelValue", lang.code);
    emit("changed", lang.code);
    open.value = false;
    q.value = "";
}

/* Align panel's left edge with the start of the button content (flag/text). */
function positionPanel() {
    nextTick(() => {
        if (props.fixedOffset !== null) {
            panelOffset.value = props.fixedOffset;
            return;
        }
        const el = btn.value;
        if (!el) return;
        const padLeft = parseFloat(getComputedStyle(el).paddingLeft || "0");
        panelOffset.value = Number.isFinite(padLeft) ? Math.round(padLeft) : 0;
    });
}

/* Close on outside click */
function onDocClick(e) {
    if (!wrap.value) return;
    if (!wrap.value.contains(e.target)) open.value = false;
}
onMounted(() => document.addEventListener("mousedown", onDocClick));
onBeforeUnmount(() => document.removeEventListener("mousedown", onDocClick));
</script>
