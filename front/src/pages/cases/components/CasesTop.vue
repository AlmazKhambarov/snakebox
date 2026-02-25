<template>
    <div class="case__top">
        <LoadingSpinner v-if="isLoading" text="Загрузка кейса..." />
        <template v-else>
        <div class="case__top-inner">
            <div
                v-show="state === 'default'"
                class="case__overlay"
                style="z-index: 5"
                x-ref="overlay"
            >
                <img
                    :src="box.image"
                    alt="Картинка кейса"
                    class="case__overlay-case"
                    loading="lazy"
                    decoding="async"
                />
            </div>
            <div v-show="state !== 'opened'" class="case__slider">
                <div class="case__slider-one" style="" x-ref="slider">
                    <div class="case__slider-cursor"></div>
                    <div class="case__slider-wrapp">
                        <div class="case__slider-outer">
                            <div class="case__slider-items" ref="rouletteList">
                                <div
                                    class="item"
                                    v-for="(rouletteItem, idx) in rouletteItems"
                                    :key="idx"
                                    :data-index="idx"
                                    :class="
                                        getItemRarityClass(rouletteItem.rarity)
                                    "
                                >
                                    <div class="item__inner">
                                        <div class="item__top"></div>
                                        <div class="item__center">
                                            <img
                                                :src="rouletteItem.image"
                                                class="item__image"
                                                alt="skin"
                                            />
                                            <div
                                                class="icon item__center-snake"
                                                style="
                                                    mask-image: url('/assets/icons/snake.svg');
                                                "
                                            ></div>
                                        </div>
                                        <div class="item__bottom">
                                            <div class="item__model">
                                                {{ rouletteItem.weapon }}
                                            </div>
                                            <div class="item__name">
                                                {{ rouletteItem.skin_name }}
                                            </div>
                                            <div class="item__quality">
                                                {{ rouletteItem.quality }}
                                            </div>
                                        </div>
                                    </div>
                                    <img
                                        :src="`/images/case/shadow-${getItemRarityClass(
                                            rouletteItem.rarity
                                        )}.webp`"
                                        class="item__rarity-img"
                                        alt="rarity"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-if="state === 'opened'"
                class="case__win-items"
                x-ref="winners"
                style=""
            >
                <div class="case__win-item">
                    <div class="item" v-if="winItems" :class="getItemRarityClass(winItems.rarity)">
                        <div class="item__inner">
                            <div class="item__top">
                                <div class="item__quality-top">
                                    {{ winItems.quality }}
                                </div>
                                <div class="sum sum--xs sum--bgWhite">
                                    <div
                                        class="icon"
                                        style="
                                            mask-image: url('/assets/icons/coin.svg');
                                        "
                                    ></div>
                                    {{ winItems.steam_price / 100 }}
                                </div>
                            </div>
                            <div class="item__center">
                                <img
                                    :src="winItems.image"
                                    class="item__image"
                                    alt="skin"
                                />
                                <div
                                    class="icon item__center-snake"
                                    style="
                                        mask-image: url('/assets/icons/snake.svg');
                                    "
                                ></div>
                            </div>
                            <div class="item__bottom">
                                <div class="item__model">
                                    {{ winItems.weapon }}
                                </div>
                                <div class="item__name">
                                    {{ winItems.skin_name }}
                                </div>
                            </div>
                        </div>
                        <img
                            :src="`/images/case/shadow-${getItemRarityClass(
                                winItems.rarity
                            )}.webp`"
                            class="item__rarity-img"
                            alt="rarity"
                        />
                    </div>
                    <button
                        @click="sellItem(winItems.id)"
                        type="button"
                        class="case__win-item-button"
                    >
                        <span>Продать за</span>
                        <div class="sum sum--xs sum--bgWhite">
                            <div
                                class="icon coin"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            {{ winItems.steam_price / 100 }}
                        </div>
                    </button>
                </div>
            </div>
        </div>
        </template>
        <div
            class="page__controls"
            :class="{ disabled: state !== 'default' || !isAuth }"
        >
            <div class="page__controls-left">
                <div class="page__controls-right-inner">
                    <div class="page__controls-right-text">
                        <div
                            class="icon"
                            style="mask-image: url('/images/icons/repeat.svg')"
                        ></div>
                        <span>ВКЛ / ВЫКЛ</span>
                    </div>
                    <div
                        class="checkbox demoOpen"
                        :class="{ active: demoOpen }"
                        @click="toggleDemo"
                    >
                        <div class="checkbox__button">
                            <div class="checkbox__button-el">
                                <div class="checkbox__button-el-inner"></div>
                            </div>
                            <div class="checkbox__button-desc">
                                <span>Демо</span>
                                <p>Открытие кейса</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page__controls-center">
                <a
                    v-if="!isAuth"
                    style="width: 100%"
                    href="#login"
                    data-fancybox=""
                    class="btn"
                    ><div class="btn__inner">
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
                    ></div
                ></a>
                <button
                    v-show="
                        state === 'default' &&
                        isAuth &&
                        (box.is_free || user.balance >= box.price || demoOpen)
                    "
                    @click="openBox()"
                    type="button"
                    class="btn page__controls-main-btn"
                >
                    <div class="btn__inner">
                        <div class="btn__inner-left">
                            <h2>Открыть кейс за</h2>
                        </div>
                        <div class="sum sum--sm">
                            <div
                                class="icon coin"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            <span
                                ><span>{{
                                    box.is_free ? "FREE" : box.price / 100
                                }}</span></span
                            >
                        </div>
                    </div>
                </button>
                <div
                    v-if="
                        user.balance < box.price &&
                        state === 'default' &&
                        isAuth &&
                        !demoOpen &&
                        !box.is_free
                    "
                    class="page__controls-main-btn case__controls-not-have"
                >
                    <div class="case__controls-not-have-top">
                        <p>На балансе</p>
                        <div class="sum">
                            <div
                                class="icon coin"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            <span>{{ user.balance / 100 }}</span>
                        </div>
                        <p>из</p>
                        <div class="sum">
                            <div
                                class="icon coin"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            <span>{{ box.price / 100 }}</span>
                        </div>
                    </div>
                    <div class="case__controls-not-have-bottom">
                        <p>Пополните ещё на</p>
                        <div class="sum">
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            <span>{{ (box.price - user.balance) / 100 }}</span>
                        </div>
                    </div>
                </div>
                <div
                    v-show="state === 'opening'"
                    class="btn btn--start page__controls-main-btn"
                >
                    <div class="btn__inner">
                        <div class="btn__inner-left">
                            <span>Открываем кейс...</span>
                            <p>Удачи! Надеюсь вам повезёт!</p>
                        </div>
                    </div>
                </div>
                <button
                    v-show="state === 'opened'"
                    @click="refresh"
                    type="button"
                    class="btn btn--start page__controls-main-btn"
                >
                    <div class="btn__inner">
                        <div class="btn__inner-left">
                            <span>Продолжить</span>
                            <p>Открыть повторно</p>
                        </div>
                    </div>
                </button>
            </div>
            <div class="page__controls-right">
                <div class="page__controls-right-inner">
                    <div class="page__controls-right-text">
                        <div
                            class="icon"
                            style="mask-image: url('/images/icons/energy.svg')"
                        ></div>
                        <span>ВКЛ / ВЫКЛ</span>
                    </div>
                    <div
                        class="checkbox fastOpen"
                        :class="{ active: fastOpen }"
                        @click="toggleFast"
                    >
                        <div class="checkbox__button">
                            <div class="checkbox__button-el">
                                <div class="checkbox__button-el-inner"></div>
                            </div>
                            <div class="checkbox__button-desc">
                                <span>Быстро</span>
                                <p>Открыть кейс</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { request } from "@/utils/request.js";
import { getItemRarityClass } from "../../../helpers/helpers";
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import gsap from "gsap";
import Cookies from "js-cookie";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

export default {
    props: {
        box: Object,
        caseContent: Array,
        isLoading: Boolean,
    },
    components: { LoadingSpinner },
    data() {
        return {
            state: "default", // default - перед открытием, Opening - во время открытия, Opened - после открытия
            speed: 1,

            rouletteItems: [],
            winItems: null,

            screenWidth: window.innerWidth,
            showWinNow: false,
            demoOpen: false,
            fastOpen: false,
        };
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
    },
    watch: {
        caseContent: {
            immediate: true,
            handler(val) {
                if (val?.length) {
                    this.initRoulette();
                }
            },
        },
    },
    mounted() {
        window.addEventListener("resize", this.updateWidth);
        this.tickSound = new Audio("/sounds/tick.mp3");
        const savedDemo = Cookies.get("demoOpen");
        if (savedDemo !== undefined) {
            this.demoOpen = savedDemo === "true";
        }
        const savedFast = Cookies.get("fastOpen");
        if (savedFast !== undefined) {
            this.fastOpen = savedFast === "true";
        }
    },
    beforeUnmount() {
        window.removeEventListener("resize", this.updateWidth);
    },
    methods: {
        playTick() {
            if (!this.tickSound) return;
            this.tickSound.currentTime = 0;
            this.tickSound
                .play()
                .then(() => {
                })
                .catch((err) => {
                    console.error("Не удалось воспроизвести звук:", err);
                });
        },
        toggleDemo() {
            this.demoOpen = !this.demoOpen;
            Cookies.set("demoOpen", this.demoOpen.toString());
            this.$playSound("/sounds/click.mp3");
        },
        toggleFast() {
            this.fastOpen = !this.fastOpen;
            Cookies.set("fastOpen", this.fastOpen.toString());
            this.$playSound("/sounds/click.mp3");
        },

        async openBox(type) {
            const openBox = await request("POST", "/case/open", {
                id: this.box.id,
                count: 1,
                demoOpen: this.demoOpen,
            }).then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                    return;
                }
                this.$playSound("/sounds/click.mp3");
                this.initRoulette();
                this.state = "opening";
                this.winItems = data.winItems[0];
                this.setWinItem(data.winItems[0]);

                this.$nextTick(() => {
                    const duration = this.fastOpen ? 2.5 : 8.5;
                    this.animateRoulette(35, duration);
                });

                this.box.is_free = false;

                setTimeout(
                    () => {
                        this.state = "opened";
                        this.$playSound("/sounds/contract-run.mp3");
                    },
                    this.fastOpen ? 2500 : 9500
                );

            });
        },
        randomInteger(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },
        initRoulette() {
            if (!this.caseContent?.length) {
                console.warn("caseContent пуст или не передан");
                return;
            }
            this.rouletteItems = Array.from({ length: 45 }, () => {
                const randomIndex = this.randomInteger(
                    0,
                    this.caseContent.length - 1
                );
                return this.caseContent[randomIndex];
            });
        },
        setWinItem(winItem) {
            this.rouletteItems = this.rouletteItems.map((item, i) =>
                i === 35 ? winItem : item
            );
        },

        resetTransform() {
            const list = this.$refs.rouletteList;
            if (!list) return;
            // Останавливаем все анимации для этого элемента
            gsap.killTweensOf(list);
            gsap.set(list, { x: 0 });
            this.showWinNow = false;
        },
        animateRoulette(winItemIndex, duration = 8.5) {
            this.resetTransform();

            const list = this.$refs.rouletteList;
            if (!list) return;

            // Дополнительная проверка остановки анимаций
            gsap.killTweensOf(list);

            const items = list.children;
            if (!items || !items.length) return;

            const winItem = items[winItemIndex];
            if (!winItem) return;

            const cardWidth = winItem.offsetWidth;
            const containerWidth = list.parentElement.offsetWidth;
            const winItemOffset = winItem.offsetLeft;

            const finalTarget = -(
                winItemOffset -
                (containerWidth / 2 - cardWidth / 2)
            );

            const randomOffset = Math.floor(Math.random() * 60) - 30;
            const mainTarget = finalTarget + randomOffset;

            let prevIndex = -1;

            const tl = gsap.timeline({
                onUpdate: () => {
                    const currentX = gsap.getProperty(list, "x");
                    const index = Math.floor(Math.abs(currentX) / cardWidth);

                    if (index !== prevIndex) {
                        prevIndex = index;
                        this.playTick();
                    }
                },
            });

            tl.to(list, {
                x: mainTarget,
                duration: duration,
                ease: "power2.out",
                force3D: true,
            });

            tl.to(list, {
                x: finalTarget,
                duration: 1,
                ease: "power2.out",
            });
        },
        toggleCookie(key) {
            this[key] = !this[key];
            Cookies.set(key, this[key].toString());
        },
        refresh() {
            this.state = "default";
            this.rouletteItems = [];
            this.winItems = [];
            this.showWinNow = false;
            this.initRoulette();
        },
        async sellItem(liveId) {
            await request("POST", "/case/sell/item", {
                liveId: liveId,
            }).then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                    return;
                } else {
                    this.$toastr.success(data.message);
                    this.refresh();
                }
            });
        },
        updateWidth() {
            this.screenWidth = window.innerWidth;
        },

        getItemRarityClass,
    },
};
</script>
