<template>
  <div>
    <GoBack class="mt-2 mb-[22px]" />
    <div class="flex items-center justify-between">
      <div
        class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]">
        {{ $t("task_details") }}
      </div>
    </div>
    <div class="self-stretch p-8 bg-white rounded-2xl w-full">
      <div class="form-row-grid">
        <FormValue label="title" :value="data.title" />
        <FormValue label="description" :value="data.description" />
        <ImageWithPreview
          v-if="data.avatar_url"
          :src="data.avatar_url"
          :label="$t('image')" />
      </div>
      <div class="flex justify-end !items-end h-full">
        <Button
          title="edit"
          :width="'w-40'"
          :to="{ name: 'TaskUpdate', params: { id: data.id } }"
          :bgClass="'bg-[#C1FDDA]'"
          :textColor="'text-[#2C6456]'" />
      </div>
    </div>
  </div>
</template>
<script setup>
import axios from "axios";
import GoBack from "@/components/common/GoBack.vue";
import FormValue from "@/components/form/FormValue.vue";
import { onBeforeMount, reactive } from "vue";
import { useRoute } from "vue-router";
import ImageWithPreview from "@/components/form/ImageWithPreview.vue";
import Button from "@/components/buttons/Button.vue";

const vRoute = useRoute();

const data = reactive({});

const getDetails = async () => {
  try {
    const res = await axios.get(
      route("tasks.show", {
        task: vRoute.params.id,
      })
    );
    Object.assign(data, res.data?.data);
  } catch (error) {
    // console.log(error);
  }
};
onBeforeMount(async () => {
  await getDetails();
});
</script>
