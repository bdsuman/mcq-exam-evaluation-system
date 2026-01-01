<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="160"
    height="64"
    viewBox="0 0 320 128"
    role="img"
    aria-label="MCQ Eval"
  >
    <defs>
      <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" :stop-color="primary" />
        <stop offset="50%" :stop-color="secondary" />
        <stop offset="100%" :stop-color="accent" />
      </linearGradient>
      <linearGradient id="ringGradient" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.15" />
        <stop offset="100%" stop-color="#ffffff" stop-opacity="0.5" />
      </linearGradient>
      <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
        <feDropShadow dx="0" dy="6" stdDeviation="8" flood-color="#002d45" flood-opacity="0.18" />
      </filter>
    </defs>

    <g filter="url(#shadow)">
      <rect x="8" y="8" rx="24" ry="24" width="304" height="112" fill="url(#bgGradient)" />
      <rect x="22" y="22" rx="18" ry="18" width="276" height="84" fill="#ffffff0f" />
      <path
        d="M64 64 a40 40 0 1 1 80 0 a40 40 0 1 1 -80 0"
        fill="none"
        stroke="url(#ringGradient)"
        stroke-width="8"
      />
    </g>

    <g fill="#ffffff" font-family="'Segoe UI', 'Inter', sans-serif" font-weight="800" letter-spacing="2">
      <text x="118" y="76" font-size="38">MCQ</text>
      <text x="118" y="102" font-size="14" opacity="0.8">Evaluation</text>
    </g>

    <g transform="translate(40 44)">
      <circle cx="24" cy="24" r="22" fill="#ffffff" fill-opacity="0.16" stroke="#ffffff" stroke-opacity="0.2" stroke-width="2" />
      <path
        d="M16 24l6 6 11-14"
        fill="none"
        stroke="#ffffff"
        stroke-width="5"
        stroke-linecap="round"
        stroke-linejoin="round"
      />
    </g>
  </svg>
</template>

<script setup>
defineProps({
  primary: {
    type: String,
    default: '#2dd4bf',
  },
  secondary: {
    type: String,
    default: '#6366f1',
  },
  accent: {
    type: String,
    default: '#ec4899',
  },
});
</script>