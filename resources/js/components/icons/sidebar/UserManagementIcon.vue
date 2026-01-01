<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="25"
        height="25"
        viewBox="0 0 25 25"
        :fill="fill"
    >
        <path
            d="M16.8296 6.64492C16.8296 9.20767 14.7521 11.2852 12.1893 11.2852C9.62659 11.2852 7.54907 9.20767 7.54907 6.64492C7.54907 4.08216 9.62659 2.00464 12.1893 2.00464C14.7521 2.00464 16.8296 4.08216 16.8296 6.64492Z"
            :stroke="stroke"
            stroke-width="2"
        />
        <path
            d="M14.5095 14.7654H9.86919C6.66576 14.7654 4.06885 17.3623 4.06885 20.5657C4.06885 21.8471 5.10761 22.8859 6.38899 22.8859H17.9897C19.2711 22.8859 20.3098 21.8471 20.3098 20.5657C20.3098 17.3623 17.7129 14.7654 14.5095 14.7654Z"
            :stroke="stroke"
            stroke-width="2"
            stroke-linejoin="round"
        />
    </svg>
</template>
<script setup>
defineProps({
    fill: {
        type: String,
        default: "null",
    },
    stroke: {
        type: String,
        default: "white",
    },
});
</script>
