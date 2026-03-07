<template>
    <div class="case__top">
        <LoadingSpinner v-if="isLoading" text="Загрузка кейса..." />
        <template v-else>
        <div class="case__top-inner">
            <div v-show="state !== 'opened'" class="case__slider multi" :class="{ 'multi-drums-active': state === 'opening' && rouletteItems.length > 1 }">
                <div class="case__slider-multi">
                    <!-- Background Decoration -->
                    <div class="multi-roulette-bg-image">
                        <img :src="box.image" alt="box-bg" />
                    </div>
                    
                    <!-- Fixed Center Case (Overlay when not opening) -->
                    <div v-if="state === 'default'" class="multi-roulette-overlay">
                        <img :src="box.image" alt="box" />
                    </div>

                    <!-- Shared Global Elements (Faders, Glow, Cursor) -->
                    <div class="multi-roulette-fader left"></div>
                    <div class="multi-roulette-fader right"></div>
                    <div class="multi-roulette-glow" v-show="state === 'opening'"></div>
                    <div v-show="state === 'opening'" class="case__slider-cursor vertical">
                        <div class="cursor-light"></div>
                    </div>

                    <div v-for="(list, listIdx) in rouletteItems" :key="listIdx" class="multi-roulette-column single-drum">
                        <div class="multi-roulette-wrapp">
                            <div class="multi-roulette-inner horizontal" :ref="el => { if (el) drums[listIdx] = el }">
                                <div
                                    class="item horizontal"
                                    v-for="(rouletteItem, idx) in list"
                                    :key="idx"
                                    :class="getItemRarityClass(rouletteItem.rarity)"
                                >
                                    <div class="item__inner">
                                        <div class="item__center">
                                            <img :src="rouletteItem.image" class="item__image" alt="skin" />
                                        </div>
                                    </div>
                                    <div class="item__info">
                                        <div class="item__name">{{ rouletteItem.name }}</div>
                                        <div class="item__subname">{{ rouletteItem.subname }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-if="state === 'opened'"
                class="case__win-modal"
            >
                <div class="case__win-modal-overlay" @click="refresh"></div>
                <div class="case__win-modal-content">
                    <div class="case__win-total">
                        <span>Общий выигрыш:</span>
                        <div class="sum sum--sm">
                            <div
                                class="icon coin"
                                style="mask-image: url('/assets/icons/coin.svg');"
                            ></div>
                            <span>{{ totalWinSum }}</span>
                        </div>
                    </div>
                    <div class="case__win-items-row">
                        <div class="case__win-item-compact" v-for="(winItem, index) in winItems" :key="index">
                            <div class="item-compact" :class="getItemRarityClass(winItem.rarity)">
                                <div class="item-compact__price">
                                    <div class="sum sum--xs sum--bgWhite">
                                        <div
                                            class="icon"
                                            style="mask-image: url('/assets/icons/coin.svg');"
                                        ></div>
                                        {{ winItem.steam_price / 100 }}
                                    </div>
                                </div>
                                <div class="item-compact__image">
                                    <img :src="winItem.image" alt="skin" />
                                </div>
                                <div class="item-compact__name">
                                    <span class="item-compact__weapon">{{ winItem.weapon }}</span>
                                    <span class="item-compact__skin">{{ winItem.skin_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="case__win-modal-footer">
                        <button
                            @click="refresh"
                            type="button"
                            class="btn btn--start case__win-footer-btn"
                        >
                            <div class="btn__inner">
                                <div class="btn__inner-left">
                                    <span>Открыть ещё</span>
                                </div>
                            </div>
                        </button>
                        <button
                            @click="sellAll"
                            type="button"
                            class="btn btn--sell case__win-footer-btn"
                        >
                            <div class="btn__inner">
                                <div class="btn__inner-left">
                                    <span>Продать за</span>
                                    <div class="sum sum--xs">
                                        <div
                                            class="icon coin"
                                            style="mask-image: url('/assets/icons/coin.svg');"
                                        ></div>
                                        {{ totalWinSum }}
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
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
                <div v-if="state === 'default' && isAuth && !box.is_free" class="case__multiplier-selector">
                    <button 
                        v-for="n in 5" 
                        :key="n"
                        @click="selectedCount = n"
                        class="multiplier-btn"
                        :class="{ active: selectedCount === n }"
                    >
                        x{{ n }}
                    </button>
                </div>
                <button
                    v-show="
                        state === 'default' &&
                        isAuth &&
                        (box.is_free || user.balance >= box.price * selectedCount || demoOpen)
                    "
                    @click="openBox(box.is_free ? 1 : selectedCount)"
                    type="button"
                    class="btn page__controls-main-btn"
                >
                    <div class="btn__inner">
                        <div class="btn__inner-left">
                            <h2>{{ box.is_free ? 'Открыть бесплатно' : `Открыть за` }}</h2>
                        </div>
                        <div v-if="!box.is_free" class="sum sum--sm">
                            <div
                                class="icon coin"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            <span
                                ><span>{{ (box.price * selectedCount) / 100 }}</span></span
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

            rouletteItems: [], // Будет массивом массивов: [[item, item...], [item, item...]]
            winItems: [],

            demoOpen: false,
            fastOpen: false,
            selectedCount: 1,
            drums: [], // Навигация по барабанам по индексу (рефы)
            isMobile: window.innerWidth <= 768,
        };
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
        totalWinSum() {
            return (this.winItems.reduce((sum, item) => sum + (item.steam_price || 0), 0) / 100).toFixed(2);
        },
    },
    watch: {
        caseContent: {
            immediate: true,
            handler(val) {
                if (val?.length) {
                    this.initRoulette(this.selectedCount);
                }
            },
        },
        selectedCount(val) {
            this.initRoulette(val);
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

        async openBox(count = 1) {
            await request("POST", "/case/open", {
                id: this.box.id,
                count: count,
                demoOpen: this.demoOpen,
            }).then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                    return;
                }
                this.$playSound("/sounds/click.mp3");
                this.initRoulette(count);
                this.state = "opening";
                this.winItems = data.winItems;
                
                // Показываем в рулетках выпавшие предметы
                this.setWinItems(data.winItems);

                this.$nextTick(() => {
                    // Даём DOM время отрисовать барабаны с правильными размерами
                    setTimeout(() => {
                        const baseDuration = this.fastOpen ? 2.5 : (this.isMobile ? 6 : 10);
                        const drumDelay = this.fastOpen ? 0.5 : (this.isMobile ? 1 : 1.5);
                        const drumCount = this.rouletteItems.length;
                        const winIdx = this.isMobile ? 25 : 40;
                        this.animateRoulette(winIdx, baseDuration, drumDelay);
                        
                        const totalDuration = baseDuration + drumDelay * (drumCount - 1) + 1.5;
                        setTimeout(() => {
                            this.state = "opened";
                            this.$playSound("/sounds/contract-run.mp3");
                        }, totalDuration * 1000);
                    }, 100);
                });

                this.box.is_free = false;
            });
        },
        randomInteger(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },
        initRoulette(count = 1) {
            if (!this.caseContent?.length) return;
            
            const itemCount = this.isMobile ? 30 : 50;
            const generateList = () => Array.from({ length: itemCount }, () => {
                const randomIndex = this.randomInteger(0, this.caseContent.length - 1);
                return this.caseContent[randomIndex];
            });

            this.rouletteItems = Array.from({ length: Math.max(1, count) }, () => generateList());
        },
        setWinItems(winItems) {
            // winItems - это массив выпавших предметов
            winItems.forEach((winItem, listIdx) => {
                if (this.rouletteItems[listIdx]) {
                    const winIdx = this.isMobile ? 25 : 40;
                    this.rouletteItems[listIdx] = this.rouletteItems[listIdx].map((item, i) => i === winIdx ? winItem : item);
                }
            });
        },

        resetTransform() {
            this.drums.forEach(list => {
                if (!list) return;
                gsap.killTweensOf(list);
                gsap.set(list, { x: 0, y: 0 });
            });
            this.showWinNow = false;
        },
        animateRoulette(winItemIndex, duration = 8.5, drumDelay = 1.5) {
            this.resetTransform();

            this.drums.forEach((list, listIdx) => {
                if (!list || listIdx >= this.rouletteItems.length) return;

                const items = list.children;
                if (!items || !items.length) return;

                const winItem = items[winItemIndex];
                if (!winItem) return;

                // Horizontal Logic 
                const cardWidth = winItem.offsetWidth || 120;
                const containerWidth = list.parentElement.offsetWidth;
                const winItemOffset = winItem.offsetLeft;

                const finalTarget = -(winItemOffset - (containerWidth / 2 - cardWidth / 2));
                const randomOffset = Math.floor(Math.random() * 40) - 20;
                const mainTarget = finalTarget + randomOffset;

                // Каждый следующий барабан останавливается позже
                const thisDuration = duration + (drumDelay * listIdx);

                let prevIndex = -1;
                const playSound = !this.isMobile; // Отключаем тик на мобилке для производительности
                const tl = gsap.timeline({
                    onUpdate: playSound ? () => {
                        const currentX = gsap.getProperty(list, "x");
                        const index = Math.floor(Math.abs(currentX) / cardWidth);
                        if (index !== prevIndex) {
                            prevIndex = index;
                            if (listIdx === 0) this.playTick();
                        }
                    } : undefined,
                });

                tl.to(list, {
                    x: mainTarget,
                    duration: thisDuration,
                    ease: "power3.out",
                    force3D: false,
                }).to(list, {
                    x: finalTarget,
                    duration: 1,
                    ease: "power2.out",
                    force3D: false,
                });
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
            this.drums = [];
            this.initRoulette(this.selectedCount);
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
                    // Вместо полной перезагрузки удаляем проданный предмет из списка
                    this.winItems = this.winItems.filter(item => item.id !== liveId);
                    // Если предметов больше нет, возвращаемся в начальное состояние
                    if (this.winItems.length === 0) {
                        this.refresh();
                    }
                }
            });
        },
        async sellAll() {
            const itemsToSell = this.winItems.filter(item => item.id);
            if (itemsToSell.length === 0) {
                this.$toastr.error('Нет предметов для продажи (демо режим)');
                return;
            }
            for (const item of itemsToSell) {
                await request("POST", "/case/sell/item", {
                    liveId: item.id,
                }).then(({ data }) => {
                    if (data.success) {
                        this.winItems = this.winItems.filter(w => w.id !== item.id);
                    }
                });
            }
            if (this.winItems.length === 0 || !this.winItems.some(w => w.id)) {
                this.$toastr.success('Все предметы проданы!');
                this.refresh();
            }
        },
        updateWidth() {
            this.screenWidth = window.innerWidth;
            this.isMobile = window.innerWidth <= 768;
        },

        getItemRarityClass,
    },
};
</script>
<style scoped>
.case__multiplier-selector {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
    background: rgba(255, 255, 255, 0.05);
    padding: 6px;
    border-radius: 12px;
}

