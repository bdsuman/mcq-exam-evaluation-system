<template>
    <div
        class="w-full mx-auto my-6 flex flex-col bg-white rounded-2xl max-h-[75vh] py-2"
    >
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b gap-3">
            <div class="min-w-0">
                <h3 class="text-base font-semibold truncate">
                    {{ displayName }}
                </h3>
                <p v-if="infoText" class="text-xs text-gray-500 truncate">
                    {{ infoText }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    @click="download"
                    :disabled="!canDownload"
                    class="cursor-pointer inline-flex items-center gap-2 text-xs px-3 py-1 rounded-md border hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <path d="M7 10l5 5 5-5" />
                        <path d="M12 15V3" />
                    </svg>
                    <span class="hidden sm:inline">{{ $t("Download") }}</span>
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div
            v-if="sheets.length > 1"
            class="sticky top-0 z-10 flex items-center gap-2 px-4 py-2 border-b bg-white"
        >
            <button
                class="cursor-pointer text-xs px-2 py-1 rounded-md border hover:bg-gray-50 shrink-0 focus:outline-none focus:ring-2 focus:ring-gray-400 disabled:opacity-40 disabled:cursor-not-allowed"
                @click="prevSheet"
                :disabled="!canPrev"
            >
                ←
            </button>

            <div ref="tabsRef" class="flex gap-2 overflow-x-auto grow min-w-0">
                <button
                    v-for="(s, i) in sheets"
                    :key="s.name + i"
                    class="group relative text-xs px-3 py-1 rounded-md border whitespace-nowrap mx-1 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400"
                    :class="
                        i === activeIndex
                            ? 'bg-gray-900 text-white border-gray-900 shadow-sm'
                            : 'bg-white hover:bg-gray-50 cursor-pointer'
                    "
                    @click="activeIndex = i"
                >
                    {{ s.name }}
                    <span class="ml-1 text-[10px] opacity-70">
                        ({{ s.dim[0] }}×{{ s.dim[1] }})
                    </span>
                </button>
            </div>

            <button
                class="cursor-pointer text-xs px-2 py-1 rounded-md border hover:bg-gray-50 shrink-0 focus:outline-none focus:ring-2 focus:ring-gray-400 disabled:opacity-40 disabled:cursor-not-allowed"
                @click="nextSheet"
                :disabled="!canNext"
            >
                →
            </button>
        </div>

        <!-- Scrollable table area -->
        <div class="flex-1 overflow-auto min-h-0">
            <!-- States -->
            <div v-if="loading" class="p-4 text-sm text-gray-500">
                {{ $t("Parsing spreadsheet…") }}
            </div>
            <div v-else-if="error" class="p-4 text-sm text-red-600">
                {{ $t(error) }}
            </div>

            <!-- Table -->
            <div v-else class="p-4">
                <table
                    class="w-full text-[13px] border-separate border-spacing-0"
                >
                    <tbody>
                        <tr
                            v-for="(row, rIdx) in limitedRows"
                            :key="'r' + rIdx"
                            :class="[
                                headerRow && rIdx === 0
                                    ? ''
                                    : rIdx % 2 === (headerRow ? 0 : 1)
                                    ? 'bg-gray-50'
                                    : '',
                                'hover:bg-gray-100',
                            ]"
                        >
                            <!-- Row number -->
                            <td
                                v-if="showRowNumbers"
                                :class="[
                                    'border border-gray-200 p-2 text-right align-top select-none sticky left-0 bg-white w-12',
                                    headerRow && rIdx === 0
                                        ? 'top-0 font-semibold text-gray-700 shadow z-30'
                                        : 'text-gray-600 z-20',
                                ]"
                            >
                                {{
                                    headerRow && rIdx === 0
                                        ? ""
                                        : rIdx +
                                          (headerRow ? 0 : 1) +
                                          rowNumberOffset
                                }}
                            </td>

                            <!-- Data cells -->
                            <td
                                v-for="(c, cIdx) in fullColRange"
                                :key="'c' + cIdx"
                                :class="[
                                    'border border-gray-200 p-2 leading-snug align-top',
                                    headerRow && rIdx === 0
                                        ? 'sticky top-0 bg-white font-semibold text-gray-700 shadow z-10'
                                        : '',
                                ]"
                                :style="{ width: columnWidths[cIdx] + 'px' }"
                            >
                                <pre
                                    class="whitespace-pre-wrap break-words min-h-[1.5rem]"
                                    v-html="formatCell(row[cIdx])"
                                ></pre>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Overflow note -->
            <div
                v-if="rowOverflow || colOverflow"
                class="px-4 pb-4 text-[12px] text-gray-600 mt-1 flex items-center gap-3"
            >
                {{
                    $t(
                        "Showing first :rowLimit rows and :colLimit columns for preview.",
                        { rowLimit, colLimit }
                    )
                }}
                <button
                    v-if="sheets.length > 1 && canNext"
                    class="inline-flex items-center gap-1 underline hover:no-underline focus:outline-none focus:ring-2 focus:ring-gray-400 rounded px-1"
                    @click="nextSheet"
                >
                    {{ $t("Jump to next sheet") }} →
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { trans } from "laravel-vue-i18n";
import { ref, watch, computed, nextTick } from "vue";
import * as XLSX from "xlsx";

