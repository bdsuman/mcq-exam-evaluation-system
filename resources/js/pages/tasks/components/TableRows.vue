<template>
  <tbody>
    <tr v-for="(item, idx) in items" :key="item.id ?? idx">
      <td :style="{ width: getWidth('title') }">
        <span :class="item.is_completed ? 'line-through text-slate-400' : ''">
          {{ item.title }}
        </span>
      </td>

      <td :style="{ width: getWidth('description') }">
        <div
          @click="emit('openDescription', item.description)"
          :class="[
            'cursor-pointer font-medium text-sm leading-[130%] tracking-[0] underline underline-offset-4 decoration-solid [text-decoration-skip-ink:auto] text-emerald-700 hover:text-emerald-900',
            item.is_completed ? 'line-through text-slate-400 hover:text-slate-500' : ''
          ]"
        >
          {{ $t("view_description") }}
        </div>
      </td>
      <td :style="{ width: getWidth('completed') }">
        <ToggleSwitch
          :modelValue="item.is_completed"
          @change="emit('complete', item)" />
      </td>
      <td :style="{ width: getWidth('image') }">
        <ImageWithPreview
          v-if="item.avatar_url"
          :src="item.avatar_url"
          :showIcon="false" />
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
            :actionItems="getActionItems(item.status, item.role)"
            @action="handleAction"
            @close="toggleActionPanel"
            :status="item.status" />
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
import ImageWithPreview from "@/components/form/ImageWithPreview.vue";
const props = defineProps({
  items: { type: Array, default: () => [] },
  columns: { type: Array, required: true },
});

const emit = defineEmits([
  "refetch",
  "action",
  "delete",
  "complete",
  "openDescription",
]);

function getWidth(key) {
  const col = props.columns.find((c) => c.key === key);
  return col?.width || "auto";
}

const showConfirmationModal = ref(false);
const selectedRowId = ref(null);
const showActionPanel = ref(false);
const router = useRouter();
function getActionItems(status, role) {
  let actionItems = [
    { label: "Edit", action: "edit" },
    { label: "View", action: "details" },
    { label: "Delete", action: "delete" },
  ];

  return actionItems;
}
const getItemById = (id) =>
  computed(() => props.items.find((item) => item.id === id));
const toggleActionPanel = (itemId) => {
  selectedRowId.value = itemId;
  showActionPanel.value = !showActionPanel.value;
};

const itemId = ref(null);
const actionType = ref(null);
const handleAction = (action, id) => {
  actionType.value = action;
  if (action === "details") {
    itemId.value = id;
    router.push({ name: "TaskDetails", params: { id } });
  }
  if (action === "edit") {
    itemId.value = id;
    router.push({ name: "TaskUpdate", params: { id } });
  }
  if (action === "delete") {
    emit("delete", getItemById(id).value);
    showConfirmationModal.value = true;
  }

  selectedRowId.value = null;
  showActionPanel.value = false;
};
</script>
