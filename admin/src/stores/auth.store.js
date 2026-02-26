import { defineStore } from "pinia";
import Cookies from "js-cookie";
import { request } from "@/utils/request.js";

function getCookieOptions() {
    if (import.meta.env.VITE_APP_PRODUCTION === "on") {
        const domain = import.meta.env.VITE_APP_FRONTEND_URL?.replace(/^https?:\/\//, '').split('/')[0];
        return domain ? { path: "/", domain: `.${domain}` } : { path: "/" };
    }
    return { path: "/" };
}

export const useAuthStore = defineStore("auth", {
    state: () => ({
        token: Cookies.get("token") || null,
        userInfo: {
            id: 0,
            username: "",
            avatar: "",
            balance: "",
        },
        isLoaded: false,
    }),
    getters: {
        isAuth: (state) => !!state.token,
        user: (state) => state.userInfo,
    },
    actions: {
        async login(username, password) {
            try {
                const { data } = await request("POST", "/api/admin/login", { username, password });

                if (data.success) {
                    this.token = data.token;
                    Cookies.set("token", data.token, getCookieOptions());
                    this.userInfo = data.user;
                    this.isLoaded = true;
                    return { success: true };
                }

                return { success: false, message: data.message || "Ошибка авторизации" };
            } catch (e) {
                const message = e?.data?.message || e?.message || "Ошибка соединения с сервером";
                return { success: false, message };
            }
        },

        async getUser() {
            if (!Cookies.get("token")) {
                this.isLoaded = true;
                return false;
            }

            try {
                const { data } = await request("GET", "/api/user/get/admin");
                if (data.status === 200) {
                    this.userInfo = data.user;
                    this.isLoaded = true;
                    return true;
                }
                this.logOut();
                return false;
            } catch {
                this.logOut();
                return false;
            }
        },

        logOut() {
            this.token = null;
            this.userInfo = { id: 0, username: "", avatar: "", balance: "" };
            this.isLoaded = true;
            Cookies.remove("token", getCookieOptions());
        },
    },
});
