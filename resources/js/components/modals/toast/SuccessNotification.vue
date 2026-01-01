<template>
    <div
        v-if="show && !isLoading"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-[100000]"
        @click.self="closeModal"
    >
        <div
            class="notification-modal success-modal"
            @mouseenter="pauseTimer"
            @mouseleave="resumeTimer"
        >
            <div class="notification flex justify-between">
                <div class="body flex gap-3 items-center">
                    <div class="p-1.5 bg-[#086E3E] rounded-3xl">
                        <SuccessIcon />
                    </div>
                    <div class="flex flex-col">
                        <p
                            class="notification-title"
                            v-trans="params.title ?? 'Successful!'"
                        ></p>
                        <p
                            class="notification-message"
                            v-trans="params.message ?? ''"
                        ></p>
                    </div>
                </div>
                <div
                    class="close flex justify-end items-center text-gray-500 hover:cursor-pointer"
                >
                    <CloseIcon @click="closeModal" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import SuccessIcon from "@/components/icons/modal/SuccessIcon.vue";
import CloseIcon from "@/components/icons/modal/CloseIcon.vue";
import { isLoading } from "@/globalLoader";

const show = ref(false);
const params = ref({});
let timer = null;
let startTime = null;
let remaining = null;

const router = useRouter();
let removeHook = null;

const open = (options = {}) => {
    params.value = options;
    show.value = true;

    const duration = options.duration ?? 3000;
    remaining = duration;
    startTimer();
};

const closeModal = () => {
    show.value = false;
    clearTimeout(timer);
};
const startTimer = () => {
    startTime = Date.now();
    clearTimeout(timer);
    timer = setTimeout(() => {
        closeModal();
    }, remaining);
};

const pauseTimer = () => {
    clearTimeout(timer);
    if (startTime) {
        const elapsed = Date.now() - startTime;
        remaining = remaining - elapsed;
    }
};

const resumeTimer = () => {
    startTimer();
};
onMounted(() => {
    removeHook = router.afterEach((to, from) => {
        const toParent = to.matched[0]?.path;
        const fromParent = from.matched[0]?.path;

        if (toParent !== fromParent) {
            closeModal();
        }
    });
});

onBeforeUnmount(() => {
    if (removeHook) removeHook();
    clearTimeout(timer);
});

defineExpose({ open });
</script>

<style scoped>
.notification-modal {
    width: 400px;
    max-width: 100%;
    padding: 16px 24px;
    background: white;
    border-radius: 1rem;
}

.success-modal {
    filter: drop-shadow(4px 0px 0px #57d05b);
    box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.25);
}

.body {
    color: white;
}

.notification-title {
    color: black;
    font-family: Poppins;
    font-size: 14px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
}

.notification-message {
    color: black;
    font-family: Poppins;
    font-size: 15px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
}
</style>
