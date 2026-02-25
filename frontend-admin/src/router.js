import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("./pages/Index.vue"),
        name: "main",
    },
    {
        path: "/categories",
        component: () => import("./pages/Categories.vue"),
        name: "categories",
    },
    {
        path: "/cases",
        component: () => import("./pages/Cases/Index.vue"),
        name: "cases",
    },
    {
        path: "/cases/statistics",
        component: () => import("./pages/Cases/Statistics.vue"),
        name: "cases.statistics",
    },
    {
        path: "/cases/:id/items",
        component: () => import("./pages/Cases/Items.vue"),
        name: "items",
    },
    {
        path: "/raffles",
        component: () => import("./pages/Raffles.vue"),
        name: "raffles",
    },
    {
        path: "/referral-management",
        component: () => import("./pages/ReferralManagement.vue"),
        name: "referral.management",
    },
    {
        path: "/promocodes",
        component: () => import("./pages/Promocodes.vue"),
        name: "promocodes",
    },
    {
        path: "/payments",
        component: () => import("./pages/Payments.vue"),
        name: "payments",
    },
    {
        path: "/withdraws",
        component: () => import("./pages/Withdraws.vue"),
        name: "withdraws",
    },

    {
        path: "/items",
        component: () => import("./pages/Items.vue"),
        name: "all.items",
    },
    {
        path: "/users",
        component: () => import("./pages/Users/Index.vue"),
        name: "users",
    },
    {
        path: "/users/:id",
        component: () => import("./pages/Users/User.vue"),
        name: "users.id",
    },
    {
        path: "/settings",
        component: () => import("./pages/Settings.vue"),
        name: "settings",
    },
    {
        path: "/methods",
        component: () => import("./pages/PaymentMethods.vue"),
        name: "methods",
    },
    {
        path: "/events",
        component: () => import("./pages/Events.vue"),
        name: "events",
    },
    {
        path: "/banners",
        component: () => import("./pages/Banners.vue"),
        name: "banners",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
