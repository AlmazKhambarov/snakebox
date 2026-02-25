<template>
    <div class="global-intro">
        <div class="global-intro__grid">
            <div class="global-intro__main">
                <div 
                    class="global-carousel" 
                    v-if="banners.length > 0"
                    @mouseenter="stopAutoSlide"
                    @mouseleave="startAutoSlide"
                >
                    <div class="global-carousel__wrapper">
                        <div
                            class="global-carousel__items"
                            :style="{
                                transition: 'transform 0.5s ease',
                                transform: `translateX(-${currentSlide * 100}%)`
                            }"
                        >
                            <a
                                v-for="(banner, index) in banners"
                                :key="banner.id"
                                :href="banner.link || '#'"
                                class="global-carousel__item"
                                :style="{ order: index }"
                            >
                                <div
                                    class="carousel-item carousel-item_big carousel-item_responsive"
                                    :style="{
                                        backgroundImage: `url(${banner.image})`
                                    }"
                                >
                                    <div v-if="banner.title" class="carousel-item__title">
                                        {{ banner.title }}
                                    </div>
                                    <div v-if="banner.text" class="carousel-item__text">
                                        {{ banner.text }}
                                    </div>
                                    <div v-if="banner.button_text" class="carousel-item__btn">
                                        <button
                                            class="btn_banner btn_color-light btn_uppercase btn_fullwidth"
                                        >
                                            <div class="btn__content">
                                                <div class="btn__label">
                                                    {{ banner.button_text }}
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="global-carousel__controls" v-if="banners.length > 1">
                        <div
                            class="global-carousel__control global-carousel__control_prev icon icon_arrow-flat"
                            @click="prevSlide"
                        ></div>
                        <div
                            class="global-carousel__control global-carousel__control_next icon icon_arrow-flat"
                            @click="nextSlide"
                        ></div>
                    </div>
                    <div class="global-carousel__dots" v-if="banners.length > 1">
                        <div
                            v-for="(banner, index) in banners"
                            :key="banner.id"
                            class="global-carousel__dot"
                            :class="{ active: currentSlide === index }"
                            @click="goToSlide(index)"
                        ></div>
                    </div>
                </div>
            </div>
            <div class="global-intro__secondary">
                <div class="global-intro__secondary-item" v-if="dailyBonus">
                    <div
                        class="carousel-item carousel-item_mini carousel-item_code"
                        action="copy"
                        :data-value="dailyBonus.code"
                    >
                        <div class="carousel-item__title">
                            Бонус к пополнению <strong>{{ dailyBonus.percent }} %</strong>
                        </div>
                        <div class="carousel-item__text">{{ dailyBonus.code }}</div>
                        <div class="carousel-item__timer">
                        </div>
                        <div class="carousel-item__btn">
                            <button
                                class="carousel-item__btn-mix btn_banner btn_color-light btn_uppercase btn_fullwidth btn_with-icon"
                                @click="copyPromocode"
                            >
                                <div class="btn__content">
                                    <div
                                        class="btn_banner_icon icon_banner icon_copy"
                                    ></div>
                                    <div class="btn__label">Скопировать</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="global-intro__secondary-item">
                    <router-link
                        :to="{ name: 'bonus' }"
                        class="carousel-item carousel-item_mini carousel-item_dailybonus carousel-item_available"
                    >
                        <div class="carousel-item__title">Ежедневный бонус</div>
                        <div class="carousel-item__text">Уже доступен!</div>

                        <div class="carousel-item__btn">
                            <button
                                class="btn_banner btn_color-info btn_uppercase btn_fullwidth btn_with-icon"
                            >
                                <div class="btn__content">
                                    <div
                                        class="btn_banner_icon icon_banner icon_dialy"
                                    ></div>
                                    <div class="btn__label">Получить</div>
                                </div>
                            </button>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="global-intro__popups"></div>
    </div>
</template>

<script>
import { request } from "@/utils/request.js";

export default {
    props: { user: Object },
    data() {
        return {
            banners: [],
            dailyBonus: null,
            currentSlide: 0,
            autoSlideInterval: null,
        };
    },
    mounted() {
        this.getBanners();
        this.getDailyBonus();
    },
    beforeUnmount() {
        this.stopAutoSlide();
    },
    watch: {
        banners: {
            handler(newBanners) {
                if (newBanners && newBanners.length > 1) {
                    this.stopAutoSlide();
                    this.startAutoSlide();
                }
            },
            immediate: true,
        },
    },
    methods: {
        async getBanners() {
            try {
                const { data } = await request("GET", "/banners");
                if (data.success && data.banners) {
                    this.banners = data.banners;
                }
            } catch (error) {
                console.error("Error loading banners:", error);
            }
        },
        async getDailyBonus() {
            try {
                const { data } = await request("GET", "/promocodes/daily-bonus");
                if (data.success && data.promocode) {
                    this.dailyBonus = data.promocode;
                }
            } catch (error) {
                console.error("Error loading daily bonus:", error);
            }
        },
        nextSlide() {
            if (this.banners.length > 0) {
                this.currentSlide = (this.currentSlide + 1) % this.banners.length;
            }
        },
        prevSlide() {
            if (this.banners.length > 0) {
                this.currentSlide = (this.currentSlide - 1 + this.banners.length) % this.banners.length;
            }
        },
        goToSlide(index) {
            this.currentSlide = index;
        },
        startAutoSlide() {
            this.stopAutoSlide(); // Останавливаем предыдущий интервал, если есть
            if (this.banners.length > 1) {
                this.autoSlideInterval = setInterval(() => {
                    this.nextSlide();
                }, 5000); // Автопрокрутка каждые 5 секунд
            }
        },
        stopAutoSlide() {
            if (this.autoSlideInterval) {
                clearInterval(this.autoSlideInterval);
                this.autoSlideInterval = null;
            }
        },
        copyPromocode() {
            if (this.dailyBonus && this.dailyBonus.code) {
                navigator.clipboard.writeText(this.dailyBonus.code).then(() => {
                    // Можно добавить уведомление об успешном копировании
                    this.$toastr.success(`Промокод ${this.dailyBonus.code} скопирован!`);
                }).catch(err => {
                    console.error("Failed to copy:", err);
                    this.$toastr.error("Не удалось скопировать промокод");
                });
            }
        },
        formatTimeUntil(validUntil) {
            if (!validUntil) return "";
            const now = new Date();
            const until = new Date(validUntil);
            const diff = until - now;
            
            if (diff <= 0) return "Истек";
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            return `Осталось: ${hours}ч ${minutes}м`;
        },
    },
};
</script>

<style scoped>
.global-carousel__control {
    cursor: pointer;
}

.global-carousel__dot {
    cursor: pointer;
}
</style>
