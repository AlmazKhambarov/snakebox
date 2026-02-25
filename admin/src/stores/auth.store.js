import { defineStore } from "pinia";
import Cookies from "js-cookie";
import { request } from "@/utils/request.js";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        token: Cookies.get("token") || null,
        userInfo: {
            id: 0,
            username: "",
            avatar: "",
            balance: "",
        },
        status: 0,
    }),
    getters: {
        isAuth: (state) => state.token !== null,
        user: (state) => state.userInfo,
    },
    actions: {
        async getUser() {
            if (!Cookies.get("token")) return location.href = import.meta.env.VITE_APP_FRONTEND_URL;

            await request("GET", "/api/user/get/admin").then(({ data }) => {
                console.log('ответ',data);
                if (data.status === 200) {
                    this.userInfo = data.user;
                    console.log(data.user);
                } else { 
                    window.location.href = import.meta.env.VITE_APP_FRONTEND_URL;
                }
            });
        },
        async logOut() {
            this.token = null;

            if (import.meta.env.VITE_APP_PRODUCTION === "on") {
                const domain = removeHttp(
                    import.meta.env.VITE_APP_FRONTEND_URL
                );
                if (domain) {
                    Cookies.remove("token", {
                        domain: `.${domain}`,
                        path: "/",
                    });
                }
            } else {
                Cookies.remove("token");
            }
        },
    },
});
