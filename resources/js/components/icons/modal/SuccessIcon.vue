<template>
    <svg
        :width="width"
        :height="height"
        viewBox="0 0 20 20"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M18.3337 10.0001C18.3337 5.39771 14.6027 1.66675 10.0003 1.66675C5.39795 1.66675 1.66699 5.39771 1.66699 10.0001C1.66699 14.6024 5.39795 18.3334 10.0003 18.3334C14.6027 18.3334 18.3337 14.6024 18.3337 10.0001Z"
            fill="white"
            stroke="white"
            stroke-width="1.25"
        />
        <path
            d="M6.66699 10.6251C6.66699 10.6251 8.00033 11.3855 8.66699 12.5001C8.66699 12.5001 10.667 8.12508 13.3337 6.66675"
            stroke="#086E3E"
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
            type: [String, Number],
            default: 25,
        },
        height: {
            type: [String, Number],
            default: 25,
        },
    },
};
</script>

<style lang="scss" scoped></style>
