<template>
    <Banners :user="user" />
    <!-- <Games /> -->
    <Cases />
</template>
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { request } from "@/utils/request.js";
import { useSeo } from "@/composables/useSeo.js";
import Banners from "@/components/index/Banners.vue";
import Games from "@/components/index/Games.vue";
import Cases from "@/components/index/Cases.vue";

export default {
    components: { Games, Banners, Cases },
    setup() {
        // Инициализируем SEO для главной страницы
        const { seoData, updateOpenGraph } = useSeo("index");

        return {
            seoData,
            updateOpenGraph
        };
    },
    data() {
        return {};
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
    },
};
</script>
