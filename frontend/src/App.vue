<template>
    <div v-if="user && user.isBanned" class="banned-overlay">
        <div class="banned-message">
            <div class="banned-container">
                <div class="banned-icon">🚫</div>
                <h1 class="banned-title">Вы заблокированы</h1>
                <p class="banned-text">Ваш аккаунт был заблокирован администрацией сайта.</p>
                <p class="banned-text">По всем вопросам обращайтесь в поддержку.</p>
            </div>
        </div>
    </div>
    <div
        v-else
        :class="{
            streamer_on: $frontSettings.streamer,
        }"
    >
        <LeftSide />
        <Header :currentOnline="currentOnline" />
        <main>
            <div class="wrapper">
                <router-view :key="$route.fullPath" />
            </div>
        </main>
        <Footer :stats="stats" :currentOnline="currentOnline" :user="user" />
        <AuthModal />
        <Settings />
        <BonusRoll />
        <WhoJobEvent />
        <Cashback />
        <CloseVip />
        <MobileBottom />
    </div>
    <!-- <div
    style="
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    "
  >
    <div class="logo_4353"></div>
    <div class="content_9385">
      <h1 class="title_8428">
        Технические <br />
        работы
      </h1>

      <p class="subtitle_8284">Скоро всё наладится...</p>
    </div>
  </div> -->
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useMainStore } from "@/stores/main.store.js";
import { request } from "@/utils/request.js";
import LeftSide from "@/components/LeftSide.vue";
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";

// Modals
import AuthModal from "@/components/modals/Auth.vue";
import Settings from "@/components/modals/Settings.vue";
import BonusRoll from "@/components/modals/BonusRoll.vue";
import WhoJobEvent from "@/components/modals/WhoJobEvent.vue";
import Cashback from "@/components/modals/Cashback.vue";
import CloseVip from "@/components/modals/CloseVip.vue";
import MobileBottom from "@/components/mobileBottom.vue";
export default {
    inject: ["socket-client"],
    components: {
        LeftSide,
        Header,
        Footer,
        AuthModal,
        Settings,
        BonusRoll,
        WhoJobEvent,
        Cashback,
        CloseVip,
        MobileBottom,
    },
    data() {
        return {
            stats: [],
            currentOnline: 0,
        };
    },

    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
        ...mapState(useMainStore, ["getInformation", "getSettings"]),
    },
    methods: {
        ...mapActions(useAuthStore, ["logOut", "getUser"]),
        ...mapState(useMainStore, ["settings"]),
        subscribeSocket() {
            const socket = this["socket-client"];
            if (!socket) return;

            if (!socket.connected) {
                socket.connect();
            }

            socket.on("online", (data) => {
                this.currentOnline = data;
            });
            socket.on("userBalance", ({ user_id, balance, event_points }) => {
                if (this.isAuth && this.user.id === user_id) {
                    this.user.balance = balance;
                    this.user.event_points = event_points;
                }
            });
        },
        async statistics() {
            await request("GET", "/main/stats").then(({ data }) => {
                if (!data.result) {
                    console.warn(data.message);
                } else {
                    this.stats = data.statistics;
                }
            });
        },
    },
    mounted() {
        this.subscribeSocket();
        this.getUser();
        this.getInformation();
        this.getSettings();
        this.statistics();

        setTimeout(() => {
            this.loading = false;
        }, 1500);
    },
};
</script>

<style scoped>
.banned-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.banned-message {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    width: 100%;
    height: 100%;
}

.banned-container {
    text-align: center;
    max-width: 500px;
    padding: 3rem;
    background: rgba(220, 53, 69, 0.1);
    border: 2px solid #dc3545;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

.banned-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.banned-title {
    font-size: 2rem;
    font-weight: bold;
    color: #dc3545;
    margin-bottom: 1rem;
}

.banned-text {
    font-size: 1.1rem;
    color: #fff;
    margin-bottom: 0.5rem;
    line-height: 1.6;
}
</style>
