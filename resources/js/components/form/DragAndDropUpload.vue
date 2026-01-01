<template>
    <div class="input-element">
        <!-- Label -->
        <div
            class="self-stretch justify-start text-[#002d45] text-sm font-medium leading-[18.20px] pb-2"
            v-if="label"
        >
            {{ $t(label) }}<span v-if="required">*</span>
        </div>

        <!-- Drop Area and File Input -->
        <div class="attachments w-full">
            <label
                class="file-input flex flex-col z-10 cursor-pointer border border-dotted border-[#C5CBC9] rounded-xl bg-[#F3F6FA]"
                :class="{
                    'border-green-500': focused,
                    '!border-red-500': localError,
                    '!cursor-not-allowed': isAtMax,
                    [extraClass]: extraClass,
                }"
                @dragover.prevent="focused = true"
                @dragleave="focused = false"
                @drop="handleDrop"
                @click.stop
            >
                <input
                    :key="inputKey"
                    ref="fileInput"
                    type="file"
                    :multiple="!singleUpload"
                    :accept="allowedExtensions.join()"
                    class="hidden"
                    @change="handleFileChange"
                    :disabled="isAtMax"
                />
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
                        :color="isAtMax ? '#D82C20' : '#3AB57C'"
                    />
                    <p
                        class="self-stretch text-center justify-center text-[#585b5a] text-sm font-medium leading-[18.20px]"
                    >
                        {{ placeholder }}
                    </p>
                    <div
                        class="text-center justify-center text-[#7b7e7d] text-xs font-normal leading-none flex gap-1"
                    >
                        <div>
                            ({{ extensionLabel }})<span
                                v-if="otherValidationTexts"
                                >&nbsp;{{ otherValidationTexts }}</span
                            >
                        </div>
                    </div>
                </div>
            </label>

            <!-- Error messages -->
            <div class="text-gray-600 text-sm mt-2">
                <p v-if="localError" class="text-red-600 text-xs mt-1">
                    {{ $t(localError) }}
                </p>
            </div>

            <!-- Display selected files -->
            <div class="flex flex-wrap gap-2 mt-4">
                <div
                    v-for="(file, index) in modelValue"
                    :key="index"
                    class="relative"
                >
                    <div v-if="getKind(file) === 'image'" class="h-[64px]">
                        <img
                            :src="generateUrl(file)"
                            class="h-[64px] max-h-[64px] rounded-[10px]"
                            @click="showImagePreview(index, file)"
                        />
                        <ImagePreviewModal
                            :show="showImagePreviewModal"
                            :image="imagePreviewModal"
                            @remove="closeImageModal"
                        />
                    </div>

                    <video
                        v-else-if="getKind(file) === 'video'"
                        :src="generateUrl(file)"
                        controls
                        class="h-20 max-h-20"
                    />

                    <MusicPreview
                        v-else-if="getKind(file) === 'audio'"
                        :src="generateUrl(file)"
                        :audioName="shortFileName(file)"
                    />
                    <!-- XLSX thumb -->
                    <div
                        v-else-if="getKind(file) === 'sheet'"
                        class="w-24 h-24 bg-gray-200 rounded-[10px] grid place-items-center"
                    >
                        <div
                            class="flex flex-col items-center gap-2"
                            @click="showXlsxPreview = true"
                        >
                            <ExcelIcon class="w-24 h-24 bg-gray-200" />
                            <span class="text-[10px] text-center truncate px-1">
                                {{ shortFileName(file) }}
                            </span>
                        </div>
                        <XlsxPreviewModal
                            :show="showXlsxPreview"
                            :src="generateUrl(file)"
                            :name="file?.name"
                            :row-limit="200"
                            :col-limit="25"
                            @close="showXlsxPreview = false"
                        />
                    </div>
                    <FilesIcon v-else class="w-24 h-24 bg-gray-200" />

                    <!-- Remove file button -->
                    <button
                        @click="removeFile(index)"
                        class="absolute top-[10%] right-1 inline-flex items-center justify-center cursor-pointer"
                    >
                        <CrossCircleIcon class="h-4 w-4" />
                    </button>

                    <button
                        v-if="
                            (file &&
                                file.type &&
                                file.type.startsWith('image/')) ||
                            (file.file_type &&
                                file.file_type.startsWith('image'))
                        "
                        @click="showImagePreview(index, file)"
                        class="absolute bottom-[10%] right-1 inline-flex items-center justify-center cursor-pointer"
                    >
                        <FileExpandIcon class="h-4 w-4" />
                    </button>
                    <!-- XLSX expand -->
                    <button
                        v-if="getKind(file) === 'sheet'"
                        @click="showXlsxPreview = true"
                        class="absolute bottom-[10%] right-1 inline-flex items-center justify-center cursor-pointer"
                    >
                        <FileExpandIcon class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onUnmounted, watch } from "vue";
