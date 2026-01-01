<template>
  <div class="relative w-full" v-click-away="closeDropdown">
    <label for="single-select" class="py-2 text-[#113B51] text-sm font-medium"
      >{{ $t(label) }}
      <span v-if="required">*</span>
    </label>
    <div
      @click="toggleDropdown"
      class="w-full h-[60px] bg-[#F5F5F5] rounded-lg border border-gray-300 flex items-center justify-between px-4 cursor-pointer transition-all"
      :class="{
        '!border-red-500': error,
        'opacity-50 cursor-not-allowed': disabled,
        'border-blue-500 ring-2 ring-blue-200': isOpen,
      }"
      id="single-select">
      <span v-if="selectedItem" class="text-gray-900">
        {{ $t(resolveLabel(selectedItem)) }}
      </span>
      <span v-else class="text-gray-400">
        {{ $t(placeholder) }}
      </span>
      <div class="flex items-center gap-2">
        <button
          v-if="showClear && selectedItem"
          @click.stop="clearSelection"
          class="text-gray-400 hover:text-gray-600 transition"
          type="button">
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <DropdownIcon :isOpen="isOpen" />
      </div>
    </div>

    <div
      v-if="isOpen && !disabled"
      class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-[300px] overflow-hidden">
      <div v-if="searchable" class="p-2 border-b border-gray-200">
        <input
          ref="searchInput"
          v-model="searchQuery"
          @input="onSearch"
          type="text"
          :placeholder="$t('Search...')"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div
        ref="optionsList"
        @scroll="onScroll"
        class="overflow-y-auto max-h-[250px]">
        <div
          v-for="item in filteredItems"
          :key="item[optionValue]"
          @click="selectItem(item)"
          class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors"
          :class="{
            'bg-blue-100': selectedItem === item[optionValue],
          }">
          {{ $t(item[optionLabel]) }}
        </div>

        <div v-if="loading" class="px-4 py-3 text-center text-gray-500">
          <svg
            class="animate-spin h-5 w-5 mx-auto"
            fill="none"
            viewBox="0 0 24 24">
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <div
          v-if="!loading && filteredItems.length === 0"
          class="px-4 py-3 text-center text-gray-500">
          {{
            searchQuery ? $t("No results found") : $t("No available options")
          }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from "vue";
import axios from "axios";
import DropdownIcon from "@/components/icons/DropdownIcon.vue";

const isOpen = ref(false);
const searchInput = ref(null);
const optionsList = ref(null);

// Props to receive dynamic optionLabel, optionValue, endpoint, and pagination flag
const props = defineProps({
  label: {
    type: String,
    default: "Select Item",
  },
  required: {
    type: Boolean,
    default: false,
  },
  optionLabel: {
    required: true,
  },
  optionValue: {
    type: String,
    required: true,
  },
  endpoint: {
    type: String,
    required: true,
  },
  pagination: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: "Select Item",
  },
  preSelected: {},
  fieldName: {
    type: String,
    default: "",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  error: {
    type: Boolean,
    default: false,
  },
  searchable: {
    type: Boolean,
    default: true,
  },
  showClear: {
    type: Boolean,
    default: false,
  },
  modelValue: {},
});

// Define the emits
const emit = defineEmits(["update:modelValue"]);

// State variables
const selectedItem = ref(null);
const items = ref([]);
const filteredItems = ref([]);
const loading = ref(false);
const page = ref(1);
const pageSize = 10;
let initialized = ref(false);
let isFetching = ref(false);
let searchQuery = ref("");

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

const selectItem = (item) => {
  selectedItem.value = item[props.optionValue];
  emit("update:modelValue", selectedItem.value);
  closeDropdown();
};

const clearSelection = () => {
  selectedItem.value = null;
  emit("update:modelValue", null);
};

// Debounce function
const debounce = (func, delay) => {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      func(...args);
    }, delay);
  };
};

// Function to fetch data based on search term and pagination
const fetchData = async (search = "", pageNum = 1, append = false) => {
  if (isFetching.value) return;

  isFetching.value = true;
  loading.value = true;
  try {
    const response = await axios.get(
      props.endpoint.startsWith("/") ? props.endpoint : `/${props.endpoint}`,
      {
        params: {
          page: pageNum,
          pageSize: pageSize,
          search: search,
          [props.fieldName]: props.preSelected,
        },
      }
    );

    if (response.data && response.data.data) {
      const filteredData = response.data.data.filter(
        (item) =>
          (!item.status || item.status !== "inactive") && !item.deleted_at
      );

      if (append) {
        items.value = [...items.value, ...filteredData];
      } else {
        items.value = filteredData;
      }

      filteredItems.value = items.value;
      initialized.value = true;
    }
  } catch (error) {
    console.error(
      "Error fetching data:",
      error.response ? error.response.data : error.message
    );
  } finally {
    loading.value = false;
    isFetching.value = false;
    if (props.preSelected) {
      selectedItem.value = props.preSelected;
    }
  }
};

// Handle scroll for lazy loading
const onScroll = (event) => {
  if (!props.pagination) return;

  const { scrollTop, scrollHeight, clientHeight } = event.target;

  if (
    scrollHeight - scrollTop - clientHeight < 50 &&
    !loading.value &&
    initialized.value
  ) {
    page.value += 1;
    fetchData(searchQuery.value, page.value, true);
  }
};

// Debounced search handler
const debouncedSearch = debounce((search) => {
  page.value = 1;
  fetchData(search, 1, false);
}, 300);

const onSearch = () => {
  debouncedSearch(searchQuery.value);
};

// Keep a valid resolve for label from either primitive v-model or object
const resolveLabel = (opt) => {
  if (opt && typeof opt === "object") return opt[props.optionLabel] ?? "";
  const obj = items.value.find((i) => i[props.optionValue] === opt);
  return obj ? obj[props.optionLabel] : "";
};

watch(
  () => props.modelValue,
  (newVal) => {
    selectedItem.value = newVal;
  }
);

onMounted(() => {
  selectedItem.value = props.modelValue || props.preSelected;
  fetchData();
});
</script>
