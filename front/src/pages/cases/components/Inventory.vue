<template>
    <div class="cases__block">
        <div class="cases__head">
            <div class="title">
                <span>Содержимое кейса</span>
                <!-- <img src="/images/title-event.svg" alt="" /> -->
            </div>
            <button
                @click="show = !show"
                type="button"
                class="dropdown__btn--small click"
                :class="{ closed: !show }"
            >
                <span>{{ show ? "Свернуть" : "Развернуть" }}</span>
                <i></i>
            </button>
        </div>
        <div class="items" v-show="show">
            <div
                class="item"
                v-for="(item, index) in caseContent"
                :key="index"
                :class="getItemRarityClass(item.rarity)"
            >
                <div class="item__inner">
                    <div class="item__top">
                        <div class="item__quality-top">{{ item.quality }}</div>
                        <div class="sum sum--xs sum--bgWhite">
                            <div
                                class="icon coin"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                            {{ item.steam_price / 100 }}
                        </div>
                    </div>
                    <div class="item__center">
                        <img :src="item.image" class="item__image" alt="Скин" />
                        <div
                            class="icon item__center-snake"
                            style="mask-image: url('/assets/icons/snake.svg')"
                        ></div>
                    </div>
                    <div class="item__bottom">
                        <div class="item__model">{{ item.weapon }}</div>
                        <div class="item__name">{{ item.skin_name }}</div>
                    </div>
                </div>
                <img
                    :src="`/images/case/shadow-${getItemRarityClass(
                        item.rarity
                    )}.webp`"
                    class="item__rarity-img"
                    alt="rarity"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { getItemRarityClass } from "../../../helpers/helpers";
export default {
    props: { caseContent: Array },
    data() {
        return {
            show: true,
        };
    },
    methods: {
        getItemRarityClass,
    },
};
</script>