const props = defineProps({
    file: { type: [Object, File, Blob], default: null },
    src: { type: String, default: "" },
    name: { type: String, default: "" },
    rowLimit: { type: Number, default: 200 },
    colLimit: { type: Number, default: 25 },
    showRowNumbers: { type: Boolean, default: true },
    rowNumberOffset: { type: Number, default: 0 },
    headerRow: { type: Boolean, default: true },
});

const loading = ref(false);
const error = ref("");
const sheets = ref([]);
const activeIndex = ref(0);
const fetchedBlob = ref(null);
const tabBtnRefs = ref([]);

const canPrev = computed(() => activeIndex.value > 0);
const canNext = computed(() => activeIndex.value < sheets.value.length - 1);

function prevSheet() {
    if (canPrev.value) activeIndex.value--;
}
function nextSheet() {
    if (canNext.value) activeIndex.value++;
}

watch(activeIndex, async () => {
    await nextTick();
    const el = tabBtnRefs.value[activeIndex.value];
    if (el?.scrollIntoView)
        el.scrollIntoView({
            block: "nearest",
            inline: "center",
            behavior: "smooth",
        });
});
const fullColRange = computed(() => {
    // Always use the widest header or row as column range
    const maxCols = activeSheet.value.dim[1];
    return Array.from({ length: maxCols });
});
const columnWidths = computed(() => {
    const maxCols = activeSheet.value.dim[1];
    const widths = Array(maxCols).fill(0);

    activeSheet.value.rows.forEach((row) => {
        row.forEach((cell, i) => {
            const len = String(cell || "").length;
            if (len > widths[i]) widths[i] = len;
        });
    });

    // Convert lengths to approximate pixel widths (adjust multiplier as needed)
    return widths.map((len) => Math.min(300, Math.max(50, len * 8)));
});
const rowLimit = computed(() => props.rowLimit);
const colLimit = computed(() => props.colLimit);
const activeSheet = computed(
    () => sheets.value[activeIndex.value] || { rows: [], dim: [0, 0] }
);
const limitedRows = computed(() =>
    activeSheet.value.rows.slice(0, rowLimit.value)
);
const rowOverflow = computed(
    () => activeSheet.value.rows.length > rowLimit.value
);
const colOverflow = computed(
    () => (activeSheet.value.rows[0]?.length || 0) > colLimit.value
);
const limitedCols = (row) => row.slice(0, colLimit.value);

const displayName = computed(
    () =>
        props.file?.name ||
        props.name ||
        props.src.split("/").pop() ||
        "Spreadsheet"
);

const infoText = computed(() => {
    if (!sheets.value.length) return "";
    const totalSheets = sheets.value.length;
    const dims = sheets.value.map((s) => s.dim);
    const rows = dims.reduce((a, d) => a + d[0], 0);
    return `${totalSheets} ${
        totalSheets > 1 ? trans("sheets") : trans("sheet")
    }, ~${rows} ${rows > 1 ? trans("rows") : trans("row")}`;
});

const canDownload = computed(
    () => !!(props.file instanceof File || fetchedBlob.value)
);

function formatCell(val) {
    if (val == null || val === "") return "&nbsp;";
    return String(val);
}

watch(() => [props.file, props.src], parse, { immediate: true });

async function parse() {
    loading.value = true;
    error.value = "";
    sheets.value = [];
    activeIndex.value = 0;

    try {
        const blob = await ensureBlob(props.file, props.src);
        fetchedBlob.value = blob;
        const buf = await blob.arrayBuffer();
        const wb = XLSX.read(buf, { type: "array" });

        sheets.value = wb.SheetNames.map((name) => {
            const ws = wb.Sheets[name];
            const aoa = XLSX.utils.sheet_to_json(ws, { header: 1 });
            const dim = [aoa.length, Math.max(0, ...aoa.map((r) => r.length))];
            return { name, rows: aoa, dim };
        });
    } catch (e) {
        console.error(e);
        error.value =
            "Unable to preview spreadsheet. Make sure the file is valid.";
    } finally {
        loading.value = false;
    }
}

async function ensureBlob(fileLike, url) {
    if (fileLike instanceof File || fileLike instanceof Blob) return fileLike;
    if (fileLike?.url) return await (await fetch(fileLike.url)).blob();
    if (url) return await (await fetch(url)).blob();
    throw new Error("No file or src provided");
}

function download() {
    try {
        const wb = XLSX.utils.book_new();
        sheets.value.forEach((s) => {
            const ws = XLSX.utils.aoa_to_sheet(s.rows);
            XLSX.utils.book_append_sheet(wb, ws, s.name.substring(0, 31));
        });
        const out = XLSX.write(wb, { bookType: "xlsx", type: "array" });
        const blob = new Blob([out], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });
        const a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = displayName.value || "spreadsheet.xlsx";
        a.click();
    } catch (e) {
        console.error(e);
    }
}
</script>
