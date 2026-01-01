<template>
    <div class="flex flex-wrap gap-3 mb-4 justify-between items-center md:flex-row flex-col md:items-center">
        <!-- Left Section: Search + Filters -->
        <div class="flex flex-wrap gap-3 items-center w-full md:w-auto">
            <TableSearch v-if="enableSearch" v-model="searchLocal" :placeholder="searchPlaceholder" :disabled="loading"
                :debounce="300" @debounced-change="handleSearchChange" @commit="handleSearchCommit"
                class="flex-1 md:flex-none min-w-[220px]" />
            <TableFilters v-if="props.filters?.length" :filters="props.filters" @change="onFilterUpdate"
                class="flex-1 md:flex-none min-w-[200px]" />
        </div>

        <!-- Right Section: Reset Button -->
        <div class="w-full md:w-auto flex justify-end">
            <Button v-if="props.filters?.some((f) => f.resettable)" title="Reset"
                :extraClasses="'!h-10 !rounded-xl !w-[90px]'" :bgClass="'bg-[#7b7e7d]'" @click="resetAllFilters" />
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-4 md:p-6">
        <div class="overflow-x-auto">
            <div class="min-w-full">
                <div v-if="$slots.switch" class="pb-4 mb-4 border-b border-gray-200">
                    <slot name="switch" />
                </div>

                <div ref="tableContainer" class="overflow-y-auto" style="scrollbar-gutter: stable" :class="$slots.switch
                    ? 'max-h-[calc(100vh-450px)] min-h-[calc(100vh-450px)]'
                    : 'max-h-[calc(100vh-356px)] min-h-[calc(100vh-356px)]'
                    ">
                    <table class="w-full table-fixed" :class="{ 'text-sm': dense }">
                        <colgroup v-if="normColumns">
                            <col v-for="c in normColumns" :key="c.key" :style="{ width: c.width || 'auto' }" />
                        </colgroup>

                        <thead class="bg-white z-20 min-h-[56px]" :class="{ 'sticky top-0': stickyHeader }">
                            <tr class="text-left text-slate-600 text-sm font-semibold">
                                <slot name="thead" :columns="normColumns" :toggleSort="toggleSort" :sort="sort"
                                    :ariaSort="ariaSort" v-if="$slots.thead" />
                                <template v-else-if="normColumns">
                                    <th v-for="c in normColumns" :key="c.key" :class="[
                                        'px-4 py-2 break-word',
                                        c.thClass,
                                        {
                                            'cursor-pointer select-none group':
                                                enableSort &&
                                                c.sortable !== false,
                                        },
                                    ]" :aria-sort="ariaSort(c.key)" @click="
                                        enableSort &&
                                        c.sortable !== false &&
                                        toggleSort(c.key)
                                        ">
                                        <div class="inline-flex items-center gap-1">
                                            <span>{{
                                                $t?.(c.label) ?? c.label
                                                }}</span>
                                            <svg v-if="enableSort" class="size-6 transition-opacity duration-200"
                                                :class="[
                                                    sort.by === c.key
                                                        ? 'opacity-100'
                                                        : 'opacity-0 group-hover:opacity-100',
                                                    sort.by === c.key
                                                        ? 'text-[#3F8B77]'
                                                        : 'text-sky-500'
                                                ]" viewBox="0 0 24 24" fill="currentColor">
                                                <path v-if="
                                                    sort.by === c.key &&
                                                    sort.dir === 'asc'
                                                " d="M7 14l5-5 5 5H7z" />
                                                <path v-else d="M7 10l5 5 5-5H7z" />
                                            </svg>
                                        </div>
                                    </th>
                                </template>
                            </tr>
                        </thead>

                        <slot :page="pageLocal" :pageSize="pageSizeLocal" :sort="sort" :search="searchLocal"
                            :rowStart="rowStart" :rowEnd="rowEnd" :total="total" :loading="loading">
                            <tbody>
                                <tr>
                                    <td :colspan="normColumns?.length || 1"
                                        class="text-center text-slate-500 !py-20 bg-slate-50 rounded-xl border border-slate-200">
                                        <slot name="empty">No data</slot>
                                    </td>
                                </tr>
                            </tbody>
                        </slot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <template v-if="!hidePager">
        <div class="flex gap-3 mb-4 items-end justify-between mt-4">
            <TablePagination v-model:page="pageLocal" :totalPages="totalPages" @update:page="onPageChange" />
            <TablePageSize v-model:pageSize="pageSizeLocal" :options="pageSizeOptions" :label="pageSizeLabel"
                @update:pageSize="onPageSizeChange" />
        </div>
    </template>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";

import Button from "@/components/buttons/Button.vue";

import TablePagination from "@/components/table/parts/TablePagination.vue";
import TablePageSize from "@/components/table/parts/TablePageSize.vue";
import TableSearch from "@/components/table/parts/TableSearch.vue";
import TableFilters from "@/components/table/parts/TableFilters.vue";

import { baseTableProps } from "@/components/table/js/baseTableProps";
import { baseTableEmits } from "@/components/table/js/baseTableEmits";
import { useBaseTable } from "@/composables/useBaseTable";

const props = defineProps(baseTableProps);
const emit = defineEmits(baseTableEmits);

const route = useRoute();
const router = useRouter();
const tableContainer = ref(null);

const {
    normColumns,
    pageLocal,
    pageSizeLocal,
    searchLocal,
    sort,
    totalPages,
    rowStart,
    rowEnd,
    ariaSort,
    toggleSort,
    onPageChange,
    onPageSizeChange,
    handleSearchChange,
    handleSearchCommit,
    onFilterUpdate,
    resetAllFilters,
} = useBaseTable({ props, emit, route, router, tableContainer });

</script>