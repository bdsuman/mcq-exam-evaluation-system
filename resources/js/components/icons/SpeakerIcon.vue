<template>
    <svg
        v-if="isMuted"
        xmlns="http://www.w3.org/2000/svg"
        width="17"
        height="17"
        viewBox="0 0 17 17"
        fill="none"
    >
        <!-- Speaker -->
        <path
            d="M9.83301 10.3067V6.5553C9.83301 4.4585 9.83301 3.4101 9.21614 3.14919C8.59928 2.88828 7.87321 3.62961 6.42122 5.11227C5.66927 5.88009 5.24024 6.05012 4.17037 6.05012C3.23472 6.05012 2.7669 6.05012 2.43084 6.2794C1.73324 6.75536 1.83869 7.68566 1.83869 8.43099C1.83869 9.17632 1.73324 10.1066 2.43084 10.5826C2.7669 10.8119 3.23472 10.8119 4.17037 10.8119C5.24024 10.8119 5.66927 10.9819 6.42122 11.7497C7.87321 13.2324 8.59928 13.9737 9.21614 13.7128C9.83301 13.4519 9.83301 12.4035 9.83301 10.3067Z"
            stroke="#005766"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <!-- Cross instead of waves -->
        <path
            d="M12 6L15 10"
            stroke="#005766"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="M15 6L12 10"
            stroke="#005766"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>

    <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        width="17"
        height="17"
        viewBox="0 0 17 17"
        fill="none"
    >
        <path
            d="M9.83301 10.3067V6.5553C9.83301 4.4585 9.83301 3.4101 9.21614 3.14919C8.59928 2.88828 7.87321 3.62961 6.42122 5.11227C5.66927 5.88009 5.24024 6.05012 4.17037 6.05012C3.23472 6.05012 2.7669 6.05012 2.43084 6.2794C1.73324 6.75536 1.83869 7.68566 1.83869 8.43099C1.83869 9.17632 1.73324 10.1066 2.43084 10.5826C2.7669 10.8119 3.23472 10.8119 4.17037 10.8119C5.24024 10.8119 5.66927 10.9819 6.42122 11.7497C7.87321 13.2324 8.59928 13.9737 9.21614 13.7128C9.83301 13.4519 9.83301 12.4035 9.83301 10.3067Z"
            stroke="#005766"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="M11.833 6.43066C12.2499 6.97712 12.4997 7.67293 12.4997 8.43066C12.4997 9.1884 12.2499 9.8842 11.833 10.4307"
            stroke="#005766"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="M13.833 5.09766C14.6669 6.00841 15.1663 7.16812 15.1663 8.43099C15.1663 9.69386 14.6669 10.8536 13.833 11.7643"
            stroke="#005766"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>

<script setup>
defineProps({
    isMuted: {
        type: Boolean,
        default: false,
    },
});
</script>
