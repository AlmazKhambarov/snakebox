<template>
    <div
        id="kt_app_sidebar"
        class="app-sidebar flex-column"
        data-kt-drawer="true"
        data-kt-drawer-name="app-sidebar"
        data-kt-drawer-activate="{default: true, lg: false}"
        data-kt-drawer-overlay="true"
        data-kt-drawer-width="225px"
        data-kt-drawer-direction="start"
        data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
    >
        <!--begin::Logo-->
        <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
            <!--begin::Logo image-->
            <router-link :to="{ name: 'main' }">
                <p
                    class="app-sidebar-logo-default fw-bold fs-1 mb-0 text-primary"
                >
                    SNAKEBOX
                </p>
                <!--        <img alt="Logo" src="/assets/media/logos/default-dark.svg" class="h-25px app-sidebar-logo-default" />-->
                <!--        <img alt="Logo" src="/assets/media/logos/default-small.svg" class="h-20px app-sidebar-logo-minimize" />-->
            </router-link>
            <!--end::Logo image-->
            <!--begin::Sidebar toggle-->
            <!--begin::Minimized sidebar setup-->
            <div
                id="kt_app_sidebar_toggle"
                class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
                data-kt-toggle="true"
                data-kt-toggle-state="active"
                data-kt-toggle-target="body"
                data-kt-toggle-name="app-sidebar-minimize"
            >
                <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <!--end::Sidebar toggle-->
        </div>
        <!--end::Logo-->
        <!--begin::sidebar menu-->
        <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
            <!--begin::Menu wrapper-->
            <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
                <!--begin::Scroll wrapper-->
                <div
                    id="kt_app_sidebar_menu_scroll"
                    class="scroll-y my-5 mx-3"
                    data-kt-scroll="true"
                    data-kt-scroll-activate="true"
                    data-kt-scroll-height="auto"
                    data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                    data-kt-scroll-wrappers="#kt_app_sidebar_menu"
                    data-kt-scroll-offset="5px"
                    data-kt-scroll-save-state="true"
                >
                    <!--begin::Menu-->
                    <div
                        class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6"
                        id="#kt_app_sidebar_menu"
                        data-kt-menu="true"
                        data-kt-menu-expand="false"
                    >
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu content-->
                            <div class="menu-content">
                                <span
                                    class="menu-heading fw-bold text-uppercase fs-7"
                                    >Управление</span
                                >
                            </div>
                            <!--end:Menu content-->
                        </div>
                        <!--end:Menu item-->
                        <div class="menu-item">
                            <router-link
                                :class="{ active: route.name === 'main' }"
                                class="menu-link"
                                :to="{ name: 'main' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-element-11 fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Главная</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'categories' }"
                                class="menu-link"
                                :to="{ name: 'categories' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-element-2 fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Категории</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'cases' }"
                                class="menu-link"
                                :to="{ name: 'cases' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-package fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Кейсы</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'cases.statistics' }"
                                class="menu-link"
                                :to="{ name: 'cases.statistics' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-chart-simple fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Статистика кейсов</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'raffles' }"
                                class="menu-link"
                                :to="{ name: 'raffles' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-gift fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Розыгрыши</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'events' }"
                                class="menu-link"
                                :to="{ name: 'events' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-star fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Ивенты</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'banners' }"
                                class="menu-link"
                                :to="{ name: 'banners' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-picture fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Баннеры</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'referral.management' }"
                                class="menu-link"
                                :to="{ name: 'referral.management' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-people fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Рефералы</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'promocodes' }"
                                class="menu-link"
                                :to="{ name: 'promocodes' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-discount fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Промокоды</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'payments' }"
                                class="menu-link"
                                :to="{ name: 'payments' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-dollar fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Пополнения</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'methods' }"
                                class="menu-link"
                                :to="{ name: 'methods' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-minus-square fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title"
                                    >Методы пополнения</span
                                >
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'withdraws' }"
                                class="menu-link"
                                :to="{ name: 'withdraws' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-minus-square fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Выводы предметов</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'all.items' }"
                                class="menu-link"
                                :to="{ name: 'all.items' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-book-open fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Предметы</span>
                            </router-link>

                            <router-link
                                :class="{
                                    active:
                                        route.name === 'users' ||
                                        route.name === 'users.id',
                                }"
                                class="menu-link"
                                :to="{ name: 'users' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-user fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Пользователи</span>
                            </router-link>

                            <router-link
                                :class="{ active: route.name === 'settings' }"
                                class="menu-link"
                                :to="{ name: 'settings' }"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-setting fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Настройки</span>
                            </router-link>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Scroll wrapper-->
            </div>
            <!--end::Menu wrapper-->
        </div>
        <!--end::sidebar menu-->
    </div>
</template>
<script setup>
import { useRoute } from "vue-router";
import { onMounted } from "vue";
import Cookies from "js-cookie";

const route = useRoute();

onMounted(() => {
    // if (isset($_COOKIE["sidebar_minimize_state"]) && $_COOKIE["sidebar_minimize_state"] === "on") {
    //     1. "src/js/layout/sidebar.js" adds "sidebar_minimize_state" cookie value to save the sidebar minimize state.
    //     2. Set data-kt-app-sidebar-minimize="on" attribute for body tag.
    //     3. Set data-kt-toggle-state="active" attribute to the toggle element with "kt_app_sidebar_toggle" id.
    //     4. Add "active" class to to sidebar toggle element with "kt_app_sidebar_toggle" id.
    // }
    const cookies = Cookies.get("sidebar_minimize_state");

    if (cookies && cookies === "on") {
        document.body.setAttribute("data-kt-app-sidebar-minimize", "on");
        document
            .getElementById("kt_app_sidebar_toggle")
            .setAttribute("data-kt-toggle-state", "active");
        document
            .getElementById("kt_app_sidebar_toggle")
            .classList.add("active");
    }
});
</script>
