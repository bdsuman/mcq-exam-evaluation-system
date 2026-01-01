<template>
    <nav
        class="flex items-center justify-center select-none w-full"
        aria-label="Pagination"
    >
        <div
            class="bg-white w-fit h-[44px] flex items-center justify-center gap-4 px-[12px]"
        >
            <!-- Previous -->
            <button
                class="px-2 py-1 text-[#0e5159] cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="page === 1"
                @click="onPrev"
            >
                {{ $t('Previous')}}
            </button>

            <!-- Page numbers -->
            <div class="flex items-center gap-2">
                <template v-for="p in pages" :key="p.key">
                    <!-- Ellipsis -->
                    <span v-if="p.ellipsis" class="px-2">…</span>

                    <!-- Page button -->
                    <button
                        v-else
                        :aria-current="p.page === page ? 'page' : undefined"
                        class="rounded-xs cursor-pointer"
                        :class="[
                            'px-3 py-1 leading-[1.2]',
                            p.page === page
                                ? 'bg-[#0e5159] text-white'
                                : 'text-[#0e5159] hover:underline',
                        ]"
                        @click="onPageClick(p.page)"
                    >
                        {{ p.label }}
                    </button>
                </template>
            </div>

            <!-- Next -->
            <button
                class="px-2 py-1 text-[#0e5159] cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="page === totalPages"
                @click="onNext"
            >
                {{ $t('Next') }}
            </button>
        </div>
    </nav>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    page: { type: Number, required: true },
    totalPages: { type: Number, required: true },
    /** how many neighbors around current page (set 3 to get 1 2 3 4 … 10) */
    neighborCount: { type: Number, default: 3 },
});

const emit = defineEmits(["update:page", "previous", "next", "page-click"]);

function onPrev() {
    if (props.page > 1) {
        emit("update:page", props.page - 1);
        emit("previous", props.page - 1);
    }
}
function onNext() {
    if (props.page < props.totalPages) {
        emit("update:page", props.page + 1);
        emit("next", props.page + 1);
    }
}
function onPageClick(p) {
    if (p !== props.page) {
        emit("update:page", p);
        emit("page-click", p);
    }
}

const pages = computed(() => {
    const last = Math.max(1, props.totalPages);
    const cur = Math.min(Math.max(1, props.page), last);

    // Always include first, last, and neighbors around current
    const set = new Set([
        1,
        last,
        cur,
        ...Array.from({ length: props.neighborCount }, (_, i) => cur - (i + 1)),
        ...Array.from({ length: props.neighborCount }, (_, i) => cur + (i + 1)),
    ]);

    const nums = [...set]
        .filter((n) => n >= 1 && n <= last)
        .sort((a, b) => a - b);

    const out = [];
    let prev = 0;
    nums.forEach((n) => {
        if (prev && n - prev > 1)
            out.push({ label: "…", key: `e${prev}`, ellipsis: true });
        out.push({ label: String(n), page: n, key: `p${n}` });
        prev = n;
    });
    return out;
});

</script>
