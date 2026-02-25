<template>
    <div class="page bonus">
        <BonusHeader />
        <div class="page__body">
            <BodyTop />
            <!-- <BonusTasks /> -->
            <BonusMore :user="user" :isAuth="isAuth" />
        </div>
    </div>
</template>
<script>
import BonusHeader from "@/pages/bonus/components/BonusHeader.vue";
import BodyTop from "@/pages/bonus/components/BodyTop.vue";
import BonusTasks from "@/pages/bonus/components/BonusTasks.vue";
import BonusMore from "@/pages/bonus/components/BonusMore.vue";
import { useSeo } from "@/composables/useSeo.js";

import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";

export default {
    components: { BonusHeader, BodyTop, BonusTasks, BonusMore },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
    },
    setup() {
        // Инициализируем SEO для страницы бонусов
        const { seoData, updateOpenGraph } = useSeo('bonus');
        
        return {
            seoData,
            updateOpenGraph
        };
    },
};
</script>
