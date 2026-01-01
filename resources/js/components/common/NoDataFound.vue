<template>
    <div class="flex flex-col items-center justify-center text-center p-8">
        <!-- Animated Icon -->
        <div class="animate-bounce mb-4">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-16 w-16 text-gray-400"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
            >
                <!-- Circle (lens) -->
                <circle cx="11" cy="11" r="7" />
                <!-- Handle -->
                <line
                    x1="16.5"
                    y1="16.5"
                    x2="21"
                    y2="21"
                    stroke-linecap="round"
                />
            </svg>
        </div>

        <!-- Message -->
        <h2 class="text-lg font-semibold text-gray-700">
            {{ $t("No Data Found") }}
        </h2>
        <p class="text-gray-500 mt-1 mb-4">
            {{
                $t(
                    "It looks a little empty here. Try adding new data or refreshing."
                )
            }}
        </p>
    </div>
</template>

<style scoped>
/* Add some subtle floating animation */
@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-6px);
    }
}

.animate-bounce {
    animation: float 2s ease-in-out infinite;
}
</style>
