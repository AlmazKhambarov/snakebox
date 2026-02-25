<template>
    <div class="profile__inventory" id="withdraw">
        <div class="title">
            <span>Ваш инвентарь</span
            >
        </div>

        <div class="withdraw__items-wrapp">
            <template v-if="userItems.length !== 0">
                <div class="items">
                    <div
                        class="case-win-item"
                        v-for="(item, index) in userItems"
                        :key="index"
                        @click="toggleItem(item)"
                    >
                        <button
                            type="button"
                            class="item withdraw__item"
                            :class="[
                                { selected: isItemSelected(item) },
                                getItemRarityClass(item.item.rarity),
                            ]"
                        >
                            <div class="item__inner">
                                <div class="item__top">
                                    <div class="item__quality-top">
                                        {{ item.item.quality }}
                                    </div>
                                    <div
                                        class="sum sum--xs sum--bgWhite itemPrice"
                                    >
                                        <div
                                            class="icon"
                                            style="
                                                mask-image: url('images/icons/coin.svg');
                                            "
                                        >
                                            <div class="icon__wrapp"></div>
                                        </div>
                                        {{ item.price / 100 }}
                                    </div>
                                </div>
                                <div class="item__center">
                                    <img
                                        :src="item.item.image"
                                        class="item__image"
                                        alt="skin"
                                    />
                                    <div
                                        class="icon item__center-snake"
                                        style="
                                            mask-image: url('/assets/icons/snake.svg');
                                        "
                                    >
                                        <div class="icon__wrapp"></div>
                                    </div>
                                </div>
                                <div class="item__bottom">
                                    <div class="item__model">
                                        {{ item.item.weapon }}
                                    </div>
                                    <div class="item__name">
                                        {{ item.item.skin_name }}
                                    </div>
                                </div>
                            </div>
                            <img
                                :src="`/images/case/shadow-${getItemRarityClass(
                                    item.item.rarity
                                )}.webp`"
                                class="item__rarity-img"
                                alt="rarity"
                            />
                        </button>
                    </div>
                </div>
            </template>
        </div>
        <div v-if="userItems.length === 0" class="withdraw__empty empty">
            <div
                class="icon"
                style="mask-image: url('/images/icons/skins.svg')"
            ></div>
            <span>В инвентаре сейчас нет предметов</span>
        </div>
        <div class="pagination">
            <button
                type="button"
                class="pagination__button pagination__prev"
                :disabled="page <= 1"
                @click="$emit('change-page', page - 1)"
            >
                <div
                    class="icon"
                    style="mask-image: url('/images/icons/arrow-left.svg')"
                ></div>
                <span>Предыдущая страница</span>
            </button>
            <div class="pagination__current">
                <span
                    ><span>{{ page }}</span> из {{ totalPages }}</span
                >
            </div>
            <button
                type="button"
                class="pagination__button pagination__next"
                :disabled="!hasMorePages"
                @click="$emit('change-page', page + 1)"
            >
                <span>Следующая страница</span>
                <div
                    class="icon"
                    style="mask-image: url('/images/icons/arrow-right.svg')"
                ></div>
            </button>
        </div>
        <!--v-if-->
    </div>
</template>

<script>
import { getItemRarityClass } from "../../../helpers/helpers";
export default {
    props: {
        userItems: Array,
        page: Number,
        hasMorePages: Boolean,
        totalPages: Number,
        toggleItem: Function,
        isItemSelected: Function,
    },
    methods: {
        getItemRarityClass,
    },
};
</script>
