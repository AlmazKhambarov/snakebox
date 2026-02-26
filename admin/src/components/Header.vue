<template>
  <div
    id="kt_app_header"
    class="app-header"
    data-kt-sticky="true"
    data-kt-sticky-activate="{default: true, lg: true}"
    data-kt-sticky-name="app-header-minimize"
    data-kt-sticky-offset="{default: '200px', lg: '0'}"
    data-kt-sticky-animation="false"
  >
    <!--begin::Header container-->
    <div
      class="app-container container-fluid d-flex align-items-stretch justify-content-between"
      id="kt_app_header_container"
    >
      <!--begin::Sidebar mobile toggle-->
      <div
        class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2"
        title="Show sidebar menu"
      >
        <div
          class="btn btn-icon btn-active-color-primary w-35px h-35px"
          id="kt_app_sidebar_mobile_toggle"
        >
          <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
            <span class="path1"></span>
            <span class="path2"></span>
          </i>
        </div>
      </div>
      <!--end::Sidebar mobile toggle-->
      <!--begin::Mobile logo-->
      <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
        <router-link
          :to="{ name: 'main' }"
          class="d-lg-none align-items-center justify-content-center"
        >
          <p class="text-primary fw-bold fs-3 mb-0">SNAKEBOX</p>
        </router-link>
      </div>
      <!--end::Mobile logo-->
      <!--begin::Header wrapper-->
      <div
        class="d-flex align-items-stretch justify-content-end flex-lg-grow-1"
        id="kt_app_header_wrapper"
      >
        <!--begin::Navbar-->
        <div class="app-navbar flex-shrink-0">
          <div class="app-navbar-item ms-1 ms-md-4">
            <!--begin::Menu toggle-->
            <!--begin::Menu-->
            <div
              class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
              data-kt-menu="true"
              data-kt-element="theme-mode-menu"
            >
              <!--begin::Menu item-->
              <div class="menu-item px-3 my-0">
                <a
                  href="#"
                  class="menu-link px-3 py-2"
                  data-kt-element="mode"
                  data-kt-value="dark"
                >
                  <span class="menu-icon" data-kt-element="icon">
                    <i class="ki-duotone ki-night-day fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                      <span class="path3"></span>
                      <span class="path4"></span>
                      <span class="path5"></span>
                      <span class="path6"></span>
                      <span class="path7"></span>
                      <span class="path8"></span>
                      <span class="path9"></span>
                      <span class="path10"></span>
                    </i>
                  </span>
                  <span class="menu-title">Светлая</span>
                </a>
              </div>
              <!--end::Menu item-->
              <!--begin::Menu item-->
              <div class="menu-item px-3 my-0">
                <a
                  href="#"
                  class="menu-link px-3 py-2"
                  data-kt-element="mode"
                  data-kt-value="dark"
                >
                  <span class="menu-icon" data-kt-element="icon">
                    <i class="ki-duotone ki-moon fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                  <span class="menu-title">Тёмная</span>
                </a>
              </div>
              <!--end::Menu item-->
              <!--begin::Menu item-->
              <div class="menu-item px-3 my-0">
                <a
                  href="#"
                  class="menu-link px-3 py-2"
                  data-kt-element="mode"
                  data-kt-value="dark"
                >
                  <span class="menu-icon" data-kt-element="icon">
                    <i class="ki-duotone ki-screen fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                      <span class="path3"></span>
                      <span class="path4"></span>
                    </i>
                  </span>
                  <span class="menu-title">Системная</span>
                </a>
              </div>
              <!--end::Menu item-->
            </div>
            <!--end::Menu-->
          </div>
          <!--begin::User menu-->
          <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
            <!--begin::Menu wrapper-->
            <div
              class="cursor-pointer symbol symbol-35px"
              data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
              data-kt-menu-attach="parent"
              data-kt-menu-placement="bottom-end"
            >
              <img :src="authStore.user.avatar" class="rounded-3" alt="user" />
            </div>
            <!--begin::User account menu-->
            <div
              class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
              data-kt-menu="true"
            >
              <!--begin::Menu item-->
              <div class="menu-item px-3">
                <div class="menu-content d-flex align-items-center px-3">
                  <!--begin::Avatar-->
                  <div class="symbol symbol-50px me-5">
                    <img alt="Logo" :src="authStore.user.avatar" />
                  </div>
                  <!--end::Avatar-->
                  <!--begin::Username-->
                  <div class="d-flex flex-column">
                    <div class="fw-bold d-flex align-items-center fs-5">
                      {{ authStore.user.username }}
                    </div>
                  </div>
                  <!--end::Username-->
                </div>
              </div>
              <!--end::Menu item-->
              <!--begin::Menu separator-->
              <div class="separator my-2"></div>
              <!--end::Menu separator-->
              <div class="menu-item px-5">
                <router-link :to="{ name: 'change.password' }" class="menu-link px-5">Сменить пароль</router-link>
              </div>
              <!--end::Menu item-->
              <!--begin::Menu item-->
              <div class="menu-item px-5">
                <a @click="handleLogout" class="menu-link px-5">Выход</a>
              </div>
              <!--end::Menu item-->
            </div>
            <!--end::User account menu-->
            <!--end::Menu wrapper-->
          </div>
          <!--end::User menu-->
        </div>
        <!--end::Navbar-->
      </div>
      <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
  </div>
</template>
<script setup>
import { useAuthStore } from "../stores/auth.store.js";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = () => {
  authStore.logOut();
  router.push({ name: 'login' });
};
</script>
