<template>
    <div class="relative" ref="toggleRef">
        <div
            class="uppercase h-[42px] px-4 py-3 rounded-lg flex justify-center items-center cursor-pointer text-white hover:text-primary-hover transition-colors duration-200"
            :class="sidebarCollapse ? 'w-[41px]' : 'w-[51px]'"
            @click="showLangOption = !showLangOption"
        >
            <span class="text-sm font-semibold">
                {{ selectedLang }}
            </span>
        </div>

        <div v-if="showLangOption">
            <div
                v-for="(lang, index) in langArr"
                :key="index"
                :class="
                    sidebarCollapse
                        ? 'absolute shadow-lg top-0 left-[48px] z-30'
                        : 'absolute shadow-lg -bottom-[30px]'
                "
            >
                <div
                    v-if="index != selectedLang"
                    class="px-4 py-2 flex justify-center items-center cursor-pointer w-full text-white hover:text-primary-hover bg-[#197F6F] rounded-[10px]"
                    @click.stop="setLang(lang, index)"
                >
                    <span class="text-sm font-semibold">{{ lang }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, inject } from "vue";
import { useUserStore } from "@/stores/useUserStore";

const props = defineProps({
    sidebarCollapse: {
        type: Boolean,
        default: false,
    },
});
const emitter = inject("emitter");

const langArr = ref({
    bn: "BN",
    en: "EN",
});

const store = useUserStore();

const showLangOption = ref(false);
const selectedLang = ref(localStorage.getItem("language") || "en");

const toggleRef = ref(null);

const setLang = async (lang, index) => {
    showLangOption.value = false;
    selectedLang.value = index;

    const res = await axios.put(route("update-profile"), {
        full_name: store.user.full_name,
        language: index,
    });
    store.setUser(res.data.data);
    emitter.emit("languageChanged", index);
};

// 📌 Click outside logic
const handleClickOutside = (event) => {
    if (toggleRef.value && !toggleRef.value.contains(event.target)) {
        showLangOption.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
