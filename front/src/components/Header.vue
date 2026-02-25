<template>
    <header class="header" :class="{ active: isScrolled }">
        <div class="wrapper">
            <Live />
            <div class="header__bottom">
                <div class="header__left">
                    <router-link
                        :to="{ name: 'index' }"
                        class="header__logotype"
                    >
                            <img src="/assets/icons/logo.webp" alt="" />
                        <!-- <span>{{ settings.site_name }}</span> -->
                    </router-link>
                    <div class="header__online">
                        <div class="header__online-ellipse"></div>
                        <div class="header__online-info">
                            <span class="online">{{ currentOnline }}</span>
                            <p>онлайн</p>
                        </div>
                    </div>
                    <!-- <div class="dropdown gpu-boost" :class="show && 'active'">
            <button
              @click="show = !show"
              type="button"
              class="header__language dropdown__btn--small"
            >
              <img src="/images/flags/ru.svg" alt="" />
              <span>Русский</span>
              <i></i>
            </button>
            <div class="dropdown__inner">
              <a @click="show = !show" href="javascript:;" class="dropdown__link">
                <img src="/images/flags/en.svg" alt="" />
                <span>English</span>
              </a>
            </div>
          </div> -->
                    <div class="header__divider"></div>
                    <nav class="header__nav gpu-boost">
                        <li>
                            <router-link :to="{ name: 'event' }" class="event">
                                <div
                                    class="icon gold"
                                    style="
                                        mask-image: url('/assets/icons/event.svg');
                                    "
                                ></div>
                                События
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'bonus' }">
                                <div
                                    class="icon gray"
                                    style="
                                        mask-image: url('/images/icons/bonus.svg');
                                    "
                                ></div>
                                Бонусы
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'referrals' }">
                                <div
                                    class="icon gray"
                                    style="
                                        mask-image: url('/images/icons/refferal.svg');
                                    "
                                ></div>
                                Партнёрство
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'vip' }" class="vip">
                                <div
                                    class="icon gray"
                                    style="
                                        mask-image: url('/images/icons/vip.svg');
                                    "
                                ></div>
                                Вип-клуб
                            </router-link>
                        </li>
                       
                    </nav>
                </div>
                <div class="header__right gpu-boost">
                    <div class="header__balance" v-if="isAuth">
                        <div class="sum">
                            <div
                                class="icon coin"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            <span id="wallet">{{ user.balance / 100 }}</span>
                        </div>
                        <div class="header__balance-buttons">
                            <router-link
                                :to="{ name: 'deposit' }"
                                class="header__balance-button deposit"
                            >
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/plus.svg');
                                    "
                                ></div>
                            </router-link>
                        </div>
                    </div>

                    <div class="header__auth">
                        <router-link
                            v-if="isAuth"
                            :to="{ name: 'profile' }"
                            class="btn btn--gray"
                        >
                            <div class="btn__inner">
                                <img
                                    class="header__user-avatar"
                                    :src="user.avatar"
                                    alt=""
                                />
                            </div>
                            <svg
                                v-if="user.is_vip"
                                width="28"
                                height="16"
                                viewBox="0 0 28 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="avatar_vipBadge"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M4.89886 0C3.46021 0 2.23054 1.05217 1.98693 2.49183L0.294193 12.4918C-0.0160643 14.3249 1.37427 16 3.20613 16H23.0704C24.5059 16 25.7335 14.9527 25.9809 13.5172L27.7045 3.51715C28.0209 1.6814 26.6294 0 24.794 0H4.89886Z"
                                    fill="#ffffff"
                                    stroke="#1d1e20"
                                    stroke-width="0"
                                ></path>
                                <path
                                    d="M9.13104 9.41751L11.7295 4.88333H13.577L13.5344 5.13919L9.93139 11.106H7.99002L6.40576 5.13919L6.43963 4.88333H8.05293L9.13104 9.41751Z"
                                    fill="#1d1e20"
                                ></path>
                                <path
                                    d="M14.5042 11.106H12.8164L13.8635 4.88333H15.5523L14.5042 11.106Z"
                                    fill="#1d1e20"
                                ></path>
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M20.1841 4.88333C21.4564 4.88333 22.5689 5.7493 22.3645 7.14993C22.1681 8.4272 21.301 9.06584 19.7631 9.06595H17.3707L17.0262 11.106H15.3384L16.3855 4.88333H20.1841ZM17.6069 7.64407H19.8202C20.3267 7.64407 20.6135 7.44455 20.6806 7.04642C20.7507 6.56564 20.4916 6.30627 20.0234 6.30618H17.8469L17.6069 7.64407Z"
                                    fill="#1d1e20"
                                ></path>
                            </svg>
                        </router-link>
                        <a v-else href="#login" data-fancybox class="btn">
                            <div class="btn__inner">
                                <div class="btn__inner-left">
                                    <span>Войти</span>
                                    <p>Осуществить вход</p>
                                </div>
                            </div>
                            <div
                                class="icon gray btn__icon green"
                                style="
                                    mask-image: url('/assets/icons/arrow-top-right.svg');
                                "
                            ></div>
                        </a>
                        <a
                            href="#settings"
                            data-fancybox=""
                            class="header__settings-btn"
                        >
                            <div
                                class="icon gray"
                                style="
                                    mask-image: url('/images/icons/settings.svg');
                                "
                            ></div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="header__mask"></div>
    </header>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useMainStore } from "@/stores/main.store.js";
import Live from "./Live.vue";

export default {
    props: { currentOnline: Number },
    components: { Live },
    data() {
        return {
            tab: 1,
            show: false,
            isScrolled: false,
            scrollThreshold: 50, // порог прокрутки в пикселях
        };
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user", "logOut"]),
        ...mapState(useMainStore, ["settings"]),
    },
    methods: {
        ...mapActions(useMainStore, ["getSettings"]),
        logInSteam() {
            window.location.href =
                import.meta.env.VITE_APP_BACKEND_URL + "/api/auth/steam/";
        },
        toggleBlock() {
            this.isOpen = !this.isOpen;
        },
        handleScroll() {
            this.isScrolled = window.scrollY > this.scrollThreshold;
        },
    },
    mounted() {
        window.addEventListener("scroll", this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener("scroll", this.handleScroll);
    },
};
</script>
