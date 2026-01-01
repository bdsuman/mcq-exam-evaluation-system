<template>
    <div class="flex items-center gap-2 cursor-pointer">
        <input
            type="date"
            v-model="startDate"
            :max="maxDateString"
            :min="minDateString"
            :disabled="disabled"
            class="!h-10 !rounded-xl !bg-white w-40 border border-gray-300 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <span class="text-gray-500">—</span>
        <input
            type="date"
            v-model="endDate"
            :max="maxDateString"
            :min="minDateString"
            :disabled="disabled"
            class="!h-10 !rounded-xl !bg-white w-40 border border-gray-300 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button
            v-if="datesLocal[0] || datesLocal[1]"
            @click="clearRange"
            type="button"
            class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium text-gray-700 bg-white rounded-full border border-gray-300 hover:bg-gray-50"
        >
            <CrossIcon class="w-4 h-4" :fill="'#7B7E7D'" />
        </button>
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import CrossIcon from "@/components/icons/CrossIcon.vue";

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    placeholder: { type: String, default: "Select date range" },
    disabled: { type: Boolean, default: false },
    minDate: { type: Date, default: null },
    maxDate: { type: Date, default: null },
});

const emit = defineEmits(["update:modelValue", "change"]);

// internal representation: [Date|null, Date|null]
const datesLocal = ref(["", ""]);

const maxDateString = computed(() => (props.maxDate instanceof Date ? toYMD(props.maxDate) : ""));
const minDateString = computed(() => (props.minDate instanceof Date ? toYMD(props.minDate) : ""));

// --- Helpers ---
function toYMD(d) {
    if (!(d instanceof Date)) return null;
    const y = d.getFullYear();
    const m = `${d.getMonth() + 1}`.padStart(2, "0");
    const day = `${d.getDate()}`.padStart(2, "0");
    return `${y}-${m}-${day}`;
}
function parseYMD(s) {
    if (!s) return null;
    const [y, m, d] = s.split("-").map(Number);
    return Number.isFinite(y) && Number.isFinite(m) && Number.isFinite(d)
        ? new Date(y, m - 1, d)
        : null;
}

// sync external model -> local
watch(
    () => props.modelValue,
    (v) => {
        if (!Array.isArray(v) || v.length < 2) {
            datesLocal.value = ["", ""];
        } else {
            datesLocal.value = [v[0] ?? "", v[1] ?? ""];
        }
    },
    { immediate: true }
);

const emitChange = () => {
    const [start, end] = datesLocal.value;
    if (start && end) {
        emit("update:modelValue", [start, end]);
        emit("change", [start, end]);
    } else {
        emit("update:modelValue", []);
        emit("change", []);
    }
};

const startDate = computed({
    get: () => datesLocal.value[0],
    set: (val) => {
        datesLocal.value[0] = val;
        emitChange();
    },
});

const endDate = computed({
    get: () => datesLocal.value[1],
    set: (val) => {
        datesLocal.value[1] = val;
        emitChange();
    },
});

function clearRange() {
    datesLocal.value = ["", ""];
    emit("update:modelValue", []);
    emit("change", []);
}

</script>