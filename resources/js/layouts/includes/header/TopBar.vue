<template>
    <nav
        class="fixed top-0 z-10 bg-body"
        :class="
            props.sidebarCollapse
                ? 'left-[100px] w-[calc(100%-100px)]'
                : 'left-[280px] w-[calc(100%-280px)]'
        "
    >
        <div class="mx-auto">
            <div
                class="relative flex w-full items-center justify-between h-[36px] my-[20px] px-8"
            >
                <!-- Left: sidebar toggler -->
                <TogglerIcon
                    :collapsed="props.sidebarCollapse"
                    class="cursor-pointer min-w-5 w-5"
                    @click="$emit('toggle-sidebar')"
                />

                <!-- Right: user menu -->
                <div class="relative flex items-center gap-3">
                    <!-- Avatar -->
                    <img
                        :src="userPhoto"
                        alt="User"
                        class="size-12 rounded-full object-cover ring-2 ring-white/20"
                    />
                    <!-- <AvatarIcon class="size-12" /> -->

                    <!-- name + role -->
                    <div class="hidden sm:flex flex-col items-start min-w-0">
                        <span
                            class="self-stretch justify-start text-secondary text-base font-medium capitalize truncate max-w-[180px]"
                            :title="displayName"
                        >
                            {{ displayName }}
                        </span>

                    </div>

                    <!-- NEW white circular dropdown toggle -->
                    <button
                        ref="userBtn"
                        type="button"
                        class="size-8 p-1 bg-white rounded-full inline-flex justify-center items-center cursor-pointer"
                        @click="toggleUserMenu"
                    >
                        <svg
                            class="text-secondary transition-transform"
                            :class="{ 'rotate-180': showUserMenu }"
                            xmlns="http://www.w3.org/2000/svg"
                            width="12"
                            height="8"
                            viewBox="0 0 12 8"
                            fill="none"
                        >
                            <path
                                d="M11.1699 1.50004C11.1699 1.50004 7.48751 6.5 6.16992 6.5C4.85226 6.5 1.16992 1.5 1.16992 1.5"
                                stroke="#005766"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>

                    <!-- dropdown -->
                    <div
                        v-show="showUserMenu"
                        ref="userMenu"
                        class="absolute right-0 top-[calc(100%+10px)] min-w-[220px] z-20 rounded-xl bg-gradient-to-b from-[#005766] to-[#3ab57c] backdrop-blur shadow-lg overflow-hidden"
                    >
                        <ul>
                            <li>
                                <router-link
                                    to="/profile"
                                    class="block px-4 py-2 text-sm text-white/90 hover:text-white hover:bg-white/10"
                                    @click="closeUserMenu"
                                >
                                    {{ $t("Profile update") }}
                                </router-link>
                            </li>
                            <li>
                                <router-link
                                    to="/change-password"
                                    class="block px-4 py-2 text-sm text-white/90 hover:text-white hover:bg-white/10"
                                    @click="closeUserMenu"
                                >
                                    {{ $t("Change Password") }}
                                </router-link>
                            </li>
                            <!-- <li
                                class="px-3 border-t border-white flex items-center hover:bg-white/10"
                            > -->
                            <li
                                class="px-4 py-2 flex items-center gap-2 hover:bg-white/10 h-12"
                            >
                                <span
                                    class="text-sm text-white/90"
                                >
                                    {{ $t("Change Language") }}:
                                </span>
                                <LangSelect :sidebarCollapse="false" />
                            </li>
                            <li
                                class="border-t border-white px-3 flex items-center hover:bg-white/10 h-12 cursor-pointer"
                                @click="handleLogout"
                            >
                                <span
                                    class="text-sm text-white/90 hover:text-white"
                                >
                                    {{ $t("Logout") }} 
                                    <!-- <LogoutIcon class="inline ml-1 w-4 h-4" /> -->
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, shallowRef, onMounted, onBeforeUnmount, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import TogglerIcon from "@/components/icons/TogglerIcon.vue";
import LangSelect from "@/layouts/includes/sidebar/dropdowns/LangSelect.vue";
import { useUserStore } from "@/stores/useUserStore";
import AvatarIcon from "@/components/icons/AvatarIcon.vue";
import LogoutIcon from "@/components/icons/LogoutIcon.vue";

const router = useRouter();
const store = useUserStore();

const props = defineProps({
    sidebarCollapse: { type: Boolean, required: true },
});
const emit = defineEmits(["toggle-sidebar"]);

const sidebarCollapse = shallowRef(props.sidebarCollapse);

const handleCollapse = () => {
    localStorage.setItem("sidebarCollapse", !sidebarCollapse.value);
    sidebarCollapse.value = !sidebarCollapse.value;
    emit("toggle-sidebar");
};

/* ---------- User data ---------- */
const userPhoto = computed(() =>
    store.user?.avatar_url ||
    store.user?.avatar ||
    localStorage.getItem("avatar") ||
    "https://placehold.co/36x36"
);
const displayName = computed(() => {
    const name = store.user?.full_name || "No Name";
    return name.length > 30 ? `${name.slice(0, 30)}…` : name;
});
const displayRole = computed(() => store.user?.role || "No role");

/* ---------- Dropdown state ---------- */
const showUserMenu = ref(false);
const userBtn = ref(null);
const userMenu = ref(null);

const toggleUserMenu = () => (showUserMenu.value = !showUserMenu.value);
const closeUserMenu = () => (showUserMenu.value = false);

/* Close on outside click / ESC */
const onDocClick = (e) => {
    const targets = [userBtn.value, userMenu.value];
    if (!targets.some((el) => el && el.contains(e.target))) closeUserMenu();
};
const onKey = (e) => {
    if (e.key === "Escape") closeUserMenu();
};

onMounted(() => {
    document.addEventListener("click", onDocClick);
    document.addEventListener("keydown", onKey);
});
onBeforeUnmount(() => {
    document.removeEventListener("click", onDocClick);
    document.removeEventListener("keydown", onKey);
});

/* ---------- Logout ---------- */
const handleLogout = async () => {
    try {
        await store.logout?.();
    } catch (e) {
        // optional: toast error
    } finally {
        router.push("/login");
    }
};
</script>
