<template>
    <div class="w-full inline-flex flex-col justify-start items-start gap-3">
        <!-- Label -->
        <div
            v-if="label"
            class="self-stretch justify-start text-[#002d45] text-sm font-medium leading-[18.20px]"
        >
            {{ $t(label) }} <span v-if="required">*</span>
        </div>

        <div class="relative w-full" v-click-away="closeDropdown">
            <div
                @click="toggleDropdown"
                class="w-full min-h-[60px] bg-[#F5F5F5] rounded-lg border border-gray-300 flex items-center justify-between px-3 py-2 cursor-pointer transition-all"
                :class="{
                    '!border-red-500': error,
                    'opacity-50 cursor-not-allowed': disabled,
                    'border-blue-500 ring-2 ring-blue-200': isOpen
                }"
            >
                <div v-if="selectedItems.length" class="flex items-center gap-2 flex-wrap flex-1">
                    <div
                        v-for="(item, index) in visibleTags"
                        :key="index"
                        class="flex items-center gap-2 bg-[#E5EAF0] text-[#002d45] text-sm px-3 py-1 rounded-full shrink-0"
                    >
                        <span class="truncate max-w-[120px]">{{ $t(resolveLabel(item)) }}</span>
                        <button
                            type="button"
                            class="flex items-center justify-center text-[#002d45] hover:text-red-500 transition w-4 h-4"
                            @click.stop="removeItem(item)"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <button
                        v-if="hiddenCount > 0"
                        type="button"
                        class="bg-[#E5EAF0] text-[#002d45] px-2.5 py-1 rounded-full text-sm shrink-0"
                    >
                        +{{ hiddenCount }} {{ $t("more") }}
                    </button>
                </div>

                <span v-else class="text-gray-400">{{ $t(placeholder) }}</span>

                <DropdownIcon :isOpen="isOpen" class="ml-2 shrink-0" />
            </div>

            <div
                v-if="isOpen && !disabled"
                class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-[320px] overflow-hidden"
            >
                <div v-if="searchable" class="p-2 border-b border-gray-200">
                    <input
                        ref="searchInput"
                        v-model="searchQuery"
                        @input="onSearch"
                        type="text"
                        :placeholder="$t('Search...')"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div v-if="showSelectAll && filteredItems.length > 0" class="p-2 border-b border-gray-200">
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded">
                        <input
                            type="checkbox"
                            :checked="isAllSelected"
                            @change="toggleSelectAll"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                        />
                        <span class="text-sm">{{ $t('Select All') }}</span>
                    </label>
                </div>

                <div
                    ref="optionsList"
                    @scroll="onScroll"
                    class="overflow-y-auto"
                    :style="{ maxHeight: searchable || showSelectAll ? '220px' : '280px' }"
                >
                    <label
                        v-for="item in filteredItems"
                        :key="item[optionValue]"
                        class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors"
                        :class="{ 'bg-blue-100': isSelected(item[optionValue]) }"
                    >
                        <input
                            type="checkbox"
                            :checked="isSelected(item[optionValue])"
                            @change="toggleItem(item)"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                            :disabled="selectionLimit && selectedItems.length >= selectionLimit && !isSelected(item[optionValue])"
                        />
                        <span>{{ $t(item[optionLabel]) }}</span>
                    </label>

                    <div v-if="loading" class="px-4 py-3 text-center text-gray-500">
                        <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div v-if="!loading && filteredItems.length === 0" class="px-4 py-3 text-center text-gray-500">
                        {{ searchQuery ? $t('No results found') : $t('No available options') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
import DropdownIcon from "@/components/icons/DropdownIcon.vue";

const emit = defineEmits(["update:modelValue"]);

const isOpen = ref(false);
const searchInput = ref(null);
const optionsList = ref(null);
const route = useRoute();

const props = defineProps({
    label: { type: String, default: "Select Items" },
    required: { type: Boolean, default: false },
    endpoint: { type: String, required: true },
    pagination: { type: Boolean, default: false },
    optionLabel: { required: true },
    optionValue: { type: String, required: true },
    labelLimit: { type: Number, default: 3 },
    selectionLimit: { type: Number },
    placeholder: { type: String, default: "Select Items" },
    fieldName: { type: String, required: true },
    preSelected: {},
    disabled: { type: Boolean, default: false },
    error: { type: Boolean, default: false },
    searchable: { type: Boolean, default: false },
    showSelectAll: { type: Boolean, default: false },
    modelValue: { type: Array, default: () => [] }
});

const selectedItems = ref([]);
const items = ref([]);
const filteredItems = ref([]);
const loading = ref(false);
const page = ref(1);
const isFetching = ref(false);
const searchQuery = ref("");
const preSelectedCommaSeparate = ref("");

const visibleCount = ref(props.labelLimit || 3);

const visibleTags = computed(() =>
    selectedItems.value.slice(0, visibleCount.value)
);

const hiddenCount = computed(() =>
    Math.max(selectedItems.value.length - visibleCount.value, 0)
);

const isAllSelected = computed(() => 
    filteredItems.value.length > 0 && 
    filteredItems.value.every(item => selectedItems.value.includes(item[props.optionValue]))
);

const isSelected = (value) => selectedItems.value.includes(value);

const toggleDropdown = () => {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
    if (isOpen.value && props.searchable) {
        nextTick(() => searchInput.value?.focus());
    }
};

const closeDropdown = () => {
    isOpen.value = false;
    searchQuery.value = "";
};

const toggleItem = (item) => {
    const value = item[props.optionValue];
    const index = selectedItems.value.indexOf(value);
    
    if (index > -1) {
        selectedItems.value.splice(index, 1);
    } else {
        if (!props.selectionLimit || selectedItems.value.length < props.selectionLimit) {
            selectedItems.value.push(value);
        }
    }
    emit("update:modelValue", [...selectedItems.value]);
};

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedItems.value = [];
    } else {
        selectedItems.value = filteredItems.value.map(item => item[props.optionValue]);
    }
    emit("update:modelValue", [...selectedItems.value]);
};

