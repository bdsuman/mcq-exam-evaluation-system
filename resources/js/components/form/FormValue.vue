<template>
  <div class="text-wrap flex flex-col gap-[12px]">
    <div class="size-lf-stretch justify-start text-[#585b5a] text-sm font-normal leading-[18.20px]" :class="$attrs.labelClass">
      <slot name="label">
        {{ $t(label) }}
      </slot>
    </div>
    <div
      v-if="html"
      class="self-stretch justify-start text-[#002d45] text-base font-medium leading-tight"
      :class="$attrs.valueClass"
      v-html="value"
    ></div>
    <div v-else class="self-stretch justify-start text-[#002d45] text-base font-medium leading-tight" :class="$attrs.valueClass">
      <slot name="value">
        {{ translate ? $t(value ?? "--") : value }}
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  components: {
  },
  props: {
    label: {
      type: String,
      default: "",
    },
    value: {
      type: [String, Number],
      default: "",
    },
    translate: {
      type: Boolean,
      default: false,
    },
    html: {
      type: Boolean,
      default: false,
    },
    path: {
      type: [String, Number],
      default: "",
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
