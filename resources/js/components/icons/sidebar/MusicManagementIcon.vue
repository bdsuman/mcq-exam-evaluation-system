<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
    >
        <!-- Wheels -->
        <path
            d="M6.5 22C8.433 22 10 20.433 10 18.5C10 16.567 8.433 15 6.5 15C4.567 15 3 16.567 3 18.5C3 20.433 4.567 22 6.5 22Z"
            :fill="fill"
            :stroke="stroke || fill"
            stroke-width="2"
        />
        <path
            d="M18 19C19.6569 19 21 17.6569 21 16C21 14.3431 20.317 13 18 13C16.3431 13 15 14.3431 15 16C15 17.6569 16.3431 19 18 19Z"
            :fill="fill"
            :stroke="stroke || fill"
            stroke-width="2"
        />
        <!-- Body -->
        <path
            d="M10 18.5004V7.00044C10 6.07699 10 5.61527 10.2635 5.32838C10.5269 5.0415 11.0175 4.99964 11.9986 4.91593C16.022 4.57266 18.909 3.26049 20.3553 2.41022C20.6508 2.23644 20.7986 2.14956 20.8993 2.20716C21 2.26476 21 2.43194 21 2.76631V16.0004"
            fill="none"
            :stroke="stroke || fill"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="M10 10C15.8667 10 19.7778 7.66667 21 7"
            fill="none"
            :stroke="stroke || fill"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>

<script setup>
defineProps({
    fill: {
        type: String,
        default: "none",
    },
    stroke: {
        type: String,
        default: "white",
    },
});
</script>
