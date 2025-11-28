import "./bootstrap";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

import { createApp, h } from "vue";
import { createInertiaApp, router } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import Vue3Toastify, { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

// Toasts
router.on("success", (event) => {
    const flash = event.detail.page.props.flash;
    if (flash) {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
        if (flash.info) {
            toast.info(flash.info);
        }
    }
});

const appName = import.meta.env.VITE_APP_NAME || "";

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Vue3Toastify, {
                autoClose: 3000,
                position: "top-right",
                theme: "dark",
            });

        app.mount(el);

        return app;
    },
    progress: { color: "blue" },
});
