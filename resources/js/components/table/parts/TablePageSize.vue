<template>
    <label class="flex items-center justify-end gap-2 text-sm text-[#002D45] w-[200px]">
        <div>{{ $t(label) }}</div>

        <div class="relative" ref="root">
            <!-- Trigger -->
            <button
                type="button"
                class="px-3 text-sm flex items-center gap-2 min-w-[72px] py-2 bg-white cursor-pointer"
                :aria-expanded="open ? 'true' : 'false'"
                :aria-haspopup="'listbox'"
                @click="toggle"
                @keydown.down.prevent="openList('down')"
                @keydown.up.prevent="openList('up')"
                @keydown.enter.prevent="toggle"
                @keydown.space.prevent="toggle"
                @keydown.esc.prevent="close"
            >
                <span>{{ pageSize }}</span>
                <svg
                    class="ps-2 text-slate-500 transition-transform duration-200"
                    :class="{ 'rotate-180': open }"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </button>

            <!-- Dropdown -->
            <ul
                v-if="open"
                class="absolute z-20 mb-1 w-full bg-white border border-slate-200 rounded-md shadow-lg py-1 max-h-56 overflow-auto bottom-full"
                role="listbox"
                :aria-activedescendant="activeId"
                tabindex="-1"
                @keydown.down.prevent="move(1)"
                @keydown.up.prevent="move(-1)"
                @keydown.enter.prevent="selectActive"
                @keydown.space.prevent="selectActive"
                @keydown.esc.prevent="close"
            >
                <li
                    v-for="(opt, i) in options"
                    :key="String(opt)"
                    :id="idFor(i)"
                    role="option"
                    :aria-selected="opt === pageSize"
                    class="px-3 py-1 text-sm cursor-pointer flex items-center justify-between"
                    :class="[
                        i === activeIndex
                            ? 'bg-primary text-white'
                            : '',
                        opt === pageSize && i !== activeIndex
                            ? 'bg-slate-100'
                            : '',
                    ]"
                    @mousemove="activeIndex = i"
                    @click="select(opt)"
                >
                    <span>{{ opt }}</span>
                    <span v-if="opt === pageSize" class="text-xs opacity-80"
                        >✓</span
                    >
                </li>
            </ul>
        </div>
    </label>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount, computed } from "vue";

const props = defineProps({
    pageSize: { type: Number, required: true },
    options: { type: Array, default: () => [5, 10, 20, 50] },
    label: { type: String, default: "Per page:" },
});

const emit = defineEmits(["update:pageSize", "change"]);

const open = ref(false);
const activeIndex = ref(-1);
const root = ref(null);

function toggle() {
    open.value = !open.value;
    if (open.value) syncActiveToCurrent();
}
function close() {
    open.value = false;
}
function openList(dir = "down") {
    open.value = true;
    syncActiveToCurrent();
    if (dir === "down") move(1);
    else move(-1);
}
function move(delta) {
    if (!props.options.length) return;
    const len = props.options.length;
    let next = activeIndex.value;
    if (next === -1) next = Math.max(0, props.options.indexOf(props.pageSize));
    next = (next + delta + len) % len;
    activeIndex.value = next;
}
function selectActive() {
    if (activeIndex.value >= 0) select(props.options[activeIndex.value]);
}
function select(val) {
    emit("update:pageSize", Number(val));
    emit("change", Number(val));
    close();
}
function syncActiveToCurrent() {
    const idx = props.options.indexOf(props.pageSize);
    activeIndex.value = idx >= 0 ? idx : 0;
}
function idFor(i) {
    return `bps-opt-${i}`;
}
const activeId = computed(() =>
    activeIndex.value >= 0 ? idFor(activeIndex.value) : undefined
);

function onClickOutside(e) {
    if (root.value && !root.value.contains(e.target)) close();
}

onMounted(() => document.addEventListener("click", onClickOutside));
onBeforeUnmount(() => document.removeEventListener("click", onClickOutside));

watch(
    () => props.pageSize,
    () => {
        if (open.value) syncActiveToCurrent();
    }
);

</script>