import UploadIcon from "@/components/icons/UploadIcon.vue";
import MusicUploadIcon from "@/components/icons/MusicUploadIcon.vue";
import CrossCircleIcon from "@/components/icons/CrossCircleIcon.vue";
import FileExpandIcon from "@/components/icons/FileExpandIcon.vue";
import FilesIcon from "@/components/icons/FilesIcon.vue";
import ExcelIcon from "@/components/icons/ExcelIcon.vue";
import { trans } from "laravel-vue-i18n";
import ImagePreviewModal from "@/components/modals/ImagePreviewModal.vue";
import MusicPreview from "@/components/form/MusicPreview.vue";
import XlsxPreviewModal from "@/components/modals/XlsxPreviewModal.vue";
/**
 * Props
 */
const props = defineProps({
    placeholder: { type: String, default: "Attach Receipts" },
    label: { type: String, default: "Attachments" },
    required: { type: Boolean, default: false },
    singleUpload: { type: Boolean, default: false },
    minFiles: { type: Number, default: 0 },
    /** If 0 => unlimited */
    maxFiles: { type: Number, default: 0 },
    allowedExtensions: {
        type: Array,
        default: () => [".jpg", ".png", ".pdf", ".mp4"],
    },
    /** If 0 => unlimited (MB) */
    maxFileSize: { type: Number, default: 0 }, // in MB
    /** If 0 => unlimited (MB) */
    maxVideoSize: { type: Number, default: 0 },
    modelValue: { default: () => [] },
    extraClass: { type: String, default: "" },
});

const emit = defineEmits(["update:modelValue", "removedFileIds"]);

watch(
    () => props.modelValue,
    (val) => {
        if (!Array.isArray(val)) emit("update:modelValue", []);
    },
    { immediate: true }
);

/**
 * State
 */
const fileInput = ref(null);
const inputKey = ref(0); // used to force-refresh the <input type="file">
const focused = ref(false);
const localError = ref(null);

const showImagePreviewModal = ref(false);
const imagePreviewModal = ref(null);

// XLSX preview state (only for .xlsx)
const showXlsxPreview = ref(false);

/**
 * Computeds
 */
const isAtMax = computed(
    () =>
        props.maxFiles > 0 && (props.modelValue?.length || 0) >= props.maxFiles
);

const allowedExtensionsUppercase = computed(() =>
    props.allowedExtensions.map((ext) => ext.toUpperCase()).join(", ")
);

const otherValidationTexts = computed(() => {
    const items = [];
    if (props.minFiles)
        items.push(trans("Min files", { value: props.minFiles }));
    if (props.maxFiles)
        items.push(trans("Max file", { value: props.maxFiles }));
    if (props.maxFileSize)
        items.push(trans("MB Max", { value: props.maxFileSize }));
    if (props.maxVideoSize)
        items.push(trans("Max video size MB", { value: props.maxVideoSize }));
    return items.join(", ");
});

const extensionLabel = computed(() =>
    props.allowedExtensions
        .map((ext) => ext.replace(".", "").toUpperCase())
        .join(", ")
);

/**
 * Helpers
 */
const resetPicker = () => {
    // Ensure selecting the *same* file triggers change again
    if (fileInput.value) fileInput.value.value = null;
    inputKey.value++; // recreate the input element
};

