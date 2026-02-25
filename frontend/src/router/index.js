import { createRouter, createWebHistory } from "vue-router";
import { initSeo, getSeoConfig } from "@/utils/seo.js";

const routes = [
    {
        path: "/",
        name: "index",
        component: () => import("../pages/Index.vue"),
    },
    {
        path: "/auth/callback",
        name: "auth.callback",
        component: () => import("../components/AuthCallback.vue"),
    },
    {
        path: "/bonus",
        name: "bonus",
        component: () => import("../pages/bonus/Bonus.vue"),
    },
    {
        path: "/referrals",
        name: "referrals",
        component: () => import("../pages/referrals/Referrals.vue"),
    },
    {
        path: "/referrals/users",
        name: "referrals-users",
        component: () => import("../pages/referrals/ReferralsUsers.vue"),
    },
    {
        path: "/profile",
        name: "profile",
        component: () => import("../pages/profile/Profile.vue"),
    },
    {
        path: "/contracts",
        name: "contracts",
        component: () => import("../pages/contracts/Contracts.vue"),
    },
    {
        path: "/upgrade",
        name: "upgrade",
        component: () => import("../pages/upgrade/Upgrade.vue"),
    },
    {
        path: "/event",
        name: "event",
        component: () => import("../pages/event/Event.vue"),
    },
    {
        path: "/case/:url",
        name: "case",
        component: () => import("../pages/cases/Cases.vue"),
    },
    {
        path: "/raffle",
        name: "raffle",
        component: () => import("../pages/raffle/Raffle.vue"),
    },
    {
        path: "/invite/:code",
        name: "invite",
        component: () => import("../pages/referrals/Invite.vue"),
    },
    {
        path: "/terms",
        name: "terms",
        component: () => import("../pages/terms/Terms.vue"),
    },
    {
        path: "/policy",
        name: "policy",
        component: () => import("../pages/policy/Policy.vue"),
    },
    {
        path: "/deposit",
        name: "deposit",
        component: () => import("../pages/deposit/Deposit.vue"),
    },
    {
        path: "/vip",
        name: "vip",
        component: () => import("../pages/vip/Vip.vue"),
    },
    {
        path: "/profile/:id",
        name: "OtherProfile",
        component: () => import("../pages/otherProfile/Profile.vue"),
    },
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: () => import("../pages/NotFound.vue"),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

router.afterEach((to) => {
    // Яндекс.Метрика
    if (window.ym) {
        ym(105052335, "hit", to.fullPath);
    }

    // Google Analytics 4
    if (window.gtag) {
        gtag("event", "page_view", {
            page_path: to.fullPath,
            page_location: window.location.href,
            page_title: document.title,
        });
    }

    // Автоматическая инициализация SEO по имени роута, если есть конфиг
    if (to.name) {
        const seo = getSeoConfig(String(to.name));
        if (seo && seo.title) {
            initSeo(String(to.name), to.params || {});
        }
    }
});

export default router;