.multiplier-btn {
    padding: 10px 20px;
    border-radius: 8px;
    background: transparent;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    font-weight: bold;
    font-size: 16px;
    min-width: 60px;
}

.multiplier-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.multiplier-btn.active {
    background: var(--primary-color, #ff9900);
    border-color: var(--primary-color, #ff9900);
    color: #000;
}

.page__controls-main-btn {
    width: 100%;
}

/* Multi-Roulette Styles (Horizontal Drum Premium) */
.case__slider.multi {
    height: 260px; 
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0;
    background: rgba(13, 13, 18, 0.4);
    border-radius: 20px;
    margin: 20px auto;
    width: 100%;
    max-width: 1200px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.03);
}

.case__slider.multi.multi-drums-active {
    height: auto !important;
    max-height: 85vh;
    padding: 5px 0;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0;
}

.case__slider-multi {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0;
    width: 100%;
    height: 100%;
}

.case__slider.multi.multi-drums-active .case__slider-multi {
    height: auto;
}

.multi-roulette-column {
    position: relative;
    width: 100%;
    height: 160px;
    flex: 0 0 160px;
    background: transparent;
    overflow: hidden;
}

.case__slider.multi.multi-drums-active .multi-roulette-column {
    flex: 0 0 100px; 
    height: 100px;
}

.case__slider.multi.multi-drums-active .item.horizontal {
    height: 100px;
    flex: 0 0 90px;
    padding: 2px;
}

.case__slider.multi.multi-drums-active .item.horizontal .item__inner {
    height: 65px;
}

.case__slider.multi.multi-drums-active .item__info {
    margin-top: 2px;
}

.case__slider.multi.multi-drums-active .item__name {
    font-size: 9px;
}

.case__slider.multi.multi-drums-active .item__subname {
    font-size: 7px;
}

.multi-roulette-bg-image {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1;
    opacity: 0.15;
    filter: blur(40px);
    pointer-events: none;
}

.multi-roulette-bg-image img {
    max-width: 80%;
    max-height: 80%;
    object-fit: contain;
}

.multi-roulette-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 15;
    background: rgba(13, 13, 18, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    pointer-events: none;
}

.multi-roulette-overlay img {
    max-width: 280px; 
    max-height: 80%;
    object-fit: contain;
    filter: drop-shadow(0 0 30px rgba(0, 0, 0, 0.8));
    z-index: 20;
}

.multi-roulette-fader {
    position: absolute;
    top: 0;
    width: 25%;
    height: 100%;
    z-index: 10;
    pointer-events: none;
}

.multi-roulette-fader.left {
    left: 0;
    background: linear-gradient(to right, #0d0d12 0%, transparent 100%);
}

.multi-roulette-fader.right {
    right: 0;
    background: linear-gradient(to left, #0d0d12 0%, transparent 100%);
}

.multi-roulette-glow {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 184, 0, 0.15) 0%, transparent 70%);
    z-index: 2;
    pointer-events: none;
}

.multi-roulette-wrapp {
    height: 100%;
    width: 100%;
    position: relative;
    display: flex;
    align-items: center;
    z-index: 5;
    /* Apply mask for item fading */
    mask-image: linear-gradient(to right, transparent, black 25%, black 75%, transparent);
}

.multi-roulette-inner.horizontal {
    display: flex;
    flex-direction: row; 
    align-items: center;
    height: 100%;
    backface-visibility: hidden;
}

.item.horizontal {
    flex: 0 0 120px; 
    height: 160px;   
    margin: 0 4px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    position: relative;
    transform: translateZ(0);
    backface-visibility: hidden;
    padding: 8px;
}

.item.horizontal .item__inner {
    width: 100%;
    height: 110px;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background 0.3s ease;
}

.item.horizontal:hover .item__inner {
    background: rgba(255, 255, 255, 0.07);
}

.item.horizontal .item__image {
    max-width: 80%;
    max-height: 80%;
    object-fit: contain;
    filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
}

.item__info {
    margin-top: 12px;
    text-align: center;
    width: 100%;
}

.item__name {
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.item__subname {
    color: rgba(255, 255, 255, 0.4);
    font-size: 11px;
    margin-top: 2px;
}

.case__slider-cursor.vertical {
    position: absolute;
    width: 2px;
    height: 100%;
    left: 50%;
    top: 0;
    transform: translateX(-50%);
    background: rgba(255, 184, 0, 0.4);
    box-shadow: 0 0 15px rgba(255, 184, 0, 0.2);
    z-index: 20;
    pointer-events: none;
}

.case__slider-cursor.vertical::before,
.case__slider-cursor.vertical::after {
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    z-index: 25;
}

.case__slider-cursor.vertical::before {
    top: -2px;
    border-top: 12px solid #ffb800;
}

.case__slider-cursor.vertical::after {
    bottom: -2px;
    border-bottom: 12px solid #ffb800;
}

.cursor-light {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 100%;
    background: radial-gradient(ellipse at center, rgba(255, 184, 0, 0.15) 0%, transparent 70%);
    filter: blur(8px);
}

.case__win-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.case__win-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(8px);
}

.case__win-modal-content {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 700px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(13, 13, 18, 0.95);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 16px;
}

.case__win-total {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
}

.case__win-items-row {
    display: flex;
    justify-content: center;
    gap: 6px;
    width: 100%;
    flex-wrap: nowrap;
}

.case__win-item-compact {
    flex: 1;
    min-width: 0;
    max-width: 130px;
}

.item-compact {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    padding: 6px;
    gap: 4px;
}

.item-compact__price {
    width: 100%;
    display: flex;
    justify-content: center;
}

.item-compact__price .sum {
    font-size: 11px;
}

.item-compact__image {
    width: 100%;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.item-compact__image img {
    max-width: 85%;
    max-height: 85%;
    object-fit: contain;
    filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.3));
}

