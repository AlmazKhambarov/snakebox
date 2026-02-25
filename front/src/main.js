import { createApp } from "vue";
import { createPinia } from "pinia";
import Cookies from "js-cookie";
import { reactive } from "vue";

import { Fancybox } from "@fancyapps/ui";

import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { toast } from "vue3-toastify";

// CSS файлы подключаются через index.html для лучшей производительности

import io from "socket.io-client";

import toastr from "toastr";

import App from "./App.vue";
import router from "./router";

const app = createApp(App);

// Импортируем SEO утилиты
import { initSeo } from '@/utils/seo.js';

// Добавляем SEO функцию в глобальные свойства
app.config.globalProperties.$seo = initSeo;

const frontSettings = reactive({
    streamer: Cookies.get("streamer") === "true",
    sounds: Cookies.get("sounds") !== "false", // По умолчанию включен, отключается только если явно установлено "false"

    toggleStreamer() {
        this.streamer = !this.streamer;
        Cookies.set("streamer", this.streamer, { expires: 365 });
    },
    toggleSounds() {
        this.sounds = !this.sounds;
        Cookies.set("sounds", this.sounds, { expires: 365 });
    },
});

toastr.options = {
    closeButton: false, // Скрываем стандартную кнопку закрытия
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: 300,
    hideDuration: 1000,
    timeOut: 5000,
    extendedTimeOut: 1000,
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
    // Кастомный рендеринг
    tapToDismiss: true,
    target: "body",
    closeHtml: "<button>&times;</button>",
};

const customToastr = {
    success(message, title = "Успех") {
        app.config.globalProperties.$playSound("/sounds/notification.mp3");
        this.showCustomToast(
            "success",
            message,
            title,
            "/assets/images/snake-glass.png"
        );
    },

    error(message, title = "Ошибка") {
        app.config.globalProperties.$playSound("/sounds/notification-bad.mp3");
        this.showCustomToast("error", message, title, "/images/error.png");
    },

    showCustomToast(type, message, title, image) {
        // Создаем кастомный HTML
        const toastHtml = `
            <div class="toast__wrapper ${type}">
                <div class="toast__image-wraper">
                    <img class="toast__image" src="${image}" alt="${title}">
                </div>
                <div class="toast__content">
                    <span class="toast__title">${title}</span>
                    <p class="toast__msg">${message}</p>
                </div>
            </div>
        `;

        // Используем native toastr с кастомным сообщением
        toastr[type](toastHtml, "", {
            closeButton: false,
            timeOut: 5000,
            extendedTimeOut: 1000,
        });
    },
};

app.config.globalProperties.$frontSettings = frontSettings;
app.config.globalProperties.$toast = toast;
app.config.globalProperties.$toastr = customToastr;
app.config.globalProperties.$playSound = (src, volume = 0.5) => {
    if (!frontSettings.sounds) volume = 0;
    const audio = new Audio(src);
    audio.volume = volume;
    audio.play().catch((err) => {
        console.warn("Failed to play sound:", err);
    });
};

const socketClient = io(import.meta.env.VITE_APP_SOCKET_URL, {
    autoConnect: false,
    transports: ["websocket"],
    reconnection: true,
    reconnectionDelay: 3 * 1000,
});

Fancybox.bind("[data-fancybox]", {
    closeButton: false, // ❌ убрать крестик "закрыть"
    dragToClose: false, // ❌ запретить закрытие перетаскиванием
});

app.directive('lazy', {
    mounted(el) {
      const loadImage = () => {
        const src = el.dataset.src
        if (!src) return
        el.src = src
        el.removeAttribute('data-src')
      }
  
      const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            loadImage()
            obs.unobserve(el)
          }
        })
      }, { rootMargin: '100px' })
  
      observer.observe(el)
    },
  })
  

app.use(createPinia());
app.use(router);

app.use(Vue3Toastify, {
    autoClose: 3000,
    position: "top-right",
    pauseOnHover: true,
    theme: "dark",
});

app.provide("socket-client", socketClient);

// window.vueApp = app;

app.mount("#app");

