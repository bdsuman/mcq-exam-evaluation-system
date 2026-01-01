<template>
    <div class="bg-sidebar to-green-500 relative">
        <transition name="sidebar-collapse">
            <div
                :class="[
                    props.sidebarCollapse
                        ? 'max-w-[100px] min-w-[100px]'
                        : 'max-w-[280px] min-w-[280px]',
                    'flex-col justify-start items-center flex relative',
                ]"
            >
                <router-link :to="{ name: 'home' }" class="cursor-pointer py-8">
                    <Logo v-if="props.sidebarCollapse" />
                    <LogoFull class="w-[90%] mx-auto" v-else />
                </router-link>

                <div
                    class="flex flex-col gap-[12px] w-[100%] max-w-[100%] h-[70vh] overflow-y-auto hide-scrollbar"
                >
                    <template v-for="(tab, index) in tabs" :key="index">
                        <!-- PARENT WITH CHILDREN: NOT A LINK; CLICK/KEY/HOVER ONLY -->
                        <div
                            v-if="hasChildren(tab)"
                            :class="{
                                'bg-[#8BEE8233]': isParentRouteIndex(index),
                            }"
                            @mouseenter="hoveredIndex = index"
                            @mouseleave="hoveredIndex = null"
                            @click.stop.prevent="toggleOpen(index)"
                            @keydown.enter.prevent="toggleOpen(index)"
                            @keydown.space.prevent="toggleOpen(index)"
                            role="button"
                            tabindex="0"
                            aria-haspopup="true"
                            :aria-expanded="isOpen(index)"
                        >
                            <div
                                class="group top-[0.77px] flex items-center cursor-pointer h-[60px] hover:bg-[#8BEE8233] transition-all"
                            >
                                <!-- left highlight -->
                                <div
                                    v-if="
                                        isOpen(index) ||
                                        isParentRouteIndex(index)
                                    "
                                    class="w-2 h-[60px] left-0 absolute bg-[#8BEE82] rounded-r-lg"
                                />

                                <!-- main row -->
                                <div
                                    class="w-[280px] pl-7 pr-3.5 py-4 inline-flex justify-between items-center"
                                >
                                    <div
                                        class="flex justify-start items-center gap-2"
                                    >
                                        <div class="size-6">
                                            <component
                                                :is="tab.iconComponent"
                                                :fill="
                                                    isParentRouteIndex(index)
                                                        ? '#8BEE82'
                                                        : 'none'
                                                "
                                                :stroke="
                                                    isParentRouteIndex(index)
                                                        ? ''
                                                        : 'white'
                                                "
                                            />
                                            <Tooltip
                                                v-if="props.sidebarCollapse"
                                                :label="tab.label"
                                                :show="hoveredIndex === index"
                                                class="absolute left-[100%] ms-[2px] whitespace-nowrap z-50"
                                            />
                                        </div>
                                        <div
                                            v-if="!props.sidebarCollapse"
                                            class="text-center justify-start text-base font-normal capitalize"
                                            :class="
                                                isParentRouteIndex(index)
                                                    ? 'text-[#87DD05]'
                                                    : 'text-white'
                                            "
                                        >
                                            {{ $t(tab.label) }}
                                        </div>
                                    </div>

                                    <!-- right chevron -->
                                    <div class="ml-2">
                                        <RightArrowIcon />
                                    </div>
                                </div>

                                <!-- submenu flyout (only on hover/click; NOT just because a child is active) -->
                                <div
                                    class="absolute left-full min-w-[240px] z-50 transition-all duration-200 ease-out opacity-0 translate-x-2 pointer-events-none group-hover:opacity-100 group-hover:translate-x-0 group-hover:pointer-events-auto"
                                    :class="
                                        isOpen(index)
                                            ? 'opacity-100 translate-x-0 pointer-events-auto'
                                            : ''
                                    "
                                    @mouseenter="hoveredIndex = index"
                                    @mouseleave="
                                        hoveredIndex = null;
                                        openIndex = null;
                                    "
                                >
                                    <div
                                        class="p-3 bg-secondary inline-flex flex-col shadow-lg border-l-2 border-[#8BEE82] min-w-[180px]"
                                    >
                                        <router-link
                                            v-for="(
                                                child, cidx
                                            ) in tab.children"
                                            :key="cidx"
                                            :to="child.route[0]"
                                            @click.prevent="
                                                handleSubmenuClick(index)
                                            "
                                            class="self-stretch p-4 inline-flex justify-start items-center gap-2 text-sm capitalize transition-colors rounded-lg"
                                            :class="[
                                                isActiveChild(child)
                                                    ? 'bg-[#8BEE82]/40 text-white'
                                                    : 'text-white hover:bg-[#8BEE82]/20 hover:text-white/95',
                                            ]"
                                        >
                                            <span
                                                class="size-2 bg-white rounded-full"
                                            ></span>
                                            <span
                                                class="text-white font-normal font-poppins"
                                                >{{ $t(child.label) }}</span
                                            >
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LEAF (NO CHILDREN): REGULAR ROUTER-LINK -->
                        <router-link
                            v-else
                            :to="tab.route[0]"
                            @mouseover="hoveredIndex = index"
                            @mouseleave="hoveredIndex = null"
                            :class="{
                                'bg-[#8BEE8233]': isParentRouteIndex(index),
                            }"
                        >
                            <div
                                class="group top-[0.77px] flex items-center cursor-pointer h-[60px] hover:bg-[#8BEE8233] transition-all"
                            >
                                <!-- left highlight -->
                                <div
                                    v-if="
                                        isParentRouteIndex(index) ||
                                        hoveredIndex === index
                                    "
                                    class="w-2 h-[60px] left-0 absolute bg-[#8BEE82] rounded-r-lg"
                                />
                                <!-- main row -->
                                <div
                                    class="w-[280px] pl-7 pr-3.5 py-4 inline-flex justify-between items-center"
                                >
                                    <div
                                        class="flex justify-start items-center gap-2"
                                    >
                                        <div class="size-6 flex">
                                            <component
                                                :is="tab.iconComponent"
                                                :fill="
                                                    isParentRouteIndex(index)
                                                        ? '#8BEE82'
                                                        : 'none'
                                                "
                                                :stroke="
                                                    isParentRouteIndex(index)
                                                        ? ''
                                                        : 'white'
                                                "
                                            />
                                            <Tooltip
                                                v-if="props.sidebarCollapse"
                                                :label="tab.label"
                                                :show="hoveredIndex === index"
                                                class="absolute left-[100%] ms-[2px] whitespace-nowrap z-50"
                                            />
                                        </div>
                                        <div
                                            v-if="!props.sidebarCollapse"
                                            class="text-center justify-start text-base font-normal capitalize"
                                            :class="
                                                isParentRouteIndex(index)
                                                    ? 'text-[#87DD05]'
                                                    : 'text-white'
                                            "
                                        >
                                            {{ $t(tab.label) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                    </template>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { shallowRef, onMounted, ref, watch, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

import Logo from "@/components/common/Logo.vue";
import LogoFull from "@/components/common/Logo.vue";
import Tooltip from "@/layouts/includes/sidebar/Tooltip.vue";

import UserManagementIcon from "@/components/icons/sidebar/UserManagementIcon.vue";
import RightArrowIcon from "@/components/icons/sidebar/RightArrowIcon.vue";

import { useUserStore } from "@/stores/useUserStore";

const props = defineProps({
    sidebarCollapse: { type: Boolean, required: true },
});
const emit = defineEmits(["toggle-sidebar"]);
const sidebarCollapse = shallowRef(props.sidebarCollapse);

const activeTab = ref(0);
const hoveredIndex = ref(null);
const openIndex = ref(null);

const route = useRoute();
const store = useUserStore();

const restrictedModules = store.user?.restrict_module_access || [];

// Mapping module names to their associated route paths
const restrictedRoutesMap = {
    // shopping_list_and_order_management: ["/shopping-list", "/orders", "/order-management"],
    // menus: ["/menus"],
};

// Collect restricted routes
const restrictedRoutes = restrictedModules.flatMap(
    (module) => restrictedRoutesMap[module] || []
);

const allTabs = [
    {
        label: "Task Management",
        route: ["/", "/tasks"],
        iconComponent: UserManagementIcon,
    },
];

// Filter tabs based on restrictedRoutes and role
const tabs = computed(() => {
    return allTabs.filter((tab) => {
        const isRouteRestricted = tab.route.some((r) =>
            restrictedRoutes.includes(r)
        );

        const role = store.user?.role;

        // Hide User Management for normal users
        const isRestrictedForUserRole =
            role === "user" && tab.label === "Task Management";

        // Show Dummy Components only for dev users
        const isRestrictedForNonDev =
            tab.label === "Dummy Components" && role !== "dev";

        return (
            !isRouteRestricted &&
            !isRestrictedForUserRole &&
            !isRestrictedForNonDev
        );
    });
});

// ---------- Route matching helpers (fixed) ----------
const matchesRoute = (path, base) => {
    // "/" should only match the home page
    if (base === "/") return path === "/";
    // match exact base or any nested path under it
    return path === base || path.startsWith(base + "/");
};

const hasChildren = (t) => Array.isArray(t.children) && t.children.length > 0;

const isParentRoute = (tab) =>
    Array.isArray(tab.route) &&
    tab.route.some((r) => matchesRoute(route.path, r));

const isParentRouteIndex = (i) => isParentRoute(tabs.value[i]);

const pathEquals = (a, b) => a.replace(/\/+$/, "") === b.replace(/\/+$/, "");
const isActiveChild = (child) => pathEquals(route.path, child.route[0]);

const toggleOpen = (i) => {
    openIndex.value = openIndex.value === i ? null : i;
};

const closeMenus = () => {
    openIndex.value = null;
    hoveredIndex.value = null;
};

// Handle submenu click - close menus before navigation
const handleSubmenuClick = (index) => {
    // Close all menus immediately
    hoveredIndex.value = null;
    openIndex.value = null;
};

// submenu only opens on hover or manual click toggle
const isOpen = (i) => hoveredIndex.value === i || openIndex.value === i;

// Watch for route changes to highlight correct parent; do NOT keep submenu open
watch(
    () => route.path,
    () => {
        let matchedIndex = -1;
        tabs.value.forEach((tab, index) => {
            if (isParentRoute(tab)) matchedIndex = index;
        });
        activeTab.value = matchedIndex !== -1 ? matchedIndex : 0;
        // close any open/hovered menus after navigation so submenu isn't forced open
        openIndex.value = null;
        hoveredIndex.value = null;
    },
    { immediate: true }
);

onMounted(() => {
    tabs.value.forEach((tab, index) => {
        if (isParentRoute(tab)) {
            activeTab.value = index;
        }
    });
});

const handleLogout = async () => {
    await axios.post("/logout");
    await store.logout();
    window.location.href = "/login";
};
</script>

<style scoped>
.bg-sidebar {
    background: linear-gradient(178deg, #005766 2.35%, #3ab57c 121.22%), #fff;
}

.hide-scrollbar {
    scrollbar-width: none;
    /* Firefox */
    -ms-overflow-style: none;
    /* IE and Edge */
}

.hide-scrollbar::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari, Opera */
}
</style>
