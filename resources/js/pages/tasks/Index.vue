<template>
    <div>
        <div class="flex items-center justify-between">
            <div
                class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[48px]"
            >
                {{ $t("Tasks") }}
            </div>
            <Button
                :show="true"
                title="add_task"
                :to="{ name: 'TaskCreate' }"
            />
        </div>

        <BaseTable
            routeSync
            :columns="columns"
            :total="total"
            v-model:page="page"
            v-model:pageSize="pageSize"
            v-model:search="search"
            :defaultSort="{ by: 'id', dir: 'desc' }"
            :enableSearch="true"
            :searchPlaceholder="$t('search_here')"
            :enableSort="false"
            :stickyHeader="true"
            @request="fetchData"
        >
            <template #default>
                <TableRows
                    :items="items"
                    :columns="columns"
                    @openDescription="openDescription"
                    @delete="handleDelete"
                    @complete="handleComplete"
                />
            </template>
        </BaseTable>
    </div>
    <ConfirmationModal
        :show="showConfirmationModal"
        title="delete"
        :message="deleted_message"
        cancelText="no"
        confirmText="yes"
        @close="showConfirmationModal = false"
        @confirm="actionDelete"
    />
    <BaseModal
        :show="isModalOpen"
        title="Description"
        :width="665"
        @close="isModalOpen = false"
    >
        <template #body>
            {{ selectedDescription }}
        </template>
    </BaseModal>
</template>

<script setup>
import { ref, inject, onBeforeUnmount } from "vue";
import axios from "axios";
import Button from "@/components/buttons/Button.vue";
import BaseTable from "@/components/table/BaseTable.vue";
import TableRows from "./components/TableRows.vue";
import { useNotify } from "@/composables/useNotification";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import { trans } from "laravel-vue-i18n";
import BaseModal from "@/components/modals/BaseModal.vue";

const notify = useNotify();

const items = ref([]);
const loading = ref(false);
const error = ref("");
const emitter = inject("emitter");
const showConfirmationModal = ref(false);
const columns = [
    { key: "title", label: "title", sortable: true, width: "100px" },
    { key: "description", label: "description", sortable: false, width: "50px" },
    { key: "completed", label: "completed", sortable: false, width: "50px" },
    { key: "image", label: "image", sortable: true, width: "50px" },
    { key: "action", label: "", sortable: false, width: "20px" },
];

const page = ref(1);
const pageSize = ref(10);
const search = ref("");
const total = ref(0);

async function fetchData({ page: p, pageSize: ps, sort, search: q }) {
    loading.value = true;
    error.value = "";
    try {
        const res = await axios.get(route("tasks.index"), {
            params: {
                page: p,
                per_page: ps,
                sort_by: sort.by,
                sort_dir: sort.dir,
                search: q || "",
            },
        });

        items.value = Array.isArray(res.data?.data) ? res.data.data : [];

        const meta = res.data?.response?.meta ?? res.data?.meta ?? {};
        total.value = meta.total ?? 0;
        page.value = meta.current_page ?? p;
        pageSize.value = meta.per_page ?? ps;
    } catch (e) {
        error.value = "failed_to_fetch_items";
    } finally {
        loading.value = false;
    }
}

const deleted_item = ref(null);
const deleted_message = ref(null);

const actionDelete = async () => {
    try {
        await axios.delete(
            route("tasks.destroy", {
                task: deleted_item.value?.id,
            })
        );

        showConfirmationModal.value = false;
        items.value = items.value.filter(
            (item) => item.id !== deleted_item.value?.id
        );

        notify.success({
            message: "success_story_deleted",
        });
    } catch (error) {
        // console.log(error);
    }
};
const handleDelete = (item) => {
    deleted_item.value = item;
    deleted_message.value = trans(
        "are_you_sure_you_want_to_delete_:name_?",
        {
            name: item.title,
        }
    );
    showConfirmationModal.value = true;
};

const handleComplete = async (data) => {
    try {
        const res = await axios.put(
            route("tasks.completed", { task: data.id })
        );

        items.value = items.value.map((item) =>
            item.id === data.id ? res.data.data : item
        );

        const completedStatus = res.data.data.is_completed ? "success_task_completed_successfully" : "success_task_uncompleted_successfully";

        notify.success({
            message: completedStatus,
        });
    } catch (error) {
        // console.log(error);
    }
};

const selectedDescription = ref(null);
const isModalOpen = ref(false);

const openDescription = (text) => {
    selectedDescription.value = text ?? "";
    isModalOpen.value = true;
};
emitter.on("languageChanged", async () => {
    await fetchData({
        page: page.value,
        pageSize: pageSize.value,
        sort: { by: "id", dir: "desc" },
        search: search.value,
    });
});

onBeforeUnmount(() => {
    emitter.off("languageChanged");
});
</script>
