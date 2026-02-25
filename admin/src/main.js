// import "./assets/main.css";

import Vue3Toastify from "vue3-toastify";

import { createApp } from "vue";
import { createPinia } from "pinia";

import "vue3-toastify/dist/index.css";

import App from "./App.vue";
import router from "./router";

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(Vue3Toastify, {
    position: "top-right",
});

app.mount("#kt_app_root");
