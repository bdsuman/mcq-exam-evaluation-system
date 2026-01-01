<template>
  <transition name="ap-fade">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-[1000] grid place-items-center bg-black/60 p-4"
    >
      <div class="overflow-hidden bg-white shadow-2xl relative rounded-[20px] w-[500px] max-w-[92vw] h-[548.25px] max-h-[92vh]">
        <!-- Close -->
        <button
          class="absolute right-2 top-2 grid h-8 w-8 place-items-center rounded-full bg-white/90 text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 cursor-pointer"
          aria-label="Close"
          @click="close"
        >
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.2214 4.58972C12.0964 4.46474 11.9269 4.39453 11.7501 4.39453C11.5733 4.39453 11.4038 4.46474 11.2788 4.58972L8.22144 7.64706L5.1641 4.58972C5.03908 4.46474 4.86955 4.39453 4.69277 4.39453C4.51599 4.39453 4.34645 4.46474 4.22144 4.58972C4.09646 4.71474 4.02625 4.88428 4.02625 5.06106C4.02625 5.23783 4.09646 5.40737 4.22144 5.53239L7.27877 8.58972L4.22144 11.6471C4.09646 11.7721 4.02625 11.9416 4.02625 12.1184C4.02625 12.2952 4.09646 12.4647 4.22144 12.5897C4.34645 12.7147 4.51599 12.7849 4.69277 12.7849C4.86955 12.7849 5.03908 12.7147 5.1641 12.5897L8.22144 9.53239L11.2788 12.5897C11.4038 12.7147 11.5733 12.7849 11.7501 12.7849C11.9269 12.7849 12.0964 12.7147 12.2214 12.5897C12.3464 12.4647 12.4166 12.2952 12.4166 12.1184C12.4166 11.9416 12.3464 11.7721 12.2214 11.6471L9.1641 8.58972L12.2214 5.53239C12.3464 5.40737 12.4166 5.23783 12.4166 5.06106C12.4166 4.88428 12.3464 4.71474 12.2214 4.58972Z" fill="#005766"/>
            </svg>
        </button>

        <!-- Cover -->
        <img :src="coverSrc" alt="cover" class="w-full h-[306.916px] object-cover rounded-t-[20px]" />

        <!-- Body -->
        <div class="p-4 overflow-y-auto" style="max-height: calc(548.25px - 306.916px);">
            <h3 class="mb-3 text-center text-lg font-semibold text-emerald-950">
                {{ title }}
            </h3>

          <!-- Seek -->
            <input
                class="ap-seek"
                type="range"
                min="0"
                :max="duration"
                :value="displayCurrent"
                step="0.01"
                @input="onSeekInput($event.target.value)"
                @mousedown="seeking = true" @touchstart.passive="seeking = true"
                @mouseup="onSeekCommit" @touchend.passive="onSeekCommit"
                :style="{ '--fill': (duration ? (displayCurrent / duration) * 100 : 0) + '%' }"
            />

            <div class="mt-1 flex justify-between text-xs text-slate-500">
                <span>{{ fmt(current) }}</span>
                <span>{{ fmt(duration) }}</span>
            </div>

            <!-- Sound control -->
            <div class="mt-3 flex items-center gap-3 w-50" v-if="showSound">
                <button
                    class="grid h-8 w-8 place-items-center rounded-full bg-white/90 text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 cursor-pointer"
                    @click="toggleMute"
                    :aria-label="muted ? 'Unmute' : 'Mute'"
                    title="Mute / Unmute"
                >
                    <svg v-if="muted || volume === 0" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M3 10v4h4l5 4v-16l-5 4H3z" fill="#7B7E7D"/>
                        <path d="M16 8l5 8M21 8l-5 8" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <svg v-else-if="volume < 0.5" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M3 10v4h4l5 4v-16l-5 4H3z" fill="#7B7E7D"/>
                        <path d="M17 12a3 3 0 0 0-3-3" stroke="#7B7E7D" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M3 10v4h4l5 4v-16l-5 4H3z" fill="#7B7E7D"/>
                        <path d="M16 7a5 5 0 0 1 0 10M19 4a8 8 0 0 1 0 16" stroke="#7B7E7D" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>

                <input
                    class="ap-seek flex-1"
                    type="range"
                    min="0"
                    max="1"
                    step="0.01"
                    :value="muted ? 0 : volume"
                    @input="onVolumeInput($event.target.value)"
                    :style="{ '--fill': ((muted ? 0 : volume) * 100) + '%' }"
                    aria-label="Volume"
                />

                <span class="text-right text-xs text-slate-500 tabular-nums">
                    {{ Math.round((muted ? 0 : volume) * 100) }}%
                </span>
            </div>

            <!-- Audio control -->
            <div class="mt-4 flex justify-center gap-4">
                <button
                    class="grid h-14 w-20 place-items-center rounded-full cursor-pointer"
                    @click="back10"
                    aria-label="Back 10 seconds"
                >
                <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16 0.126953C7.16344 0.126953 0 7.29039 0 16.127C0 24.9635 7.16344 32.127 16 32.127C24.8366 32.127 32 24.9635 32 16.127C32 7.29039 24.8366 0.126953 16 0.126953ZM15.6067 16.6475C15.4431 16.5167 15.3565 16.3364 15.3565 16.127C15.3565 15.9175 15.4431 15.7372 15.6067 15.6064L22.5737 10.0327C22.7787 9.8687 23.0425 9.8387 23.279 9.95239C23.5156 10.0661 23.6569 10.2908 23.6569 10.5532V21.7006C23.6569 21.963 23.5156 22.1877 23.279 22.3014C23.0424 22.4151 22.7787 22.3851 22.5737 22.2211L15.6067 16.6475ZM6.90581 16.6475C6.74225 16.5167 6.65562 16.3364 6.65562 16.127C6.65562 15.9175 6.74225 15.7372 6.90581 15.6064L13.8729 10.0327C14.0778 9.8687 14.3416 9.8387 14.5781 9.95239C14.8146 10.0661 14.956 10.2908 14.956 10.5532V21.7006C14.956 21.963 14.8147 22.1877 14.5781 22.3014C14.3416 22.4151 14.0778 22.3851 13.8729 22.2211L6.90581 16.6475Z" fill="#7B7E7D"/>
                    </svg>
                </button>

                <button
                class="grid h-14 w-14 place-items-center rounded-full bg-[#005766] text-white hover:brightness-105 cursor-pointer"
                @click="toggle"
                aria-label="Play/Pause"
                >
                    <svg v-if="!isPlaying" width="28" height="28" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>
                    <svg v-else width="28" height="28" viewBox="0 0 24 24"><path d="M6 5h4v14H6zM14 5h4v14h-4z" fill="currentColor"/></svg>
                </button>

                <button
                class="grid h-14 w-20 place-items-center rounded-full cursor-pointer"
                @click="fwd10"
                aria-label="Forward 10 seconds"
                >
                <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 0.126953C24.8366 0.126953 32 7.29039 32 16.127C32 24.9635 24.8366 32.127 16 32.127C7.16344 32.127 0 24.9635 0 16.127C0 7.29039 7.16344 0.126953 16 0.126953ZM16.3933 16.6475C16.5569 16.5167 16.6435 16.3364 16.6435 16.127C16.6435 15.9175 16.5569 15.7372 16.3933 15.6064L9.42625 10.0327C9.22131 9.8687 8.9575 9.8387 8.721 9.95239C8.48444 10.0661 8.34313 10.2908 8.34313 10.5532V21.7006C8.34313 21.963 8.48444 22.1877 8.721 22.3014C8.95756 22.4151 9.22131 22.3851 9.42625 22.2211L16.3933 16.6475ZM25.0942 16.6475C25.2577 16.5167 25.3444 16.3364 25.3444 16.127C25.3444 15.9175 25.2577 15.7372 25.0942 15.6064L18.1271 10.0327C17.9222 9.8687 17.6584 9.8387 17.4219 9.95239C17.1854 10.0661 17.044 10.2908 17.044 10.5532V21.7006C17.044 21.963 17.1853 22.1877 17.4219 22.3014C17.6584 22.4151 17.9222 22.3851 18.1271 22.2211L25.0942 16.6475Z" fill="#7B7E7D"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Hidden audio -->
        <audio
            ref="audioEl"
            :src="audioUrl"
            preload="auto"
            @loadedmetadata="onLoadedMetadata"
            @durationchange="onLoadedMetadata"
            @canplay="onCanPlay"
            @ended="onEnded"
        />
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, onUnmounted, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    audioUrl:   {
        type: String,
        default: ''
    },
    title:      {
        type: String,
        default: 'Fountain Rhythm'
    },
    coverUrl:   {
        type: String,
        default: ''
    },
    autoplay:   {
        type: Boolean,
        default: true
    },
    showSound:{
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'play', 'pause', 'ended']);

