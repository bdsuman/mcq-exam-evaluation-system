<template>
    <div
        v-click-away="onClickAway"
        v-if="showModal"
        ref="panel"
        class="absolute right-[80%] w-[150px] h-auto py-2 bg-[#F3F6FA] shadow flex-col justify-center items-start gap-2 z-10 cursor-pointer rounded-2xl"
        :class="positionClass"
        :style="computedStyle"
    >
        <div
            v-for="(item, index) in itemsWithIcons"
            :key="index"
            class="self-stretch px-2 bg-[#F3F6FA] inline-flex w-full"
        >
            <button
                type="button"
                class="w-full flex items-center gap-2 text-left text-[#585754] text-sm leading-[16.80px] px-2 py-2 hover:bg-[#C1FDDA] hover:text-primary rounded-lg cursor-pointer"
                :class="[
                    fontClass,
                    item.action === selectedAction
                        ? 'bg-[#C1FDDA] text-primary'
                        : '',
                ]"
                @click="handleAction(item.action, selectedRowId)"
            >
                <!-- Icon (default, registry, or explicit) -->
                <component
                    v-if="item._icon"
                    :is="item._icon"
                    :class="iconSizeClass + ' shrink-0'"
                    aria-hidden="true"
                />
                <span class="truncate">{{ $t(item.label) }}</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import EyeIcon from "@/components/icons/EyeIcon.vue";
import PencilSquareIcon from "@/components/icons/PencilSquareIcon.vue";
import TrashIcon from "@/components/icons/TrashIcon.vue";
// import ActiveIcon from "@/components/icons/ActiveIcon.vue";
// import InactiveIcon from "@/components/icons/InactiveIcon.vue";

// Props and emits
const props = defineProps({
    show: Boolean,
    actionItems: { type: Array, default: () => [] }, // [{ label, action, icon? }]
    selectedRowId: Number,
    fontClass: String,
    positionClass: String,
    selectedAction: String,

    // Optional registry the parent can pass to override/extend defaults
    // Keys can be action names or arbitrary keys you reference via item.icon (string)
    iconRegistry: { type: Object, default: () => ({}) },

    // Optional: tweak icon size from parent if you want
    iconSizeClass: { type: String, default: "w-4 h-4" },
});

const emit = defineEmits(["action", "close"]);
const panel = ref(null);
const positionAbove = ref(false);
const showModal = ref(props.show);

// 1) Built-in defaults (you can add more)
const DEFAULT_ICONS = {
    // aliases for view
    view: EyeIcon,
    details: EyeIcon,

    edit: PencilSquareIcon,
    delete: TrashIcon,

    // extra examples that you already use
    // active: ActiveIcon,
    // inactive: InactiveIcon,
};

// 2) Merge defaults with parent overrides
const registry = computed(() => ({ ...DEFAULT_ICONS, ...props.iconRegistry }));

// 3) Normalize items so each has a resolved icon component in `_icon`
const itemsWithIcons = computed(() =>
    props.actionItems.map((item) => {
        let IconComp = null;

        if (item.icon) {
            // If parent passed a string, look it up in the registry; if they passed a component, use it directly
            IconComp =
                typeof item.icon === "string"
                    ? registry.value[item.icon] ?? null
                    : item.icon;
        } else if (item.action && registry.value[item.action]) {
            // No icon provided → use default by action (edit/view/details/delete/active/inactive)
            IconComp = registry.value[item.action];
        }

        return { ...item, _icon: IconComp };
    })
);

// --- existing code below, unchanged ---
const computedStyle = computed(() => ({
    top: positionAbove.value ? "auto" : "100%",
    bottom: positionAbove.value ? "100%" : "auto",
    marginBottom: positionAbove.value ? "8px" : "0",
    marginTop: positionAbove.value ? "0" : "8px",
}));

const handleAction = (action, selectedRowId) => {
    showModal.value = false;
    emit("action", action, selectedRowId);
};

const onClickAway = () => {
    showModal.value = false;
    emit("close");
};

onMounted(() => {
    const panelRect = panel.value.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    positionAbove.value = panelRect.bottom + 150 > viewportHeight;
});
</script>
