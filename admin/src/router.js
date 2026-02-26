import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth.store.js";

const routes = [
    {
        path: "/login",
        component: () => import("./pages/Login.vue"),
        name: "login",
        meta: { guest: true }
    },
    {
        path: "/",
        component: () => import("./pages/Index.vue"),
        name: "main",
        meta: { requiresAuth: true }
    },
    {
        path: "/categories",
        component: () => import("./pages/Categories.vue"),
        name: "categories",
        meta: { requiresAuth: true }
    },
    {
        path: "/cases",
        component: () => import("./pages/Cases/Index.vue"),
        name: "cases",
        meta: { requiresAuth: true }
    },
    {
        path: "/cases/statistics",
        component: () => import("./pages/Cases/Statistics.vue"),
        name: "cases.statistics",
        meta: { requiresAuth: true }
    },
    {
        path: "/cases/:id/items",
        component: () => import("./pages/Cases/Items.vue"),
        name: "items",
        meta: { requiresAuth: true }
    },
    {
        path: "/raffles",
        component: () => import("./pages/Raffles.vue"),
        name: "raffles",
        meta: { requiresAuth: true }
    },
    {
        path: "/referral-management",
        component: () => import("./pages/ReferralManagement.vue"),
        name: "referral.management",
        meta: { requiresAuth: true }
    },
    {
        path: "/promocodes",
        component: () => import("./pages/Promocodes.vue"),
        name: "promocodes",
        meta: { requiresAuth: true }
    },
    {
        path: "/payments",
        component: () => import("./pages/Payments.vue"),
        name: "payments",
        meta: { requiresAuth: true }
    },
    {
        path: "/withdraws",
        component: () => import("./pages/Withdraws.vue"),
        name: "withdraws",
        meta: { requiresAuth: true }
    },
    {
        path: "/items",
        component: () => import("./pages/Items.vue"),
        name: "all.items",
        meta: { requiresAuth: true }
    },
    {
        path: "/users",
        component: () => import("./pages/Users/Index.vue"),
        name: "users",
        meta: { requiresAuth: true }
    },
    {
        path: "/users/:id",
        component: () => import("./pages/Users/User.vue"),
        name: "users.id",
        meta: { requiresAuth: true }
    },
    {
        path: "/settings",
        component: () => import("./pages/Settings.vue"),
        name: "settings",
        meta: { requiresAuth: true }
    },
    {
        path: "/methods",
        component: () => import("./pages/PaymentMethods.vue"),
        name: "methods",
        meta: { requiresAuth: true }
    },
    {
        path: "/events",
        component: () => import("./pages/Events.vue"),
        name: "events",
        meta: { requiresAuth: true }
    },
    {
        path: "/banners",
        component: () => import("./pages/Banners.vue"),
        name: "banners",
        meta: { requiresAuth: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Initial fetch of user info if it's not loaded yet
    if (authStore.token && !authStore.isLoaded) {
        await authStore.getUser();
    }

    const isAuthenticated = authStore.isAuth;

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: "login" });
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: "main" });
    } else {
        next();
    }
});

export default router;