const shortFileName = (file) => {
    const name = file.name || "Unknown";
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
    if (/\.(xlsx)$/.test(src)) return "sheet";
    if (/\.(png|jpe?g|gif|webp|svg)$/.test(src)) return "image";
    if (/\.(mp4|webm|ogg)$/.test(src)) return "video";
    if (/\.(mp3|wav|m4a|ogg)$/.test(src)) return "audio";
    if (/^https?:\/\//.test(src)) return "image"; // best-effort default
    return "other";
};

/**
 * Events
 */
const handleDrop = (e) => {
    e.preventDefault();
    focused.value = false;
    const droppedFiles = Array.from(e.dataTransfer.files || []);
    handleFiles(droppedFiles);
};

const handleFileChange = (e) => {
    const selectedFiles = Array.from(e.target.files || []);
    handleFiles(selectedFiles);
    resetPicker(); // allow reselecting the same file immediately
};

/**
 * Core logic
 */
const handleFiles = (incomingFiles) => {
    localError.value = null;

    // Filter and validate
    const validFiles = validateFiles(incomingFiles);
    if (!validFiles.length) return;

    // De-dup against current modelValue
    const newFiles = removeDuplicates(validFiles);
    if (!newFiles.length) {
        localError.value = trans("Duplicate files cannot be added.");
        return;
    }

    // Enforce maxFiles only if >0
    if (
        props.maxFiles > 0 &&
        props.modelValue.length + newFiles.length > props.maxFiles
    ) {
        localError.value = trans("You cannot attach more than files.", {
            value: props.maxFiles,
        });
        return;
    }

    const updatedFiles = [...(props.modelValue || []), ...newFiles];
    emit("update:modelValue", updatedFiles);
};

const validateFiles = (files) => {
    return files.filter((file) => {
        // Extension check
        const extension = `.${
            (file.name || "").split(".").pop()?.toLowerCase() || ""
        }`;
        const isValidExtension = props.allowedExtensions.includes(extension);

        if (!isValidExtension) {
            localError.value =
                trans("Selected files do not match allowed formats:") +
                allowedExtensionsUppercase.value;
            return false;
        }

        // Size checks (only if limits are set)
        const fileType = file.type || "";
        if (fileType.startsWith("video/")) {
            if (props.maxVideoSize > 0) {
                const videoSizeInMB = Math.ceil(file.size / 1024 / 1024);
                if (videoSizeInMB > props.maxVideoSize) {
                    localError.value = trans(
                        "Video size exceeds the limit of MB.",
                        { value: props.maxVideoSize }
                    );
                    return false;
                }
            }
        } else {
            if (props.maxFileSize > 0) {
                const fileSizeInMB = Math.ceil(file.size / 1024 / 1024);
                if (fileSizeInMB > props.maxFileSize) {
                    localError.value = trans(
                        "Image size exceeds the limit of :value MB.",
                        { value: props.maxFileSize }
                    );
                    return false;
                }
            }
        }

        return true;
    });
};

const removeDuplicates = (files) => {
    return files.filter(
        (incomingFile) =>
            !props.modelValue?.some(
                (existingFile) =>
                    existingFile.name === incomingFile.name &&
                    existingFile.size === incomingFile.size
            )
    );
};

const removeFile = (index) => {
    const fileToRemove = props.modelValue[index];

    // If we created an object URL for this file, revoke it
    if (fileToRemove && fileToRemove.__objectUrl) {
        URL.revokeObjectURL(fileToRemove.__objectUrl);
    }

    if (fileToRemove && fileToRemove.id) {
        emit("removedFileIds", { id: fileToRemove.id });
    }

    const updatedFiles = [...props.modelValue];
    updatedFiles.splice(index, 1);
    emit("update:modelValue", updatedFiles);

    resetPicker(); // enable picking the same file again
};

const generateUrl = (file) => {
    if (!file) return "";
    if (typeof file === "string") return file; // direct URL
    if (file.url) return file.url; // normalized object

    // Cache object URL on the file to revoke later
    if (!file.__objectUrl) {
        file.__objectUrl = URL.createObjectURL(file);
    }
    return file.__objectUrl;
};

/**
 * Image preview
 */
const showImagePreview = (index, file) => {
    const previewUrl = file.url ? file.url : generateUrl(file);
    imagePreviewModal.value = previewUrl;
    showImagePreviewModal.value = true;
};

const closeImageModal = () => {
    showImagePreviewModal.value = false;
    imagePreviewModal.value = null;
};

/**
 * Cleanup
 */
onUnmounted(() => {
    const files = Array.isArray(props?.modelValue)
        ? props.modelValue
        : props?.modelValue
        ? [props.modelValue] // wrap single object in array
        : [];

    files.forEach((f) => {
        if (f && f.__objectUrl) {
            URL.revokeObjectURL(f.__objectUrl);
        }
    });
});
</script>
