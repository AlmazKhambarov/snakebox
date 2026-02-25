<template>
    <div class="invite-page">
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p>Обработка реферальной ссылки...</p>
        </div>
    </div>
</template>

<script>
import { useSeo } from "@/composables/useSeo.js";

export default {
    name: "InvitePage",
    setup() {
        // Инициализируем SEO для страницы приглашения
        const { seoData, updateOpenGraph } = useSeo('invite');

        return {
            seoData,
            updateOpenGraph
        };
    },
    mounted() {
        this.handleReferralCode();
    },
    methods: {
        handleReferralCode() {
            const referralCode = this.$route.params.code;

            if (referralCode && this.isValidCode(referralCode)) {
                // Сохраняем в localStorage
                localStorage.setItem("referral_code", referralCode);

                // Устанавливаем куки с правильными параметрами
                this.setReferralCookie(referralCode);


                // Редирект на авторизацию Steam
                this.$router.push("/");
            } else {
                this.$router.push("/?error=invalid_referral");
            }
        },

        isValidCode(code) {
            return code && code.length >= 3 && code.length <= 20;
        },

        setReferralCookie(code) {
            const date = new Date();
            date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
            const expires = "expires=" + date.toUTCString();

            // Устанавливаем куки с правильными параметрами
            document.cookie = `referral_code=${code}; ${expires}; path=/; domain=.${window.location.hostname}; samesite=lax`;

          
        },
    },
};
</script>
