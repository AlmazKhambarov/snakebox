import { defineStore } from "pinia";
import Cookies from "js-cookie";
import { removeHttp } from "@/utils/index.js";
import { request } from "@/utils/request.js";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        token: Cookies.get("token") || null,
        userInfo: {
            avatar: "",
            balance: "",
            event_points: 0,
            favorite_box: null,
            id: 0,
            isBanned: false,
            lives_count: 0,
            role: "",
            social: "",
            steamId: "",
            top_drop: null,
            total_bet: 0,
            tradeLink: "",
            username: "",
        },
        provably_fair: {
            server_seed_hashed: "N/A",
            client_seed: "N/A",
            games_count: "0",
        },
    }),

    getters: {
        isAuth: (state) => state.token !== null,
        user: (state) => state.userInfo,
        provably: (state) => state.provably_fair,
    },
    actions: {
        async setToken(newToken) {
            this.token = newToken;

            let options = {
                expires: 180,
                path: "/",
            };

            if (import.meta.env.VITE_APP_PRODUCTION === "on") {
                options.domain = `.${removeHttp(
                    import.meta.env.VITE_APP_FRONTEND_URL
                )}`;
            }

            Cookies.set("token", newToken, options);
            await this.getUser();
        },
        async getUser() {
            if (!Cookies.get("token")) return;

            await request("GET", "/user").then(({ data }) => {
                if (data.status === 200) {
                    this.userInfo = data.user;
                    this.provably_fair = data.provably;
                }
            });
        },

        async logOut() {
            this.token = null;

            const options = {
                path: "/",
            };

            if (import.meta.env.VITE_APP_PRODUCTION === "on") {
                options.domain = `.${removeHttp(
                    import.meta.env.VITE_APP_FRONTEND_URL
                )}`;
            } else {
                options.domain = "localhost";
            }

            Cookies.remove("token", options);
            window.location.href = import.meta.env.VITE_APP_FRONTEND_URL;
        },
    },
});
