<template>
    <div>
        <div class="flex items-center justify-between">
            <div
                class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[48px]"
            >
                {{ $t("Questions") }}
            </div>
            <Button
                :show="true"
                title="add_question"
                :to="{ name: 'QuestionCreate' }"
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
                    @delete="handleDelete"
                    @togglePublish="handleTogglePublish"
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

const notify = useNotify();

const items = ref([]);
const loading = ref(false);
const error = ref("");
const emitter = inject("emitter");
const showConfirmationModal = ref(false);
const columns = [
    { key: "question", label: "question", sortable: true, width: "200px" },
    { key: "type", label: "type", sortable: false, width: "80px" },
    { key: "mark", label: "mark", sortable: true, width: "50px" },
    { key: "options_count", label: "options", sortable: false, width: "50px" },
    { key: "published", label: "published", sortable: false, width: "50px" },
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
        const res = await axios.get(route("questions.index"), {
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
            route("questions.destroy", {
                question: deleted_item.value?.id,
            })
        );

        showConfirmationModal.value = false;
        items.value = items.value.filter(
            (item) => item.id !== deleted_item.value?.id
        );

        notify.success({
            message: "success_question_deleted",
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
            name: item.question.substring(0, 50) + "...",
        }
    );
    showConfirmationModal.value = true;
};

const handleTogglePublish = async (item) => {
    try {
        await axios.put(route("questions.update", { question: item.id }), {
            published: !item.published,
        });

        const index = items.value.findIndex((i) => i.id === item.id);
        if (index !== -1) {
            items.value[index].published = !item.published;
        }

        notify.success({
            message: "success_question_updated",
        });
    } catch (error) {
        // console.log(error);
    }
};

onBeforeUnmount(() => {
    if (emitter) {
        emitter.off("refetch");
    }
});
</script>
