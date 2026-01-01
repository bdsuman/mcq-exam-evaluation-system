import "./bootstrap";
import "./axios";

import { createApp } from "vue";
import { createPinia } from "pinia";
import VueClickAway from "vue3-click-away";
import { i18nVue } from "laravel-vue-i18n";

import router from "./router/index.js";
import "../css/app.css";
import vDateFormat from "./directives/vDateFormat";
import vTrans from "./directives/vTrans";
import App from "./App.vue";
import mitt from "mitt";
import mixin from "./mixins";

const emitter = mitt();
// Debug global errors
window.addEventListener("error", (e) => {
    console.error("Global error:", e.error);
});

window.addEventListener("unhandledrejection", (e) => {
    console.error("Unhandled promise rejection:", e.reason);
});

const app = createApp(App);
const pinia = createPinia();
const langs = import.meta.glob('../lang/*.json', { eager: true });

// Initialize default language in localStorage if not set
if (!localStorage.getItem("language")) {
    localStorage.setItem("language", "en");
}

app.mixin(mixin)
    .use(router)
    .use(pinia)
    .use(VueClickAway)
    .use(i18nVue, {
        lang: localStorage.getItem("language") || "en",
        resolve: (lang) => {
            const file = langs[`../lang/${lang}.json`];
            return Promise.resolve(file || langs["../lang/en.json"]);
        },
    })
    .directive("date-format", vDateFormat)
    .directive("trans", vTrans)
    .provide("emitter", emitter);

app.config.globalProperties.$emitter = emitter;

try {
    app.mount("#app");

    router.onError((error) => {
        console.error("Router error:", error);
    });
} catch (error) {
    document.getElementById("app").innerHTML = `
        <div style="padding: 20px; color: red; font-family: Arial, sans-serif; background: #ffe6e6; border: 1px solid #ff9999; border-radius: 8px; margin: 20px;">
            <h2 style="margin-top: 0;">⚠️ Application Error</h2>
            <p>Failed to load the application. Please check the browser console for details.</p>
            <p><strong>Error:</strong> ${error.message}</p>
            <details style="margin-top: 10px;">
                <summary style="cursor: pointer; font-weight: bold;">Stack Trace</summary>
                <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow: auto; font-size: 12px;">${error.stack}</pre>
            </details>
        </div>
    `;
}
