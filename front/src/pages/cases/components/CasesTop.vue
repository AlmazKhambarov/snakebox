<template>
    <div class="case__top">
        <LoadingSpinner v-if="isLoading" text="Загрузка кейса..." />
        <template v-else>
        <div class="case__top-inner">
            <div v-show="state !== 'opened'" class="case__slider multi">
                <div class="case__slider-multi">
                    <div v-for="(list, listIdx) in rouletteItems" :key="listIdx" class="multi-roulette-column">
                        <div v-show="state === 'default'" class="multi-roulette-overlay">
                            <img :src="box.image" alt="box" />
                        </div>
                        <div v-show="state !== 'default'" class="case__slider-cursor horizontal"></div>
                        <div class="multi-roulette-wrapp">
                            <div class="multi-roulette-inner" ref="rouletteList">
                                <div
                                    class="item vertical"
                                    v-for="(rouletteItem, idx) in list"
                                    :key="idx"
                                    :data-index="idx"
                                    :class="getItemRarityClass(rouletteItem.rarity)"
                                >
                                    <div class="item__inner">
                                        <div class="item__center">
                                            <img :src="rouletteItem.image" class="item__image" alt="skin" />
                                        </div>
                                    </div>
                                    <img :src="`/images/case/shadow-${getItemRarityClass(rouletteItem.rarity)}.webp`" class="item__rarity-img" alt="rarity" />
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
                    this.animateRoulette(25, duration);
                    
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
        initRoulette(count = 1) {
            if (!this.caseContent?.length) return;
            
            const generateList = () => Array.from({ length: 30 }, () => {
                const randomIndex = this.randomInteger(0, this.caseContent.length - 1);
                return this.caseContent[randomIndex];
            });

            if (count === 1) {
                this.rouletteItems = [generateList()];
            } else {
                this.rouletteItems = Array.from({ length: count }, () => generateList());
            }
        },
        setWinItems(winItems) {
            // winItems - это массив выпавших предметов
            this.rouletteItems = this.rouletteItems.map((list, listIdx) => {
                return list.map((item, i) => i === 25 ? winItems[listIdx] : item);
            });
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

            const lists = Array.isArray(this.$refs.rouletteList) 
                ? this.$refs.rouletteList 
                : [this.$refs.rouletteList];

            lists.forEach((list, listIdx) => {
                if (!list) return;

                const items = list.children;
                if (!items || !items.length) return;

                const winItem = items[winItemIndex];
                if (!winItem) return;

                // Vertical Logic (Unified for x1-x5)
                const cardHeight = winItem.offsetHeight;
                const containerHeight = list.parentElement.offsetHeight;
                const winItemOffset = winItem.offsetTop;

                const finalTarget = -(winItemOffset - (containerHeight / 2 - cardHeight / 2));
                const randomOffset = Math.floor(Math.random() * 40) - 20;
                const mainTarget = finalTarget + randomOffset;

                let prevIndex = -1;
                const tl = gsap.timeline({
                    delay: listIdx * 0.1, // Slight stagger for better feeling
                    onUpdate: () => {
                        const currentY = gsap.getProperty(list, "y");
                        const index = Math.floor(Math.abs(currentY) / cardHeight);
                        if (index !== prevIndex) {
                            prevIndex = index;
                            // Only play tick for the first column to avoid sound mess
                            if (listIdx === 0) this.playTick();
                        }
                    },
                });

                tl.to(list, {
                    y: mainTarget,
                    duration: duration,
                    ease: "power3.out",
                    force3D: true,
                }).to(list, {
                    y: finalTarget,
                    duration: 1,
                    ease: "power2.out",
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

/* Multi-Roulette Styles */
.case__slider.multi {
    height: 500px; /* Increased to fit 160px items */
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px;
    background: rgba(0, 0, 0, 0.15);
    border-radius: 20px;
    margin: 15px auto;
    max-width: 1400px; /* Increased to fit multiple 208px columns */
    position: relative;
    overflow: hidden;
}

.case__slider-multi {
    display: flex;
    gap: 12px;
    width: 100%;
    height: 100%;
    justify-content: center;
}

.multi-roulette-column {
    position: relative;
    flex: 0 0 208px; /* Fixed width as requested */
    height: 100%;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.multi-roulette-column:hover {
    background: rgba(255, 255, 255, 0.04);
}

.multi-roulette-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 5;
    background: rgba(20, 20, 27, 0.99);
    display: flex;
    justify-content: center;
    align-items: center;
    pointer-events: none;
}

.multi-roulette-overlay img {
    max-width: 80%; 
    max-height: 80%;
    object-fit: contain;
    filter: drop-shadow(0 0 15px rgba(0, 0, 0, 0.5));
}

.multi-roulette-wrapp {
    height: 100%;
    width: 100%;
    position: relative;
}

.multi-roulette-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    will-change: transform; /* Hardware acceleration */
    backface-visibility: hidden;
}

.case__win-items {
    display: flex;
    flex-wrap: nowrap;
    justify-content: center;
    gap: 20px;
    width: 100%;
    overflow-x: auto;
    padding: 30px 0;
}

.case__win-item {
    flex: 0 0 260px;
    min-width: 220px;
    transition: transform 0.3s ease;
}

.case__win-item:hover {
    transform: translateY(-5px);
}

.case__win-item .item {
    width: 100%;
}

.item.vertical {
    width: 100%;
    min-height: 160px; /* Fixed height as requested */
    height: 160px;
    margin: 3px 0;
    flex-shrink: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    transition: all 0.3s ease;
    transform: translateZ(0); /* Hardware acceleration */
    backface-visibility: hidden;
}

.item.vertical .item__inner {
    width: 90%;
    height: 90%;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.item.vertical .item__image {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

.case__slider-cursor.horizontal {
    width: 100%;
    height: 3px;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    background: var(--primary-color, #ff9900);
    box-shadow: 0 0 12px var(--primary-color, #ff9900);
    z-index: 10;
}

/* Mobile responsive for multi-roulette */
@media (max-width: 768px) {
    .case__slider.multi {
        height: 350px; /* Reduced for tablet */
        padding: 10px;
    }
    
    .case__slider-multi {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        padding: 0 5px;
        overflow-x: hidden; /* Prevent scroll, fit everything */
    }
    
    .multi-roulette-column {
        flex: 1 1 0; /* Equal width to fit all */
        min-width: 0;
        height: 100%;
    }

    .case__win-items {
        gap: 8px;
        padding: 10px 0;
    }

    .case__win-item {
        min-width: 120px;
        flex: 0 1 150px;
    }

    .item.vertical {
        min-height: 100px; /* Smaller for tablet */
        height: 100px;
    }

    .item.vertical .item__inner {
        width: 92%;
        height: 92%;
    }
}

@media (max-width: 480px) {
    .case__slider.multi {
        height: 250px; /* Compact for phones */
        border-radius: 12px;
    }

    .item.vertical {
        min-height: 60px; /* Even smaller for phones */
        height: 60px;
    }
    
    .case__win-items {
        gap: 5px;
        padding: 5px 0;
    }

    .case__win-item {
        min-width: 90px;
        flex: 0 1 110px;
    }

    .multi-roulette-overlay img {
        max-width: 70%;
    }

    .case__slider-multi {
        gap: 4px;
    }
}
</style>