const audioEl = ref(null);
const isPlaying = ref(false);
const duration  = ref(0);
const current   = ref(0);
const displayCurrent = ref(0);
const volume = ref(1);
const muted  = ref(false);
const seeking = ref(false);
const EPS = 0.15;
const FALLBACK =
'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0nNjAwJyBoZWlnaHQ9JzMwMCcgdmlld0JveD0nMCAwIDYwMCAzMDAnIHhtbG5zPSdodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2Zyc+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSdnJyB4MT0nMCUnIHkxPScwJScgeDI9JzAlJyB5Mj0nMTAwJSc+PHN0b3Agb2Zmc2V0PScwJScgc3RvcC1jb2xvcj0nI2Q5ZTVkMicvPjxzdG9wIG9mZnNldD0nMTAwJScgc3RvcC1jb2xvcj0nI2UyZmFmNycvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHdpZHRoPSc2MDAnIGhlaWdodD0nMzAwJyBmaWxsPSd1cmwoI2cpJy8+PC9zdmc+';

let rafId = null;

const coverSrc = computed(() => props.coverUrl || FALLBACK);

const startRaf = () => {
  stopRaf();
  const loop = () => {
    if (!seeking.value && audioEl.value) {
      current.value = audioEl.value.currentTime || 0;
      if (duration.value && (duration.value - current.value) <= EPS) {
        current.value = duration.value;
      }
      displayCurrent.value = current.value;
    }
    rafId = requestAnimationFrame(loop);
  };
  rafId = requestAnimationFrame(loop);
};