.item-compact__name {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    overflow: hidden;
}

.item-compact__weapon {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.5);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.item-compact__skin {
    font-size: 11px;
    color: #fff;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.case__win-modal-footer {
    margin-top: 14px;
    width: 100%;
    display: flex;
    gap: 8px;
    justify-content: center;
}

.case__win-footer-btn {
    flex: 1;
    max-width: 250px;
}

.btn--sell {
    background: linear-gradient(135deg, #ff9900, #ff6600) !important;
}

.btn--sell .btn__inner-left {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .case__slider.multi {
        height: 200px;
        max-width: 95%;
    }

    .multi-roulette-column {
        height: 130px;
        flex: 0 0 130px;
    }
    
    .case__slider.multi.multi-drums-active .multi-roulette-column {
        flex: 0 0 80px; 
        height: 80px;
    }

    .case__win-modal {
        padding: 10px;
    }

    .case__win-modal-content {
        padding: 10px;
    }

    .case__win-items-row {
        gap: 4px;
    }

    .item-compact {
        padding: 4px;
    }

    .item-compact__weapon { font-size: 8px; }
    .item-compact__skin { font-size: 9px; }
    .item-compact__price .sum { font-size: 9px; }

    .case__win-total {
        font-size: 14px;
    }

    .case__win-modal-footer {
        flex-direction: column;
        gap: 6px;
    }

    .case__win-footer-btn {
        max-width: 100%;
        padding: 8px;
        font-size: 16px;
    }

    .case__win-footer-btn .btn__inner {
        padding: 6px 16px;
    }
    
    .item.horizontal {
        flex: 0 0 100px;
        height: 130px;
        padding: 6px;
    }

    .item.horizontal .item__inner {
        height: 90px;
        border-radius: 10px;
    }

    .multi-roulette-overlay img {
        max-width: 160px;
    }
}

@media (max-width: 480px) {
    .case__slider.multi {
        height: 160px;
    }

    .multi-roulette-column {
        height: 110px;
        flex: 0 0 110px;
    }

    .case__slider.multi.multi-drums-active .multi-roulette-column {
        flex: 0 0 70px; 
        height: 70px;
    }

    .case__slider.multi.multi-drums-active .item.horizontal {
        height: 70px;
        flex: 0 0 60px;
    }

    .case__slider.multi.multi-drums-active .item.horizontal .item__inner {
        height: 45px;
        border-radius: 8px;
    }

    .item.horizontal {
        flex: 0 0 80px;
        height: 110px;
        padding: 4px;
    }

    .item.horizontal .item__inner {
        height: 70px;
        border-radius: 8px;
    }

    .item__name { font-size: 10px; }
    .item__subname { font-size: 8px; }

    .multi-roulette-overlay img {
        max-width: 120px;
    }
}
</style>
