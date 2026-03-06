<template>
    <div class="case__top">
        <LoadingSpinner v-if="isLoading" text="Загрузка кейса..." />
        <template v-else>
        <div class="case__top-inner">
            <div v-show="state !== 'opened'" class="case__slider multi">
                <div class="case__slider-multi">
                    <div class="multi-roulette-column single-drum">
                        <!-- Background Decoration -->
                        <div class="multi-roulette-bg-image">
                            <img :src="box.image" alt="box-bg" />
                        </div>
                        
                        <!-- Fixed Center Case (Overlay when not opening) -->
                        <div v-show="state === 'default'" class="multi-roulette-overlay">
                            <img :src="box.image" alt="box" />
                        </div>

                        <!-- Side Faders -->
                        <div class="multi-roulette-fader left"></div>
                        <div class="multi-roulette-fader right"></div>

                        <!-- Current Item Glow -->
                        <div class="multi-roulette-glow" v-show="state === 'opening'"></div>
                        
                        <div v-show="state === 'opening'" class="case__slider-cursor vertical">
                            <div class="cursor-light"></div>
                        </div>

                        <div class="multi-roulette-wrapp">
                            <div class="multi-roulette-inner horizontal" ref="rouletteList" v-if="rouletteItems[0]">
                                <div
                                    class="item horizontal"
                                    v-for="(rouletteItem, idx) in rouletteItems[0]"
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
                class="case__win-items"
                x-ref="winners"
            >
                <div class="case__win-item" v-for="(winItem, index) in winItems" :key="index">
                    <div class="item" :class="getItemRarityClass(winItem.rarity)">
                        <div class="item__inner">
                            <div class="item__top">
                                <div class="item__quality-top">
                                    {{ winItem.quality }}
                                </div>
                                <div class="sum sum--xs sum--bgWhite">
                                    <div
                                        class="icon"
                                        style="
                                            mask-image: url('/assets/icons/coin.svg');
                                        "
                                    ></div>
                                    {{ winItem.steam_price / 100 }}
                                </div>
                            </div>
                            <div class="item__center">
                                <img
                                    :src="winItem.image"
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
                                    {{ winItem.weapon }}
                                </div>
                                <div class="item__name">
                                    {{ winItem.skin_name }}
                                </div>
                            </div>
                        </div>
                        <img
                            :src="`/images/case/shadow-${getItemRarityClass(
                                winItem.rarity
                            )}.webp`"
                            class="item__rarity-img"
                            alt="rarity"
                        />
                    </div>
                    <button
                        @click="sellItem(winItem.id)"
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
                            {{ winItem.steam_price / 100 }}
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

            rouletteItems: [], // Будет массивом массивов: [[item, item...], [item, item...]]
            winItems: [],

            screenWidth: window.innerWidth,
            showWinNow: false,
            demoOpen: false,
            fastOpen: false,
            selectedCount: 1,
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
                    const duration = this.fastOpen ? 2 : 8;
                    this.animateRoulette(18, duration);
                    
                    setTimeout(() => {
                        this.state = "opened";
                        this.$playSound("/sounds/contract-run.mp3");
                    }, duration * 1000 + 500);
                });

                this.box.is_free = false;
            });
        },
        randomInteger(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },
        initRoulette() {
            if (!this.caseContent?.length) return;
            
            const generateList = () => Array.from({ length: 22 }, () => {
                const randomIndex = this.randomInteger(0, this.caseContent.length - 1);
                return this.caseContent[randomIndex];
            });

            this.rouletteItems = [generateList()];
        },
        setWinItems(winItems) {
            // winItems - это массив выпавших предметов
            // Для одиночного барабана показываем первый предмет из списка
            if (this.rouletteItems[0]) {
                this.rouletteItems[0] = this.rouletteItems[0].map((item, i) => i === 18 ? winItems[0] : item);
            }
        },

        resetTransform() {
            const lists = Array.isArray(this.$refs.rouletteList) 
                ? this.$refs.rouletteList 
                : [this.$refs.rouletteList];
                
            lists.forEach(list => {
                if (!list) return;
                gsap.killTweensOf(list);
                gsap.set(list, { x: 0, y: 0 });
            });
            this.showWinNow = false;
        },
        animateRoulette(winItemIndex, duration = 8.5) {
            this.resetTransform();

            const list = this.$refs.rouletteList;
            if (!list) return;

            const items = list.children;
            if (!items || !items.length) return;

            const winItem = items[winItemIndex];
            if (!winItem) return;

            // Horizontal Logic 
            const cardWidth = winItem.offsetWidth;
            const containerWidth = list.parentElement.offsetWidth;
            const winItemOffset = winItem.offsetLeft;

            const finalTarget = -(winItemOffset - (containerWidth / 2 - cardWidth / 2));
            const randomOffset = Math.floor(Math.random() * 40) - 20;
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
                ease: "power3.out",
                force3D: true, // Hardware acceleration
            }).to(list, {
                x: finalTarget,
                duration: 1,
                ease: "power2.out",
                force3D: true,
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
        updateWidth() {
            this.screenWidth = window.innerWidth;
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
    height: 380px; 
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

.case__slider-multi {
    display: flex;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.multi-roulette-column {
    position: relative;
    width: 100%;
    height: 100%;
    background: transparent;
    overflow: hidden;
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
    will-change: transform;
    backface-visibility: hidden;
}

.item.horizontal {
    flex: 0 0 180px; 
    height: 240px;   
    margin: 0 10px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    position: relative;
    transform: translateZ(0);
    backface-visibility: hidden;
    padding: 15px;
}

.item.horizontal .item__inner {
    width: 100%;
    height: 180px;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 16px;
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
    background: rgba(255, 184, 0, 0.8);
    box-shadow: 0 0 15px rgba(255, 184, 0, 0.5);
    z-index: 20;
}

.cursor-light {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 12px;
    height: 40px;
    background: linear-gradient(to bottom, #ffb800, transparent);
    filter: blur(4px);
}

.case__win-items {
    display: flex;
    flex-wrap: nowrap;
    justify-content: center;
    gap: 15px;
    width: 100%;
    overflow-x: auto;
    padding: 30px 0;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .case__slider.multi {
        height: 300px;
        max-width: 95%;
    }
    
    .item.horizontal {
        flex: 0 0 140px;
        height: 200px;
        padding: 10px;
    }

    .item.horizontal .item__inner {
        height: 140px;
    }

    .multi-roulette-overlay img {
        max-width: 180px;
    }
}

@media (max-width: 480px) {
    .case__slider.multi {
        height: 240px;
    }

    .item.horizontal {
        flex: 0 0 110px;
        height: 160px;
    }

    .item.horizontal .item__inner {
        height: 110px;
        border-radius: 12px;
    }

    .item__name { font-size: 11px; }
    .item__subname { font-size: 9px; }

    .multi-roulette-overlay img {
        max-width: 140px;
    }
}
</style>
