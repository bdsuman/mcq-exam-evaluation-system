<template>
    <div
        class="size- inline-flex justify-start items-center gap-3"
    >
        <div
            class="size- p-2 bg-[#005766] rounded-[100px] shadow-[0px_4px_70px_0px_rgba(171,176,172,0.25)] flex justify-start items-start cursor-pointer"
            @click="goBack"
        >
            <div class="size-4 relative overflow-hidden">
                <LeftArrowIcon />
            </div>
        </div>
        <div
            class="justify-center text-[#3e3d3c] text-sm font-medium font-['Poppins'] leading-[18.20px]"
        >
            {{ $t('Back') }}
        </div>
    </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import LeftArrowIcon from "@/components/icons/LeftArrowIcon.vue";

const router = useRouter();

const props = defineProps({
  to: { type: [String, Object], default: null },
  replace: { type: Boolean, default: false },
});

const goBack = () => {
  if (props.to) {
    const location = typeof props.to === 'string' ? { name: props.to } : props.to;
    return props.replace ? router.replace(location) : router.push(location);
  }
  router.back();
};
</script>
