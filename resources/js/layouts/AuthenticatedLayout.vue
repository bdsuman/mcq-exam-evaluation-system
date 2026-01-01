<template>
    <div class="flex h-screen overflow-hidden">
        <SideBar :sidebarCollapse="sidebarCollapse" />

        <!-- Right side -->
        <div class="flex flex-col flex-1 bg-body">
            <!-- Top bar stays at top -->
            <TopBar
                :sidebarCollapse="sidebarCollapse"
                @toggle-sidebar="toggleSidebar"
            />
            <!-- Scroll ONLY this area; fills all remaining height -->
            <main
                class="flex-1 overflow-y-auto px-[36px] pt-[70px]"
                :class="
                    sidebarCollapse
                        ? 'w-[calc(100vw-100px)]'
                        : 'w-[calc(100vw-280px)]'
                "
            >
                <router-view />
            </main>
        </div>

        <SuccessNotification ref="successRef" />
        <FailNotification ref="failRef" />
        <GlobalLoader />
    </div>
</template>

<script setup>
import SideBar from "@/layouts/includes/sidebar/Sidebar.vue";
import TopBar from "@/layouts/includes/header/TopBar.vue";
import GlobalLoader from "@/components/common/Loader.vue";
import { ref, onMounted, onBeforeUnmount } from "vue";
import SuccessNotification from "@/components/modals/toast/SuccessNotification.vue";
import FailNotification from "@/components/modals/toast/FailNotification.vue";
import { useNotificationRefs } from "@/composables/useNotification";

const { successRef, failRef } = useNotificationRefs();

const props = defineProps({
    langChange: {
        type: String,
        default: "de",
    },
});

const sidebarCollapse = ref(localStorage.getItem("sidebarCollapse") === "true");

const currentLanguage = ref(props.langChange);

const handleCurrentLanguage = (value) => {
    currentLanguage.value = value;
};

const toggleSidebar = () => {
    sidebarCollapse.value = !sidebarCollapse.value;
    localStorage.setItem("sidebarCollapse", sidebarCollapse.value);
};

// ✅ Auto-collapse logic
const handleResize = () => {
    if (window.innerWidth < 1024) {
        // e.g. collapse if width < 1024px
        sidebarCollapse.value = true;
    } else {
        sidebarCollapse.value =
            localStorage.getItem("sidebarCollapse") === "true";
    }
};

onMounted(() => {
    handleResize(); // check immediately on mount
    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});
</script>
