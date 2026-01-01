import axios from "axios";
import { startLoading, stopLoading } from "@/globalLoader";
import { useNotify } from "@/composables/useNotification";
import { trans } from "laravel-vue-i18n";
const notify = useNotify();

axios.defaults.baseURL = "/api";
axios.defaults.headers.common = { Accept: "application/json" };

const loaderBlacklist = [
    "/background-track/dropdown/genre",
    "/background-track/dropdown/goal",
    "/session/dropdown/type",
    "/session/dropdown/topic",
    "/session/dropdown/style",
    "/session/dropdown/duration",
    "/session/dropdown/mandatory",
    "journal-questions/dropdown/question_option",
    "journal-questions/dropdown/question_type",
    "journal-questions/dropdown/topic",
    "/login",
    "/logout",
    "background-track-title-exists"
];

// ---- Debug flag (Vite/Webpack friendly) ----
function parseBool(v) {
    if (typeof v === "boolean") return v;
    if (typeof v === "string") return /^(true|1|yes|on)$/i.test(v);
    return false;
}
const isAppDebug = (() => {
    try {
        // Vite-style envs
        const viteEnv =
            typeof import.meta !== "undefined" && import.meta.env
                ? import.meta.env
                : undefined;
        if (viteEnv?.DEV === true) return true; // vite dev server
        if (viteEnv && parseBool(viteEnv.APP_DEBUG ?? viteEnv.APP_DEBUG))
            return true;

        // Webpack / Vue CLI style envs
        const pe = typeof process !== "undefined" ? process.env : undefined;
        if (pe?.NODE_ENV === "development") return true;
        if (pe && parseBool(pe.APP_DEBUG ?? pe.APP_DEBUG)) return true;

        return false;
    } catch {
        return false;
    }
})();

// ---- Loader helpers ----
let loaderStartTime = null;
function stopLoaderWithMinimumDelay(showLoader = true) {
    if (!showLoader) return;
    const started = loaderStartTime || Date.now();
    const elapsed = Date.now() - started;
    const remaining = 800 - elapsed;
    if (remaining > 0) setTimeout(stopLoading, remaining);
    else stopLoading();
    loaderStartTime = null;
}

// ---- Request Interceptor ----
axios.interceptors.request.use(
    (config) => {
        // allow per-request opt-out via config.metadata.showLoader = false
        config.metadata = config.metadata || {};

        const shouldSkipLoader = loaderBlacklist.some((pattern) =>
            config.url?.includes(pattern)
        );

        if (!shouldSkipLoader && config.metadata.showLoader !== false) {
            startLoading();
            loaderStartTime = Date.now();
            config.metadata.showLoader = true;
        }

        const raw = localStorage.getItem("token") || "";
        // strip accidental "Bearer " that might have been saved into storage
        const token = raw.replace(/^Bearer\s+/i, "").trim();

        if (token) {
            if (config.headers && typeof config.headers.set === "function") {
                // Axios v1 AxiosHeaders path
                config.headers.set("Authorization", `Bearer ${token}`);
                config.headers.set("Accept", "application/json");
            } else {
                // Fallback
                config.headers = {
                    ...(config.headers || {}),
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                };
            }
        }

        config.headers.set("language", localStorage.getItem("language") || "en");
        return config;
    },
    (error) => {
        stopLoaderWithMinimumDelay(true);
        return Promise.reject(error);
    }
);

// ---- Response Interceptor ----
axios.interceptors.response.use(
    (response) => {
        stopLoaderWithMinimumDelay(response.config?.metadata?.showLoader);
        return response;
    },
    (error) => {
        const cfg = error && error.config ? error.config : {};
        stopLoaderWithMinimumDelay(cfg.metadata?.showLoader);

        const currentPath = window.location.pathname || "/";

        // Status/message directly from response (or sensible fallbacks)
        const status =
            error && error.response && error.response.status
                ? error.response.status
                : 0; // 0 = network/CORS-like
        const message =
            (error &&
                error.response &&
                error.response.data &&
                (error.response.data.message || error.response.data.error)) ||
            (error && error.response && error.response.statusText) ||
            (error && error.message) ||
            "Unknown error";

        // Optional compact details payload
        let details = "";
        try {
            const body = error && error.response ? error.response.data : null;
            if (body && typeof body === "object") {
                const compact = {
                    errors: body.errors || undefined,
                    code: body.code || undefined,
                    traceId: body.traceId || body.id || undefined,
                };
                if (compact.errors || compact.code || compact.traceId) {
                    details = encodeURIComponent(JSON.stringify(compact));
                }
            }
        } catch {
            /* ignore */
        }

        // Clear token on 401; show toast if available
        if (status === 401) {
            try {
                localStorage.removeItem("token");
            } catch {}
            try {
                localStorage.removeItem("user");
            } catch {}
            try {
                notify.error({
                    message: trans("Something went wrong. Please try again later."),
                });
            } catch {}
            if (!currentPath.startsWith("/login")) {
                window.location.href = "/login";
            }
            return Promise.reject(error);
        }

        // ----- APP_DEBUG: don't redirect on errors -----
        if (isAppDebug) {
            try {
                notify.error({
                    message: trans("Something went wrong. Please try again later."),
                });
            } catch {}
            try {
                console.groupCollapsed(`[Axios][${status}] ${message}`);
                console.warn("Request config:", cfg);
                console.warn("Response data:", error?.response?.data);
                console.groupEnd();
            } catch {}
            return Promise.reject(error);
        }

        // Build /error route with query params (production)
        const params = new URLSearchParams({
            code: String(status),
            message: message,
            from: currentPath,
        });
        if (details) params.set("details", details);

        // Prevent redirect loop if already on /error
        if (!currentPath.startsWith("/error")) {
            window.location.href = `/error?${params.toString()}`;
        }

        return Promise.reject(error);
    }
);