const stopRaf = () => {
  if (rafId) cancelAnimationFrame(rafId);
  rafId = null;
};

const onLoadedMetadata = () => {
  if (audioEl.value) {
    const d = Number(audioEl.value.duration);
    duration.value = Number.isFinite(d) ? d : 0;
  }
};

const onCanPlay = () => {
  onLoadedMetadata();
  if (props.autoplay) play();
};

const play  = async () => {
  try {
    await audioEl.value?.play();
    isPlaying.value = true;
    startRaf();
    emit('play');
  } catch {}
};

const pause = () => {
  audioEl.value?.pause();
  isPlaying.value = false;
  stopRaf();
  emit('pause');
};

const onEnded = () => {
  isPlaying.value = false;
  stopRaf();
  if (audioEl.value) {
    current.value = audioEl.value.duration || current.value;
    displayCurrent.value = current.value;
  }
};

const onSeekInput = (val) => {
  const t = Math.max(0, Math.min(duration.value || 0, Number(val)));
  seeking.value = true;
  displayCurrent.value = Number.isFinite(t) ? t : 0;
};
const onSeekCommit = () => {
  if (!audioEl.value) return;
  const t = Math.max(0, Math.min(duration.value || 0, displayCurrent.value));
  audioEl.value.currentTime = t;
  current.value = t;
  seeking.value = false;
};

const close = () => emit('update:modelValue', false);
const toggle = () => (isPlaying.value ? pause() : play());
const lock = () => document.documentElement.style.overflow = 'hidden';
const unlock = () => document.documentElement.style.overflow = '';
const back10 = () => onSeekCommit(displayCurrent.value = Math.max(0, displayCurrent.value - 10));
const fwd10  = () => onSeekCommit(displayCurrent.value = Math.min(duration.value, displayCurrent.value + 10));
const fmt = (n) => {
  if (!Number.isFinite(n)) return '0:00';
  const m = Math.floor(n / 60);
  const s = Math.floor(n % 60).toString().padStart(2,'0');
  return `${m}:${s}`;
};

const applyVolume = () => {
  if (!audioEl.value) return;
  audioEl.value.volume = muted.value ? 0 : volume.value;
  audioEl.value.muted  = muted.value;
};

const onVolumeInput = (val) => {
  const v = Math.min(1, Math.max(0, Number(val)));
  volume.value = Number.isFinite(v) ? v : 0;
  if (v > 0 && muted.value) muted.value = false;
  applyVolume();
};

const toggleMute = () => {
  muted.value = !muted.value || volume.value === 0;
  applyVolume();
};

watch(() => props.modelValue, async (open) => {
  if (open) {
    lock();
    isPlaying.value = false;
    current.value = 0;
    duration.value = 0;
    requestAnimationFrame(() => { if (props.autoplay) play(); });
  } else {
    unlock();
    pause();
  }
});

watch(() => props.audioUrl, () => {
  if (props.modelValue) {
    isPlaying.value = false;
    current.value = 0;
    duration.value = 0;
    requestAnimationFrame(() => { if (props.autoplay) play(); });
  }
});

onUnmounted(() => {
  stopRaf();
  unlock();
});

onUnmounted(unlock);
</script>

<style scoped>
.ap-fade-enter-active, .ap-fade-leave-active {
    transition: opacity .2s ease;
}

.ap-fade-enter-from, .ap-fade-leave-to {
    opacity: 0;
}

.ap-seek {
  -webkit-appearance: none;
  appearance: none;
  width: 100%;
  height: 10px;
  border-radius: 999px;
  outline: none;
  background: linear-gradient(to right,#27c498 0 var(--fill), #e5e7eb var(--fill) 100%);
}

.ap-seek::-webkit-slider-runnable-track {
  height: 10px;
  border-radius: 999px;
  background: linear-gradient(to right,#27c498 0 var(--fill), #e5e7eb var(--fill) 100%);
}

.ap-seek::-moz-range-track {
    height: 10px;
    border-radius: 999px;
    background: #e5e7eb;
}

.ap-seek::-moz-range-progress {
    height: 10px;
    border-radius: 999px;
    background: #27c498;
}

.ap-seek::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #3F8B77;
  margin-top: -3px;
  cursor: pointer;
}

.ap-seek::-moz-range-thumb {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #27c498;
    border: none;
}
</style>
