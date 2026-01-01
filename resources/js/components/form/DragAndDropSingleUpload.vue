<template>
    <div class="input-element">
        <div
            v-if="label"
            class="self-stretch justify-start text-[#002d45] text-sm font-medium leading-[18.2px] pb-2"
        >
            {{ $t(label) }} <span v-if="required">*</span>
        </div>

        <div class="attachments w-full">
            <label
                class="file-input flex flex-col z-10 cursor-pointer border border-dotted rounded-xl bg-[#F3F6FA]"
                :class="{
                    'border-green-500': focused && !hasFile,
                    '!border-red-500': localError || hasFile,
                    '!cursor-not-allowed': disabled || hasFile,
                    [extraClass]: extraClass,
                }"
                @dragover.prevent="focused = true"
                @dragleave="focused = false"
                @drop="handleDrop"
                @click="handleClick"
            >
                <input
                    :key="inputKey"
                    ref="fileInput"
                    type="file"
                    class="hidden"
                    :accept="allowedExtensions.join(',')"
                    @change="handleFileChange"
                    :disabled="disabled || hasFile"
                />

                <!-- Upload UI -->
                <div
                    class="flex flex-col justify-center items-center gap-2 p-4"
                >
                    <component
                        :is="
                            allowedExtensions.includes('.mp3')
                                ? MusicUploadIcon
                                : UploadIcon
                        "
                        class="w-8 h-8"
                        :color="hasFile ? '#D82C20' : '#3AB57C'"
                    />
                    <p class="text-center text-[#585b5a] text-sm font-medium">
                        {{ placeholder }}
                    </p>
                    <div
                        class="text-center text-[#7b7e7d] text-xs leading-none flex gap-1"
                    >
                        <div>
                            ({{ extensionLabel }})
                            <span v-if="otherValidationTexts"
                                >&nbsp;{{ otherValidationTexts }}</span
                            >
                        </div>
                    </div>
                </div>
            </label>

            <p v-if="localError" class="text-red-600 text-xs mt-1">
                {{ $t(localError) }}
            </p>

            <!-- Preview -->
            <div v-if="modelValue" class="relative inline-block mt-4">
                <div v-if="getKind(modelValue) === 'image'">
                    <ImageWithPreview
                        :src="generateUrl(modelValue)"
                        @close="showImagePreviewModal = false"
                    />
                </div>

                <video
                    v-else-if="getKind(modelValue) === 'video'"
                    :src="generateUrl(modelValue)"
                    controls
                    class="h-24 rounded-lg"
                />

                <MusicPreview
                    v-else-if="getKind(modelValue) === 'audio'"
                    :src="generateUrl(modelValue)"
                    :audioName="shortFileName(modelValue)"
                />

                <template v-else-if="getKind(modelValue) === 'sheet'">
                    <div
                        class="w-24 h-24 bg-gray-200 rounded-lg flex flex-col items-center justify-center cursor-pointer"
                        @click="showXlsxPreview = true"
                    >
                        <ExcelIcon class="mb-1" />
                        <span class="text-xs text-center truncate px-1">{{
                            shortFileName(modelValue)
                        }}</span>
                    </div>
                    <XlsxPreviewModal
                        :show="showXlsxPreview"
                        :src="generateUrl(modelValue)"
                        :name="modelValue?.name"
                        :row-limit="200"
                        :col-limit="25"
                        @close="showXlsxPreview = false"
                    />
                </template>

                <FilesIcon v-else class="w-24 h-24 bg-gray-200 rounded-lg" />
                <!-- Remove -->
                <button
                    @click="removeFile"
                    class="absolute top-1 -right-1 translate-x-1/2 -translate-y-1/2 bg-white border border-gray-200 hover:bg-gray-100 rounded-full shadow p-[3px] cursor-pointer transition"
                >
                    <CrossCircleIcon class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from "vue";
import UploadIcon from "@/components/icons/UploadIcon.vue";
import MusicUploadIcon from "@/components/icons/MusicUploadIcon.vue";
import CrossCircleIcon from "@/components/icons/CrossCircleIcon.vue";
import FilesIcon from "@/components/icons/FilesIcon.vue";
import ExcelIcon from "@/components/icons/ExcelIcon.vue";
import { trans } from "laravel-vue-i18n";
import ImageWithPreview from "@/components/form/ImageWithPreview.vue";

import MusicPreview from "@/components/form/MusicPreview.vue";
import XlsxPreviewModal from "@/components/modals/XlsxPreviewModal.vue";

