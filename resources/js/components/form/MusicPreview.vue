<template>
    <div>
        <div v-if="label" class="mb-3 size-lf-stretch justify-start text-[#585b5a] text-sm font-normal leading-[18.20px]">
            {{ label }}
        </div>
        <div
            class="self-stretch px-4 py-5 bg-[#f3f6fa] rounded-2xl inline-flex flex-col gap-3 h-auto w-[250px]"
        >
            <!-- Top Section: Thumbnail + Info + Play/Pause -->
            <div class="flex justify-between items-center w-full">
                <div class="flex items-center gap-3">
                    <MusicUploadIcon class="w-8 h-8" />
                    <div class="flex flex-col">
                        <span
                            class="text-[#383a39] text-sm font-medium leading-[18.20px]"
                        >
                            {{ audioName.length > 20 ? audioName.slice(0, 17) + '...' : audioName }}
                        </span>
                        <span class="text-xs text-[#7b7e7d]">
                            {{ voiceGender }}
                        </span>
                    </div>
                </div>

                <!-- Play / Pause Button -->
                <button
                    @click="togglePlay"
                    class="relative w-8 h-8 flex items-center justify-center rounded-full bg-[#7b7e7d] text-white cursor-pointer"
                >
                    <svg
                        v-if="!isPlaying"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                        class="w-5 h-5"
                    >
                        <path d="M8 5v14l11-7z" />
                    </svg>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                        class="w-5 h-5"
                    >
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                    </svg>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="flex items-center gap-2 w-full" v-if="showPlayer">
                <!-- Seek Slider -->
                <input
                    type="range"
                    min="0"
                    :max="duration"
                    step="0.1"
                    v-model="currentTime"
                    @input="seekAudio"
                    class="flex-1 accent-[#3AB57C] cursor-pointer"
                />

                <!-- Remaining Time -->
                <span class="text-xs text-[#7b7e7d] w-[50px] text-right">
                    {{ formatTime(remainingTime) }}
                </span>
            </div>

            <!-- Volume Control -->
            <div class="flex items-center gap-2 w-full" v-if="showPlayer">
                <!-- Mute / Unmute Icon on LEFT -->
                <span
                    class="text-xs text-[#7b7e7d] cursor-pointer select-none"
                    @click="toggleMute"
                >
                    <SpeakerIcon :isMuted="isMuted" />
                </span>

                <!-- Volume Slider (1/3 width of progress bar) -->
                <input
                    type="range"
                    min="0"
                    max="1"
                    step="0.01"
                    v-model="volume"
                    class="w-1/3 accent-[#3AB57C] cursor-pointer"
                />
            </div>

            <!-- Hidden audio element -->
            <audio ref="audioEl" :src="src"></audio>
        </div>
    </div>
</template>

<script setup>
import {
    defineProps,
    ref,
    onMounted,
    onBeforeUnmount,
    watch,
    computed,
} from "vue";
import MusicUploadIcon from "@/components/icons/MusicUploadIcon.vue";
import SpeakerIcon from "@/components/icons/SpeakerIcon.vue";

const props = defineProps({
    src: { type: String, required: true },
    audioName: { type: String, default: "User Voice" },
    voiceGender: { type: String, default: "" },
    label:{type: String, default: ""}
});

const audioEl = ref(null);
const isPlaying = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const volume = ref(0.1); // start at 10%
const isMuted = ref(false);
let lastVolume = 0.1;

let interval = null;
const showPlayer = ref(false);

// computed remaining time
const remainingTime = computed(() => {
    return Math.max(duration.value - currentTime.value, 0);
});

const togglePlay = () => {
    if (!audioEl.value) return;

    if (!showPlayer.value) {
        showPlayer.value = true;
    }

    if (isPlaying.value) {
        audioEl.value.pause();
        isPlaying.value = false;
    } else {
        audioEl.value.play();
        isPlaying.value = true;

        audioEl.value.onended = () => {
            isPlaying.value = false;
            // currentTime.value = 0;
        };
    }
};

// Update progress
const updateProgress = () => {
    if (audioEl.value) {
        currentTime.value = audioEl.value.currentTime;
        duration.value = audioEl.value.duration || 0;
    }
};

// Seek
const seekAudio = () => {
    if (audioEl.value) {
        audioEl.value.currentTime = currentTime.value;
    }
};

// Toggle mute/unmute
const toggleMute = () => {
    if (!audioEl.value) return;

    if (isMuted.value) {
        volume.value = lastVolume || 0.1;
        audioEl.value.volume = volume.value;
        isMuted.value = false;
    } else {
        lastVolume = volume.value;
        volume.value = 0;
        audioEl.value.volume = 0;
        isMuted.value = true;
    }
};

// Watch volume changes from slider
watch(volume, (newVal) => {
    if (!audioEl.value) return;

    audioEl.value.volume = newVal;

    if (newVal === 0) {
        isMuted.value = true;
    } else {
        isMuted.value = false;
        lastVolume = newVal;
    }
});

// Format mm:ss
const formatTime = (time) => {
    if (!time || isNaN(time)) return "0:00";
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60)
        .toString()
        .padStart(2, "0");
    return `${minutes}:${seconds}`;
};

onMounted(() => {
    if (audioEl.value) {
        audioEl.value.volume = volume.value;
    }
    interval = setInterval(updateProgress, 500);
});

onBeforeUnmount(() => {
    clearInterval(interval);
});
</script>
