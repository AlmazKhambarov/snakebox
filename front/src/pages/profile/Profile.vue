<template>
    <div class="page profile">
        <ProfileHeader />
        <div class="page__body">
            <ProfileTop :user="user" :logOut="logOut" />
            <Inventory />
        </div>
    </div>
</template>
<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

import ProfileHeader from "@/pages/profile/components/Header.vue";
import ProfileTop from "@/pages/profile/components/ProfileTop.vue";
import Inventory from "@/pages/profile/components/Inventory.vue";

export default {
    components: { ProfileHeader, ProfileTop, Inventory },
    setup() {
        // Инициализируем SEO для страницы профиля
        const { seoData, updateOpenGraph } = useSeo('profile');

        return {
            seoData,
            updateOpenGraph
        };
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
    },
    watch: {
        user: {
            handler(newUser) {
                if (newUser && newUser.username) {
                    // Обновляем Open Graph теги для профиля пользователя
                    this.updateOpenGraph({
                        title: `Профиль игрока ${newUser.username} - SNAKEBOX`,
                        description: `Профиль игрока ${newUser.username} на SNAKEBOX. Инвентарь, топ дропы, статистика.`,
                        image: newUser.avatar || '/images/default-avatar.png',
                        url: window.location.href
                    });
                }
            },
            immediate: true,
            deep: true
        }
    },
    methods: {
        ...mapActions(useAuthStore, ["logOut", "getUser"]),
    },
};
</script>
