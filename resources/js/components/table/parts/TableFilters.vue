<template>
    <div v-if="filters?.length" class="flex flex-wrap gap-3 items-center">
        <template v-for="(f, idx) in filters" :key="f.key || idx">
            <DateRangeFilter
                v-if="f.type === 'daterange'"
                :placeholder="f.placeholder || 'DD.MM.YYYY-DD.MM.YYYY'"
                v-model="f.model"
                class="shrink-0"
                @change="(val) => $emit('change', f, val)"
            />

            <MultiSelect
                v-else
                :modelValue="f.model"
                :preSelected="
                    Array.isArray(f.preSelected)
                        ? [...f.preSelected]
                        : f.preSelected
                "
                :placeholder="f.placeholder"
                :endpoint="f.endpoint"
                :fieldName="f.fieldName"
                :optionLabel="f.optionLabel"
                :optionValue="f.optionValue"
                :pagination="f.pagination ?? false"
                :extraClasses="'!h-10 !rounded-xl !bg-white'"
                @update:modelValue="(val) => $emit('change', f, val)"
                :searchable="f.searchable ?? true"
                :disabled="f.disabled ?? false"
                :showSelectAll="f.showSelectAll ?? false"
                class="shrink-0"
            />
        </template>
    </div>
</template>

<script setup>
import MultiSelect from "@/components/form/MultiSelect.vue";
import DateRangeFilter from "@/components/table/parts/DateRangeFilter.vue";

defineProps({ filters: { type: Array, default: () => [] } });
defineEmits(["change"]);

</script>