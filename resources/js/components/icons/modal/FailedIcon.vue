<template>
    <svg
        :width="width"
        :height="height"
        viewBox="0 0 20 20"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M18.9243 10C18.9243 5.07137 14.9288 1.07593 10.0002 1.07593C5.07161 1.07593 1.07617 5.07137 1.07617 10C1.07617 14.9286 5.07161 18.9241 10.0002 18.9241C14.9288 18.9241 18.9243 14.9286 18.9243 10Z"
            fill="white"
        />
        <path
            d="M13.0005 13.0011L6.99902 6.99902M6.99966 13.0011L13.0011 6.99902"
            stroke="#E73238"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>

<script>
export default {
    props: {
        width: {
            type: Number,
            default: 20,
        },
        height: {
            type: Number,
            default: 20,
        },
    },
};
</script>

<style lang="scss" scoped></style>