const props = defineProps({
    placeholder: { type: String, default: "Upload File" },
    label: { type: String, default: "Attachment" },
    required: { type: Boolean, default: false },
    allowedExtensions: {
        type: Array,
        default: () => [".jpg", ".png", ".pdf", ".mp4", ".mp3", ".xlsx"],
    },
    maxFileSize: { type: Number, default: 0 },
    maxVideoSize: { type: Number, default: 0 },
    modelValue: { default: null },
    extraClass: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue", "removedFileIds"]);

const fileInput = ref(null);
const inputKey = ref(0);
const focused = ref(false);
const localError = ref(null);

const showImagePreviewModal = ref(false);
const imagePreviewModal = ref(null);
const showXlsxPreview = ref(false);

/** Robust "has file" check */
const hasFile = computed(() => {
    const v = props.modelValue;
    if (!v) return false;
    if (typeof v === "string") return v.trim().length > 0;
    if (v.url) return true;
    if (v instanceof Blob) return true;
    if (v.name) return true;
    return false;
});

const extensionLabel = computed(() =>
    props.allowedExtensions
        .map((ext) => ext.replace(".", "").toUpperCase())
        .join(", ")
);

const otherValidationTexts = computed(() => {
    const arr = [];
    if (props.maxFileSize)
        arr.push(trans("Max size :value MB", { value: props.maxFileSize }));
    if (props.maxVideoSize)
        arr.push(
            trans("Max video size :value MB", { value: props.maxVideoSize })
        );
    return arr.join(", ");
});

/** Helpers */
const resetFileInput = () => {
    if (fileInput.value) fileInput.value.value = null;
    inputKey.value++; // fully remount input (ensures same file can be picked again)
};

const handleClick = (e) => {
    if (props.disabled || hasFile.value) {
        e.preventDefault();
        return;
    }
    // no prevent — allow native label->input click
};

const shortFileName = (file) => {
    const name = file?.name || "Unknown";
    return name.length > 15 ? name.slice(0, 12) + "..." : name;
};

const getKind = (file) => {
    const mime = (file && (file.type || file.file_type)) || "";
    if (mime.startsWith("image")) return "image";
    if (mime.startsWith("video")) return "video";
    if (mime.startsWith("audio")) return "audio";

    const src = (
        typeof file === "string" ? file : file?.url || file?.name || ""
    )
        .split("?")[0]
        .toLowerCase();
    if (/\.(xlsx|xls)$/.test(src)) return "sheet";
    if (/\.(png|jpe?g|gif|webp|svg)$/.test(src)) return "image";
    if (/\.(mp4|webm|ogg)$/.test(src)) return "video";
    if (/\.(mp3|wav|m4a|ogg)$/.test(src)) return "audio";
    return "other";
};

const generateUrl = (file) => {
    if (!file) return "";
    if (typeof file === "string") return file;
    if (file.url) return file.url;
    if (file instanceof Blob) {
        if (!file.__objectUrl) file.__objectUrl = URL.createObjectURL(file);
        return file.__objectUrl;
    }
    return "";
};

/** Events */
const handleDrop = (e) => {
    e.preventDefault();
    if (hasFile.value) return;
    focused.value = false;
    const droppedFiles = Array.from(e.dataTransfer.files || []);
    handleFiles(droppedFiles);
};

const handleFileChange = (e) => {
    const selectedFiles = Array.from(e.target.files || []);
    handleFiles(selectedFiles);
    resetFileInput(); // allow re-selecting same file
};

const handleFiles = (incomingFiles) => {
    localError.value = null;
    const valid = validateFiles(incomingFiles);
    if (!valid.length) return;
    emit("update:modelValue", valid[0]);
};

/** Validation */
const validateFiles = (files) => {
    return files.filter((file) => {
        const ext = `.${
            (file.name || "").split(".").pop()?.toLowerCase() || ""
        }`;
        if (!props.allowedExtensions.includes(ext)) {
            localError.value =
                trans("File format must be: ") +
                props.allowedExtensions.map((e) => e.toUpperCase()).join(", ");
            return false;
        }
        const type = file.type || "";
        const sizeMB = Math.ceil(file.size / 1024 / 1024);
        if (type.startsWith("video/") && props.maxVideoSize > 0) {
            if (sizeMB > props.maxVideoSize) {
                localError.value = trans("Video exceeds :value MB", {
                    value: props.maxVideoSize,
                });
                return false;
            }
        } else if (props.maxFileSize > 0 && sizeMB > props.maxFileSize) {
            localError.value = trans("File exceeds :value MB", {
                value: props.maxFileSize,
            });
            return false;
        }
        return true;
    });
};

/** Remove */
const removeFile = () => {
    const f = props.modelValue;
    if (f?.__objectUrl) URL.revokeObjectURL(f.__objectUrl);
    if (f?.id) emit("removedFileIds", { id: f.id });
    emit("update:modelValue", null);
    resetFileInput();
};

/** Image preview */
const showImagePreview = (file) => {
    imagePreviewModal.value = generateUrl(file);
    showImagePreviewModal.value = true;
};
const closeImageModal = () => {
    showImagePreviewModal.value = false;
    imagePreviewModal.value = null;
};

/** Keep input fresh when parent clears model from outside */
watch(
    () => props.modelValue,
    () => {
        if (!hasFile.value) resetFileInput();
    }
);

/** Cleanup */
onUnmounted(() => {
    const f = props.modelValue;
    if (f && f.__objectUrl) URL.revokeObjectURL(f.__objectUrl);
});
</script>

<style scoped>
.file-input {
    transition: all 0.2s ease-in-out;
}
.file-input:hover {
    background-color: #edf2f7;
}
</style>
