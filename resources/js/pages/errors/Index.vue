<template>
    <GuestLayout>
        <div
            class="p-10 bg-white rounded-3xl inline-flex flex-col justify-center items-center gap-10 z-50 w-full max-w-xl"
        >
            <div class="self-stretch flex flex-col items-center gap-4">
                <div
                    class="text-center text-[#002d45] text-[32px] font-semibold"
                >
                    {{ pageTitle }}
                </div>
                <div class="text-center text-[#4a5b66] text-base">
                    <span
                        class="px-2 py-1 rounded-full border border-gray-200 text-sm font-mono"
                    >
                        {{ displayCode }}
                    </span>
                </div>
            </div>

            <div class="w-full grid grid-cols-1 gap-4 text-center">
                <p class="text-lg text-[#002d45] font-medium">
                    {{ headline }}
                </p>
                <!-- <p
                    class="text-sm text-gray-600 leading-6"
                    v-if="displayMessage"
                >
                    {{ displayMessage }}
                </p> -->

                <!-- Helpful guidance per status family -->
                <div
                    class="text-left bg-gray-50 border border-gray-200 rounded-2xl p-4 mt-4"
                    v-if="tips.length"
                >
                    <p class="font-semibold text-[#002d45] mb-2">
                        {{ $t("What you can try") }}
                    </p>
                    <ul class="list-disc pl-5 space-y-1 text-gray-700">
                        <li v-for="(t, i) in tips" :key="i">{{ t }}</li>
                    </ul>
                </div>

                <!-- Optional details toggle (if present) -->
                <details
                    v-if="detailsJSON"
                    class="text-left bg-gray-50 border border-gray-200 rounded-2xl p-4"
                >
                    <summary class="cursor-pointer font-medium text-[#002d45]">
                        {{ $t("Technical details") }}
                    </summary>
                    <pre
                        class="mt-2 overflow-auto text-xs leading-5 whitespace-pre-wrap break-word"
                        >{{ detailsJSON }}</pre
                    >
                </details>

                <!-- Actions -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                    <OutlineButton
                        :width="'w-full'"
                        :outlineClass="'border border-gray-200'"
                        @click="retry"
                        title="Try again"
                    />

                    <Button
                        :width="'w-full'"
                        @click="goHome"
                        title="Go to Home"
                    />

                    <OutlineButton
                        v-if="showLogin"
                        :width="'w-full sm:col-span-2'"
                        :outlineClass="'border border-gray-200'"
                        @click="goLogin"
                        title="Login"
                    />

                    <a
                        class="w-full px-4 py-3 rounded-2xl font-medium transition duration-200 focus:outline-none focus:ring-0 border border-gray-200 hover:opacity-90 hover:shadow-lg text-[#005766] sm:col-span-2 text-center"
                        :href="`mailto:${supportEmail}?subject=Error ${displayCode}&body=${encodeURIComponent(
                            supportBody
                        )}`"
                    >
                        {{ $t("Contact Support") }}
                    </a>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/layouts/GuestLayout.vue";
import Button from "@/components/buttons/Button.vue";
import OutlineButton from "@/components/buttons/OutlineButton.vue";
import { useUserStore } from "@/stores/useUserStore";

import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { trans } from "laravel-vue-i18n";

const route = useRoute();
const router = useRouter();

const store = useUserStore();

/**
 * We read everything from query params that the interceptor sets:
 *   /error?code=404&message=Not%20Found&from=/settings
 * Optionally, it can pass `details` as a JSON-stringified object for advanced debugging.
 */
const code = computed(() => Number(route.query.code || 500));
const rawMessage = computed(() =>
    route.query.message ? String(route.query.message) : ""
);
const from = computed(() => (route.query.from ? String(route.query.from) : ""));
const detailsJSON = computed(() => {
    const d = route.query.details ? String(route.query.details) : "";
    if (!d) return "";
    try {
        // Pretty-print JSON if it looks like JSON; otherwise show raw text
        const parsed = JSON.parse(d);
        return JSON.stringify(parsed, null, 2);
    } catch {
        return d;
    }
});

const pageTitle = computed(() => trans("Something went wrong"));
const displayCode = computed(() => (code.value ? `${code.value}` : "—"));
const displayMessage = computed(
    () => rawMessage.value || friendlyTitleByCode(code.value)
);
const headline = computed(() => friendlyTitleByCode(code.value));

const showLogin = computed(() => {
    return [401, 403].includes(code.value) && !store.token;
});
const supportEmail = ""; // put your support email here e.g., "support@yourapp.com"

const supportBody = computed(() => {
    const payload = {
        code: code.value,
        message: displayMessage.value,
        from: from.value || window?.location?.pathname || "",
    };
    return `Hello Support,%0D%0A%0D%0AI encountered an error:%0D%0A${JSON.stringify(
        payload,
        null,
        2
    )}%0D%0A%0D%0AThanks.`;
});

function goHome() {
    router.push({ path: "/" });
}

function goLogin() {
    router.push({ path: "/login" });
}

function retry() {
    // Prefer returning to the failed page if we have it, otherwise reload.
    if (from.value) {
        router.push({ path: from.value });
    } else {
        router.go(-1);
    }
}

/** Human-friendly titles/messages per status code family */
function friendlyTitleByCode(status) {
    if (!status)
        return trans(
            "Network error. Please check your connection and try again."
        );
    const map = {
        400: trans("Bad request"),
        401: trans("Unauthorized — please log in"),
        403: trans("Forbidden — you don’t have access"),
        404: trans("We couldn’t find that page"),
        408: trans("Request timeout"),
        409: trans("Conflict"),
        410: trans("This resource is gone"),
        415: trans("Unsupported media type"),
        422: trans("Validation failed"),
        426: trans("Upgrade required"),
        429: trans("Too many requests — please wait and retry"),
        500: trans("Server error"),
        501: trans("Not implemented"),
        502: trans("Bad gateway"),
        503: trans("Service unavailable"),
        504: trans("Gateway timeout"),
    };
    // Choose best match: exact or family fallback
    if (map[status]) return map[status];
    if (status >= 400 && status < 500)
        return trans("Something’s not right with the request.");
    if (status >= 500 && status < 600) return trans("Our server had a hiccup.");
    return trans("An unexpected error occurred.");
}

/** Actionable tips tailored by status code */
const tips = computed(() => {
    const status = code.value || 0;
    if (status === 0) {
        return [
            trans("Check your internet connection."),
            trans("Disable VPN/Proxy temporarily and try again."),
            trans("If the issue persists, contact support."),
        ];
    }
    if (status === 401) {
        return [trans("Log in again, then retry your action.")];
    }
    if (status === 403) {
        return [trans("Ask your admin for the right permissions.")];
    }
    if (status === 404) {
        return [trans("Verify the URL or go back to the previous page.")];
    }
    if (status === 422) {
        return [trans("Review the form fields and fix validation errors.")];
    }
    if (status === 429) {
        return [trans("Wait a bit before retrying.")];
    }
    if (status >= 500 && status < 600) {
        return [
            trans("Retry in a moment — it’s likely temporary."),
            trans("If it keeps happening, let support know."),
        ];
    }
    return [
        trans("Try again or return home. If it persists, contact support."),
    ];
});
</script>
