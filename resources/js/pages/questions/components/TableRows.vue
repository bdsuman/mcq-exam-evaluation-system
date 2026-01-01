<template>
  <tbody>
    <tr v-for="(item, idx) in items" :key="item.id ?? idx">
      <td :style="{ width: getWidth('question') }">
        <span :class="!item.published ? 'text-slate-400' : ''">
          {{ truncate(item.question, 80) }}
        </span>
      </td>

      <td :style="{ width: getWidth('type') }">
        <span
          class="px-2 py-1 text-xs rounded-full"
          :class="
            item.type === 'single_choice'
              ? 'bg-blue-100 text-blue-800'
              : 'bg-purple-100 text-purple-800'
          ">
          {{ formatType(item.type) }}
        </span>
      </td>

      <td :style="{ width: getWidth('mark') }">
        <span class="font-medium">{{ item.mark }}</span>
      </td>

      <td :style="{ width: getWidth('options_count') }">
        <span class="text-gray-600">{{ item.options_count || 0 }}</span>
      </td>

      <td :style="{ width: getWidth('published') }">
        <ToggleSwitch
          :modelValue="item.published"
          @change="emit('togglePublish', item)" />
      </td>

      <td :style="{ width: getWidth('action') }">
        <div :class="showActionPanel ? 'relative' : ''">
          <ThreeDotsVerticalIcon
            :color="
              selectedRowId === item.id && showActionPanel
                ? '#00A280'
                : '#585755'
            "
            @click="toggleActionPanel(item.id)"
            class="cursor-pointer" />
          <ActionPanel
            v-if="selectedRowId === item.id && showActionPanel"
            :selectedRowId="item.id"
            positionClass=""
            :show="true"
            :actionItems="getActionItems()"
            @action="handleAction"
            @close="toggleActionPanel" />
        </div>
      </td>
    </tr>
  </tbody>
</template>

<script setup>
import { ref, computed } from "vue";
import ThreeDotsVerticalIcon from "@/components/icons/ThreeDotsVerticalIcon.vue";
import ActionPanel from "@/components/form/ActionPanel.vue";
import { useRouter } from "vue-router";
import ToggleSwitch from "@/components/form/ToggleSwitch.vue";

const props = defineProps({
  items: { type: Array, default: () => [] },
  columns: { type: Array, required: true },
});

const emit = defineEmits([
  "refetch",
  "action",
  "delete",
  "togglePublish",
]);

function getWidth(key) {
  const col = props.columns.find((c) => c.key === key);
  return col?.width || "auto";
}

const truncate = (text, length) => {
  if (!text) return "";
  return text.length > length ? text.substring(0, length) + "..." : text;
};

const formatType = (type) => {
  if (type === "single_choice") return "Single";
  if (type === "multiple_choice") return "Multiple";
  return type;
};

const showConfirmationModal = ref(false);
const selectedRowId = ref(null);
const showActionPanel = ref(false);
const router = useRouter();

function getActionItems() {
  return [
    { label: "Edit", action: "edit" },
    { label: "View", action: "details" },
    { label: "Delete", action: "delete" },
  ];
}

const getItemById = (id) =>
  computed(() => props.items.find((item) => item.id === id));

const toggleActionPanel = (itemId) => {
  selectedRowId.value = itemId;
  showActionPanel.value = !showActionPanel.value;
};

const handleAction = (action, itemId) => {
  const item = getItemById(itemId).value;

  if (action === "edit") {
    router.push({
      name: "QuestionUpdate",
      params: { id: itemId },
    });
  } else if (action === "details") {
    router.push({
      name: "QuestionDetails",
      params: { id: itemId },
    });
  } else if (action === "delete") {
    emit("delete", item);
  }

  showActionPanel.value = false;
  selectedRowId.value = null;
};
</script>