const removeItem = (item) => {
    const value = typeof item === 'object' ? item[props.optionValue] : item;
    const index = selectedItems.value.indexOf(value);
    if (index > -1) {
        selectedItems.value.splice(index, 1);
        emit("update:modelValue", [...selectedItems.value]);
    }
};

const debounce = (func, delay) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), delay);
    };
};

const fetchData = async (searchValue = "", pageNum = 1, append = false) => {
    if (isFetching.value) return;
    isFetching.value = true;
    loading.value = true;
    try {
        const paramsDefault = {
            page: pageNum,
            search: searchValue,
            [props.fieldName]: preSelectedCommaSeparate.value,
        };
        const params = { ...paramsDefault, ...route.query };
        const response = await axios.get(`/${props.endpoint}`, { params });
        const filteredData = response.data.data.filter(
            (item) =>
                (!item.status || item.status !== "inactive") &&
                !item.deleted_at &&
                !["deleted", "blocked"].includes(item.value)
        );
        
        if (append) {
            items.value.push(...filteredData);
        } else {
            items.value = filteredData;
            page.value = 1;
        }
        filteredItems.value = items.value;
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
        isFetching.value = false;
    }
};

const onScroll = (event) => {
    if (!props.pagination) return;
    
    const { scrollTop, scrollHeight, clientHeight } = event.target;
    
    if (scrollHeight - scrollTop - clientHeight < 50 && !loading.value) {
        page.value += 1;
        fetchData(searchQuery.value, page.value, true);
    }
};

const debouncedSearch = debounce((search) => {
    page.value = 1;
    fetchData(search, 1, false);
}, 300);

const onSearch = () => {
    debouncedSearch(searchQuery.value);
};

const resolveLabel = (opt) => {
    const obj = typeof opt === "object" 
        ? opt 
        : items.value.find((i) => i[props.optionValue] === opt);
    return obj ? obj[props.optionLabel] : "";
};

watch(() => props.modelValue, (newVal) => {
    if (JSON.stringify(newVal) !== JSON.stringify(selectedItems.value)) {
        selectedItems.value = newVal || [];
    }
}, { deep: true });

onMounted(async () => {
    selectedItems.value = props.modelValue || props.preSelected || [];
    
    if (props.preSelected?.length > 0) {
        preSelectedCommaSeparate.value = props.preSelected.join(",");
    }
    
    await fetchData("", 1, false);
});
</script>
