<template>
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
        <Header />
        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
            <Sidebar />
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <router-view :key="$route.fullPath" />
                <Footer />
            </div>
        </div>
    </div>

    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-duotone ki-arrow-up">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import Header from "./components/Header.vue";
import Footer from "./components/Footer.vue";
import Sidebar from "./components/Sidebar.vue";

export default {
    components: {
        Header,
        Footer,
        Sidebar,
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
    },
    methods: {
        ...mapActions(useAuthStore, ["getUser"]),
    },
    mounted() {
        this.getUser();
        console.log('isAuth', this.isAuths, this.user);
        localStorage.setItem("data-bs-theme", "dark");
        localStorage.setItem("data-bs-theme-mode", "dark");

        document.documentElement.setAttribute("data-bs-theme", "dark");
    },
};
</script>

<style>
.preloader {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background: white;
    z-index: 99999999;
    transition: all 0.3s ease;
}

.preloader.dark {
    background: #26272f;
}

.spinner {
    width: 80px;
    height: 80px;
    border: 2px solid #1b84ff;
    border-top: 3px solid #1b84ff;
    border-radius: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;
    animation: spin 1s infinite ease;
    transition: all 0.3s ease;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
