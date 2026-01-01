<template>
    <div
        v-for="(row, qIdx) in options"
        :key="qIdx"
        class="text-[#002D45] mb-8 w-full px-1"
    >
        <!-- Question row -->
        <div
            class="font-medium leading-tight flex justify-between gap-2 text-base"
        >
            <template v-if="number_show">
                {{ `${number}. ${row?.question}` }}
            </template>
            <template v-else>
                {{ row.question }}
            </template>
            <DeleteIcon
                v-if="options.length > 1 && has_delete"
                @click="$emit('delete', row)"
            />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div v-for="(value, index) in row?.options" :key="value.id || index">
                <div
                    class="p-4 min-h-15 rounded-2xl flex flex-col justify-center gap-2.5 text-sm leading-tight"
                    :class="[
                        active_id === value.id
                            ? 'bg-[#C1FDDA] border border-[#3AB57C]'
                            : 'bg-[#F3F6FA]',
                    ]"
                >
                    <div
                        class="font-semibold"
                        v-if="value.option_value && option_value_show"
                    >
                        {{ $t(value.option_value) }}
                    </div>
                    <div v-if="value.option_text">
                        {{ value.option_text }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import DeleteIcon from "@/components/icons/DeleteIcon.vue";
const props = defineProps({
    options: {
        type: Array,
        required: true,
        default: () => [],
    },
    has_delete: {
        type: Boolean,
        default: true,
    },
    option_value_show: {
        type: Boolean,
        default: true,
    },
    active_id: {
        type: Number,
        default: 0,
    },
    number: {
        type: Number,
        default: 1,
    },
    number_show: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["delete"]);
</script>
