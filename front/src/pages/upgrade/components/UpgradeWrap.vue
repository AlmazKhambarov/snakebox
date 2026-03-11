<template>
    <div class="upgrade__wrapp">
        <div class="upgrade__item left">
            <div class="upgrade__item-skin" v-if="selectedUserItem">
                <div class="item__top">
                    <div class="item__quality-top">
                        {{ selectedUserItem.item.quality }}
                    </div>
                    <div class="sum sum--xs sum--bgWhite itemPrice">
                        <div
                            class="icon"
                            style="mask-image: url('images/icons/coin.svg')"
                        >
                            <div class="icon__wrapp"></div>
                        </div>
                        {{ selectedUserItem.price / 100 }}
                    </div>
                </div>
                <div class="item__center">
                    <img
                        :src="selectedUserItem.item.image"
                        class="item__image"
                        alt="Нож Карамбит для апгрейда"
                    />
                    <img
                        :src="`/images/case/shadow-${getItemRarityClass(
                            selectedUserItem.item.rarity
                        )}-circle.png`"
                        class="item__rarity-img"
                        alt="rarity circle"
                    />
                    <div
                        class="icon item__center-snake"
                        style="mask-image: url('/assets/icons/snake.svg')"
                    ></div>
                </div>
                <div class="item__bottom">
                    <div class="item__model">
                        {{ selectedUserItem.item.weapon }}
                    </div>
                    <div class="item__name">
                        {{ selectedUserItem.item.skin_name }}
                    </div>
                </div>
            </div>
            <div class="upgrade__item-skin upgrade__balance-display" v-else-if="isBalanceMode && balanceAmount > 0">
                <div class="item__top">
                    <div class="sum sum--xs sum--bgWhite itemPrice">
                        <div
                            class="icon"
                            style="mask-image: url('images/icons/coin.svg')"
                        >
                            <div class="icon__wrapp"></div>
                        </div>
                        {{ balanceAmount }}
                    </div>
                </div>
                <div class="item__center">
                    <div class="upgrade__balance-icon">
                        <div
                            class="icon"
                            style="mask-image: url('images/icons/coin.svg')"
                        ></div>
                    </div>
                </div>
                <div class="item__bottom">
                    <div class="item__model">Баланс</div>
                    <div class="item__name">{{ balanceAmount }} монет</div>
                </div>
            </div>
            <div class="upgrade__item-overlay" v-show="!hasUserInput">
                <div class="empty">
                    <div
                        class="icon"
                        style="mask-image: url('images/icons/skins.svg')"
                    ></div>
                    <span>Выберите предмет слева</span>
                </div>
            </div>
            <img src="/images/upgrade-bg.svg" alt="" />
        </div>
        <div class="upgrade__center">
            <div class="upgrade__center-wrapp">
                <img
                    src="/images/cursor.png"
                    class="upgrade__cursor"
                    v-show="selectedSiteItem && hasUserInput"
                />
                <div
                    class="upgrade__center-progress"
                    id="progress"
                    v-if="selectedSiteItem && hasUserInput"
                >
                    <svg
                        viewBox="0 0 100 100"
                        :style="{
                            transition:
                                'transform 5s cubic-bezier(0.1, 0.9, 0.05, 1)',
                            transform: `rotate(${countTurnover}deg)`,
                        }"
                    >
                        <path
                            d="M 50,50 m 0,-47 a 47,47 0 1 1 0,94 a 47,47 0 1 1 0,-94"
                            stroke="#A0BDA6"
                            stroke-width="6"
                            fill="none"
                            :style="{
                                strokeDasharray: circumference,
                                strokeDashoffset:
                                    circumference -
                                    (percent / 100) * circumference,
                            }"
                        ></path>
                    </svg>
                </div>

                <div class="upgrade__center-start">
                    <div
                        class="upgrade__center-chances"
                        v-show="selectedSiteItem && hasUserInput"
                    >
                        {{ percent.toFixed(2) }}%
                    </div>
                    <img
                        v-show="!(selectedSiteItem && hasUserInput)"
                        src="/assets/icons/logo.png"
                        class="upgrade__center-logo"
                        alt=""
                    />
                    <img
                        src="/assets/images/upgrade-sphere.png"
                        alt=""
                        class="upgrade__center-bg upgrade__sphere"
                    />
                </div>
                <img
                    src="/assets/images/upgrade-circle.svg"
                    alt=""
                    class="upgrade__center-bg"
                />
            </div>
        </div>
        <div class="upgrade__item right">
            <div class="upgrade__item-skin" v-if="selectedSiteItem">
                <div class="item__top">
                    <div class="item__quality-top">
                        {{ selectedSiteItem.quality }}
                    </div>
                    <div class="sum sum--xs sum--bgWhite itemPrice">
                        <div
                            class="icon"
                            style="mask-image: url('/assets/icons/coin.svg')"
                        ></div>
                        {{ selectedSiteItem.steam_price / 100 }}
                    </div>
                </div>
                <div class="item__center">
                    <img
                        :src="selectedSiteItem.image"
                        class="item__image"
                        alt="Предмет для апгрейда"
                    />
                    <img
                        :src="`/images/case/shadow-${getItemRarityClass(
                            selectedSiteItem.rarity
                        )}-circle.png`"
                        class="item__rarity-img"
                        alt="rarity circle"
                    />

                    <div
                        class="icon item__center-snake"
                        style="mask-image: url('/assets/icons/snake.svg')"
                    ></div>
                </div>
                <div class="item__bottom">
                    <div class="item__model">{{ selectedSiteItem.weapon }}</div>
                    <div class="item__name">
                        {{ selectedSiteItem.skin_name }}
                    </div>
                    <!-- <div class="item__quality"></div> -->
                </div>
            </div>
            <div class="upgrade__item-overlay" v-show="!selectedSiteItem">
                <div class="empty">
                    <div
                        class="icon"
                        style="mask-image: url('images/icons/skins.svg')"
                    ></div>
                    <span>Выберите предмет справа</span>
                </div>
            </div>
            <img
                src="/images/upgrade-bg.svg"
                style="transform: scale(-1, 1)"
                alt=""
            />
        </div>
    </div>
</template>

<script>
import { getItemRarityClass } from "../../../helpers/helpers";
export default {
    props: {
        selectedSiteItem: Object,
        selectedUserItem: Object,
        percent: Number,
        state: String,
        refresh: Function,
        isBalanceMode: Boolean,
        balanceAmount: Number,
    },
    data() {
        return {
            circumference: 2 * Math.PI * 47,
            countTurnover: 0,
            innerState: "default",
        };
    },
    computed: {
        hasUserInput() {
            return this.selectedUserItem || (this.isBalanceMode && this.balanceAmount > 0);
        },
        percentInDeg() {
            return (this.percent / 100) * 360;
        },
    },
    watch: {
        state(newState) {
            const fullRotations = Math.floor(this.random(4, 6)) * 360; // 4-6 полных оборотов
            const arrowDeg = 0; // стрелка в 0°
            const filledSegmentDeg = (this.percent / 100) * 360; // угол залитого сегмента

          

            if (newState === "win") {
                // Выигрыш: стрелка указывает на залитый сегмент
                const targetDeg = this.random(-filledSegmentDeg + 5, -5);
                this.countTurnover = fullRotations + targetDeg;
              
                setTimeout(() => {
                    this.innerState = "win";
                    this.$emit("finish", this.innerState);
                    this.$playSound("/sounds/upgrade-result-win.mp3");
                }, 5500);
                setTimeout(() => {
                    this.countTurnover = 0;
                    this.innerState = "default";
                    this.$emit("finish", this.innerState);
                    this.refresh();
                }, 6500);
            } else if (newState === "lose") {
                // Проигрыш: стрелка НЕ должна указывать на залитый сегмент
                const emptySegmentDeg = 360 - filledSegmentDeg;

                let missDeg;
                if (emptySegmentDeg > 10) {
                    // Останавливаемся в незалитой зоне
                    // Незалитая зона: от -360° до -filledSegmentDeg
                    const emptyZoneStart = -360;
                    const emptyZoneEnd = -filledSegmentDeg;

                    // Выбираем случайную точку в незалитой зоне с небольшим отступом от границ
                    missDeg = this.random(emptyZoneStart + 5, emptyZoneEnd - 5);
                } else {
                    // Если незалитая зона очень маленькая, останавливаемся точно по центру
                    missDeg = -filledSegmentDeg - emptySegmentDeg / 2;
                }

                this.countTurnover = fullRotations + missDeg;
              
                setTimeout(() => {
                    this.innerState = "lose";
                    this.$emit("finish", this.innerState);
                    this.$playSound("/sounds/upgrade-result-lose.mp3");
                }, 5500);
                setTimeout(() => {
                    this.countTurnover = 0;
                    this.innerState = "default";
                    this.$emit("finish", this.innerState);
                    this.refresh();
                }, 6500);
            }
        },
    },
    methods: {
        random(max, min = 0) {
            return Math.random() * (max - min) + min;
        },
        getItemRarityClass,
    },
};
</script>
