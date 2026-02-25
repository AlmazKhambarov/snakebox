<template>
    <div class="page profile user">
        <div class="page__header">
            <div class="page__header-left">
                <router-link :to="{ name: 'index' }" class="page__header-back">
                    <div
                        class="icon"
                        style="mask-image: url('/images/icons/arrow-left.svg')"
                    ></div>
                </router-link>
                <div class="page__header-info">
                    <div class="page__header-info-inner">
                        <span>{{ user.username }}</span>
                        <p>Профиль игрока</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="page__body">
            <div class="profile__wrapp">
                <div class="profile__top">
                    <div class="profile__left">
                        <div class="profile__left-top">
                            <div class="profile__card">
                                <div class="profile__user">
                                    <div style="position: relative">
                                        <img :src="user.avatar" alt="" />
                                        <svg
                                            v-if="user.is_vip"
                                            width="28"
                                            height="16"
                                            viewBox="0 0 28 16"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="avatar_vipBadge profile"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M4.89886 0C3.46021 0 2.23054 1.05217 1.98693 2.49183L0.294193 12.4918C-0.0160643 14.3249 1.37427 16 3.20613 16H23.0704C24.5059 16 25.7335 14.9527 25.9809 13.5172L27.7045 3.51715C28.0209 1.6814 26.6294 0 24.794 0H4.89886Z"
                                                fill="#ffffff"
                                                stroke="#1d1e20"
                                                stroke-width="0"
                                            ></path>
                                            <path
                                                d="M9.13104 9.41751L11.7295 4.88333H13.577L13.5344 5.13919L9.93139 11.106H7.99002L6.40576 5.13919L6.43963 4.88333H8.05293L9.13104 9.41751Z"
                                                fill="#1d1e20"
                                            ></path>
                                            <path
                                                d="M14.5042 11.106H12.8164L13.8635 4.88333H15.5523L14.5042 11.106Z"
                                                fill="#1d1e20"
                                            ></path>
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M20.1841 4.88333C21.4564 4.88333 22.5689 5.7493 22.3645 7.14993C22.1681 8.4272 21.301 9.06584 19.7631 9.06595H17.3707L17.0262 11.106H15.3384L16.3855 4.88333H20.1841ZM17.6069 7.64407H19.8202C20.3267 7.64407 20.6135 7.44455 20.6806 7.04642C20.7507 6.56564 20.4916 6.30627 20.0234 6.30618H17.8469L17.6069 7.64407Z"
                                                fill="#1d1e20"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div class="profile__user-inner">
                                        <span>{{ user.username }}</span>
                                        <div class="profile__user-id">
                                            <p>ID: {{ user.id }}</p>
                                            <input
                                                type="hidden"
                                              ref="copyInput"
                                        :value="user.id"
                                                class="profileUserId"
                                            />
                                            <button
                                                type="button"
                                                class="copyUserId"
                                                 @click="copyId"
                                            >
                                                <div
                                                    class="icon"
                                                    style="
                                                        mask-image: url('/images/icons/copy.svg');
                                                    "
                                                ></div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile__left-grid">
                            <div class="bonus__task">
                                <div class="bonus__task-left">
                                    <div class="sum">
                                        <span>
                                            {{
                                                formatDateTime(user.created_at)
                                            }}
                                        </span>
                                    </div>
                                    <div class="bonus__task-title">
                                        На сайте с
                                    </div>
                                </div>
                                <div class="bonus__task-image">
                                    <img
                                        src="/assets/images/time.png"
                                        alt="Время в игре"
                                    />
                                </div>
                            </div>
                            <div class="bonus__task">
                                <div class="bonus__task-left">
                                    <div class="sum">
                                        <div
                                            class="icon coin"
                                            style="
                                                mask-image: url('/assets/icons/coin.svg');
                                            "
                                        ></div>
                                        <span>{{
                                            topDrop?.price / 100 || 0
                                        }}</span>
                                    </div>
                                    <div class="bonus__task-title">
                                        Топ выигрыш
                                    </div>
                                </div>
                                <div class="bonus__task-image top-win">
                                    <img
                                        src="/assets/images/top-win.png"
                                        alt="Лучший выигрыш"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile__right">
                        <div class="profile__skins">
                            <div class="profile__skins-item top">
                                <span>Топ дроп</span>
                                <div
                                    class="profile__skins-item-skin-wrapp"
                                    v-if="topDrop"
                                >
                                    <div class="item__top">
                                        <div class="sum sum--xs sum--bgWhite">
                                            <div
                                                class="icon"
                                                style="
                                                    mask-image: url('/assets/icons/coin.svg');
                                                "
                                            ></div>
                                            {{ topDrop?.price / 100 || 0 }}
                                        </div>
                                    </div>
                                    <div class="item__center">
                                        <img
                                            :src="topDrop?.item?.image"
                                            class="item__image"
                                            alt="Galil AR | Orange DDPAT (Field-Tested)"
                                        />
                                        <img
                                            :src="`/images/case/shadow-${getItemRarityClass(
                                                topDrop?.item?.rarity
                                            )}-circle.png`"
                                            class="item__rarity-img"
                                            alt=""
                                        />
                                    </div>
                                    <div class="item__bottom">
                                        <div class="item__model">
                                            {{ topDrop?.item?.weapon }}
                                        </div>
                                        <div class="item__name">
                                            {{ topDrop?.item?.skin_name }}
                                        </div>
                                        <div class="item__quality">
                                            {{ topDrop?.item?.quality }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="profile__skins-item-skin-wrapp"
                                >
                                    <div class="item__center">
                                        <div class="empty-item">
                                            Нет предмета
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="profile__skins-item love">
                                <span>Любимый кейс</span>
                                <router-link
                                    v-if="favoriteBox"
                                    :to="{
                                        name: 'case',
                                        params: { url: favoriteBox.box.url },
                                    }"
                                    class="profile__skins-item-skin-wrapp"
                                >
                                    <div class="item__center">
                                        <img
                                            :src="favoriteBox.box.image"
                                            class="item__image"
                                            :alt="favoriteBox.box.name"
                                        />
                                        <div
                                            class="icon item__center-snake"
                                            style="
                                                mask-image: url('/assets/icons/snake.svg');
                                            "
                                        ></div>
                                    </div>
                                </router-link>
                                <div
                                    v-else
                                    class="profile__skins-item-skin-wrapp no-favorite-case"
                                >
                                    <div class="item__center">
                                        <div class="no-case-text">
                                            Еще не открывали кейсы
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile__inventory">
                <div class="withdraw__items-wrapp" v-if="userItems.length >= 1">
                    <div class="items">
                        <button
                            v-for="(item, index) in userItems"
                            :key="index"
                            type="button"
                            class="item withdraw__item"
                        >
                            <div class="item__inner">
                                <div class="item__top">
                                    <div
                                        class="status"
                                        :class="{
                                            warning: item.status === 'STOCK',
                                            gray: item.status === 'SELL',
                                            success:
                                                item.status === 'WITHDRAWN',
                                        }"
                                    >
                                        <i></i>
                                        <span>
                                            {{ getItemStatusText(item.status) }}
                                        </span>
                                    </div>
                                    <div
                                        class="sum sum--xs sum--bgWhite itemPrice"
                                    >
                                        <div
                                            class="icon"
                                            style="
                                                mask-image: url('/assets/icons/coin.svg');
                                            "
                                        ></div>
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
                                    ></div>
                                </div>
                                <div class="item__bottom">
                                    <div class="item__model">
                                        {{ item.item.weapon }}
                                    </div>
                                    <div class="item__name">
                                        {{ item.item.skin_name }}
                                    </div>
                                    <div class="item__quality"></div>
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
                <div
                    v-if="userItems.length === 0"
                    class="withdraw__empty empty"
                    style=""
                >
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
                        @click="changePage(page - 1)"
                    >
                        <div
                            class="icon"
                            style="
                                mask-image: url('/images/icons/arrow-left.svg');
                            "
                        ></div>
                        <span>Предыдущая страница</span>
                    </button>
                    <div class="pagination__current">
                        <span>{{ page }} из {{ totalPages }}</span>
                    </div>
                    <button
                        type="button"
                        class="pagination__button pagination__next"
                        :disabled="!hasMorePages"
                        @click="changePage(page + 1)"
                    >
                        <span>Следующая страница</span>
                        <div
                            class="icon"
                            style="
                                mask-image: url('/images/icons/arrow-right.svg');
                            "
                        ></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { request } from "@/utils/request.js";
import { getItemRarityClass } from "../../helpers/helpers";
import { useSeo } from "@/composables/useSeo.js";

export default {
    setup() {
        // Инициализируем SEO для страницы профиля другого пользователя
        const { seoData, updateSeo, updateOpenGraph } = useSeo("OtherProfile", {
            id: "",
            username: "Профиль игрока",
        });

        return {
            seoData,
            updateSeo,
            updateOpenGraph,
        };
    },
    data() {
        return {
            user: [],
            userItems: [],
            page: 1,
            hasMorePages: true,
            totalPages: 1,
            topDrop: null,
            favoriteBox: null,
        };
    },
    mounted() {
        this.getUser();
    },
    methods: {
        changePage(newPage) {
            if (newPage < 1 || newPage > this.totalPages) return;
            this.getUser(newPage);
        },
        async getUser(newPage = null) {
            if (newPage !== null) {
                this.page = newPage;
            }
            try {
                const params = {
                    page: this.page,
                    id: this.$route.params.id,
                };

                const { data } = await request("GET", "/user/other", params);

                if (!data.success) {
                    this.$router.push({ name: "index" });
                } else {
                    this.user = data.user;
                    this.topDrop = data.topDrop;
                    this.favoriteBox = data.favoriteBox;
                    this.userItems = data.items.data;
                    this.hasMorePages = data.hasMorePages;
                    this.totalPages = data.items.last_page;

                    // Обновляем SEO после загрузки пользователя
                    this.updateSeo({
                        id: String(this.user.id || this.$route.params.id || ""),
                        username: this.user.username || "Профиль игрока",
                    });
                }
            } catch (error) {
                console.error("Ошибка при загрузке предметов:", error);
            }
        },
        getItemStatusText(status) {
            const statusMap = {
                "": "Продан",
                STOCK: "Ожидание",
                SELL: "Продан",
                SENDING: "Ожидаем продавца",
                WITHDRAWN: "Выведен",
            };
            return statusMap[status] || "Продан";
        },
        formatDateTime(dateString) {
            if (!dateString) return "-";

            const date = new Date(dateString);

            // Проверка на валидность даты
            if (isNaN(date.getTime())) return "-";

            return date.toLocaleString("ru-RU", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
            });
        },
             copyId() {
            const input = this.$refs.copyInput;
            if (!input) return;
            input.select();
            navigator.clipboard.writeText(input.value);
            this.$toastr.success("ID скопирован в буфер обмена");
        },
        getItemRarityClass,
    },
};
</script>
