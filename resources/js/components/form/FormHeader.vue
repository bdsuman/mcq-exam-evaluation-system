<template>
  <div class="form-row-header">
    <div class="self-stretch h-[15px] justify-start text-[#002d45] text-xl font-semibold leading-relaxed" :class="$attrs.labelClass">
      <slot name="label">
        {{ $t(label) }}
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: "",
    },
    translate: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style lang="scss" scoped>
.form-row-header {
  margin-bottom: 24px;
}
</style>
