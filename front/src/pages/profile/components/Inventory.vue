<template>
    <div class="profile__inventory" id="withdraw">
        <div class="cases__top">
            <div class="cases__top-left">
                <div class="cases__select-game">
                    <button
                        type="button"
                        class="cases__select-game-btn active"
                        data-game="0"
                    >
                        <div
                            class="icon"
                            style="mask-image: url('/images/icons/cs2.svg')"
                        ></div>
                    </button>
                    <!-- <button
                        type="button"
                        class="cases__select-game-btn"
                        data-game="1"
                    >
                        <div
                            class="icon"
                            style="mask-image: url('/images/icons/dota2.svg')"
                        ></div>
                    </button>
                    <button
                        type="button"
                        class="cases__select-game-btn"
                        data-game="2"
                    >
                        <div
                            class="icon"
                            style="mask-image: url('/images/icons/rust.svg')"
                        ></div>
                    </button> -->
                </div>
                <button
                    type="button"
                    @click="resetAllFilters"
                    class="btn-gray clearAll"
                >
                    <div
                        class="icon"
                        style="mask-image: url('/images/icons/clear.svg')"
                    ></div>
                    Сбросить всё
                </button>
            </div>
            <div class="cases__top-right">
                <div class="form-input">
                    <div class="form-input__wrapp">
                        <div class="form-input__icon">
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/search.svg');
                                "
                            ></div>
                        </div>
                        <input
                            type="text"
                            class="searchSkin"
                            placeholder="Название"
                            v-model="filterInputSearch"
                        />
                    </div>
                </div>
                <div class="cases__prices">
                    <div class="form-input__wrapp">
                        <input
                            type="number"
                            v-model="minPrice"
                            placeholder="От"
                            id="price-from"
                        />
                    </div>
                    <div class="form-input__wrapp">
                        <input
                            type="number"
                            v-model="maxPrice"
                            placeholder="До"
                            id="price-to"
                        />
                    </div>
                    <div class="cases__prices-divider"></div>
                    <div class="cases__prices-slider">
                        <div id="price-slider"></div>
                    </div>
                </div>
                <button
                    type="button"
                    class="btn-gray cases__clear-all-mobile clearAll"
                >
                    <div
                        class="icon"
                        style="mask-image: url('/images/icons/clear.svg')"
                    ></div>
                    Сбросить всё
                </button>
            </div>
        </div>
        <div class="withdraw__sort">
            <div class="withdraw__sort-left">
                <!-- Фильтр по статусу -->
                <div class="dropdown" :class="{ active: showStatusDropdown }">
                    <button
                        type="button"
                        @click="showStatusDropdown = !showStatusDropdown"
                        @keydown.esc="showStatusDropdown = false"
                        class="withdraw__sort-item"
                    >
                        <span>{{ getStatusText(selectedStatus) }}</span>
                        <i></i>
                    </button>
                    <div class="dropdown__inner statusInventory">
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedStatus === '' }"
                            @click="changeStatus('')"
                        >
                            Все предметы
                        </button>
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedStatus === 'STOCK' }"
                            @click="changeStatus('STOCK')"
                        >
                            Доступные
                        </button>
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedStatus === 'SELL' }"
                            @click="changeStatus('SELL')"
                        >
                            Проданные
                        </button>
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedStatus === 'WITHDRAWN' }"
                            @click="changeStatus('WITHDRAWN')"
                        >
                            Выведенные
                        </button>
                    </div>
                </div>

                <!-- Сортировка -->
                <div class="dropdown" :class="{ active: showSortDropdown }">
                    <button
                        type="button"
                        @click="showSortDropdown = !showSortDropdown"
                        @keydown.esc="showSortDropdown = false"
                        class="withdraw__sort-item"
                    >
                        <span>{{ getSortText(selectedSort) }}</span>
                        <i></i>
                    </button>
                    <div class="dropdown__inner sortInventory">
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedSort === 'price_asc' }"
                            @click="changeSort('price_asc')"
                        >
                            По цене (по возр.)
                        </button>
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedSort === 'price_desc' }"
                            @click="changeSort('price_desc')"
                        >
                            По цене (по убыв.)
                        </button>
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedSort === 'oldest' }"
                            @click="changeSort('oldest')"
                        >
                            По дате (по возр.)
                        </button>
                        <button
                            type="button"
                            class="dropdown__link"
                            :class="{ active: selectedSort === 'newest' }"
                            @click="changeSort('newest')"
                        >
                            По дате (по убыв.)
                        </button>
                    </div>
                </div>
            </div>
            <button @click="sellAllItem" class="withdraw__sort-sale-all">
                Продать всё
            </button>
        </div>
        <LoadingSpinner v-if="isLoading" text="Загрузка инвентаря..." />
        <div class="withdraw__items-wrapp" v-else-if="userItems.length >= 1">
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
                                    success: item.status === 'WITHDRAWN',
                                }"
                            >
                                <i></i>
                                <span>
                                    {{ getItemStatusText(item.status) }}
                                </span>
                            </div>
                            <div class="sum sum--xs sum--bgWhite itemPrice">
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
                        <div
                            class="item__status"
                            v-if="item.status === 'STOCK'"
                        >
                            <button
                                class="item__withdraw"
                                @click="withdraw(item.id)"
                            >
                                <div class="item__withdraw-icon">
                                    <img
                                        loading="lazy"
                                        class="CustomImage_img__2Vg0y"
                                        src="/images/new/common/withdraw.svg"
                                        alt=""
                                    />
                                </div>
                            </button>
                            <button
                                class="item__sell"
                                @click="sellItem(item.id)"
                            >
                                <div class="item__sell-icon">
                                    <img
                                        loading="lazy"
                                        class="CustomImage_img__2Vg0y"
                                        src="/images/new/common/black-cart.svg"
                                        alt=""
                                    />
                                </div>
                            </button>
                        </div>
                    </div>
                    <img
                        :src="`/images/case/shadow-${getItemRarityClass(
                            item.item.rarity
                        )}.webp`"
                        class="item__rarity-img"
                        alt="rarity"
                    />
                    <div
                        class="blocker"
                        v-if="
                            item.status === 'SENDING' ||
                            item.status === 'WAIT' ||
                            item.status === 'ORDER_READY'
                        "
                    >
                        <div
                            v-show="
                                item.status === 'SENDING' ||
                                item.status === 'WAIT'
                            "
                            class="loader__Me4ix"
                        >
                            <div class="loader color--gold">
                                <span></span><span></span><span></span>
                            </div>
                        </div>
                        <button
                            v-show="item.status === 'ORDER_READY'"
                            type="button"
                            class="btn page__controls-main-btn"
                        >
                            <div class="btn__inner">
                                <div class="btn__inner-left">
                                    <h2>ПРИНЯТЬ ТРЕЙД</h2>
                                </div>
                            </div>
                        </button>
                        <div
                            v-show="
                                item.status === 'SENDING' ||
                                item.status === 'WAIT'
                            "
                            class="text__OlAkW color--inherit__nPrtI variant--h5___ABOK bold__nhebz"
                        >
                            {{
                                item.status === "SENDING"
                                    ? "Закупаем предмет"
                                    : item.status === "WAIT"
                                    ? "Ожидаем продавца"
                                    : "UNKNOWN"
                            }}
                        </div>
                    </div>
                </button>
            </div>
        </div>
        <div
            v-if="userItems.length === 0 && !isLoading"
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
                    style="mask-image: url('/images/icons/arrow-left.svg')"
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
                    style="mask-image: url('/images/icons/arrow-right.svg')"
                ></div>
            </button>
        </div>
    </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { request } from "@/utils/request.js";
import { getItemRarityClass } from "../../../helpers/helpers";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

export default {
    components: { LoadingSpinner },
    inject: ["socket-client"],
    data() {
        return {
            userItems: [],
            isLoading: true,
            page: 1,
            hasMorePages: true,
            totalPages: 1,

            // Фильтры
            minPrice: null,
            maxPrice: null,
            filterInputSearch: "",
            selectedStatus: "",
            selectedSort: "newest",

            // Dropdown состояния
            showStatusDropdown: false,
            showSortDropdown: false,

            debounceTimer: null,
            isSelling: false,
        };
    },
    computed: {
        ...mapActions(useAuthStore, ["logOut", "getUser"]),
        ...mapState(useAuthStore, ["isAuth", "user"]),
    },
    mounted() {
        this.getItems();
        this.subscribeSocket();
        // Закрываем dropdown при клике вне
        document.addEventListener("click", this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside);
    },
    watch: {
        filterInputSearch() {
            this.debounceGetItems();
        },
        minPrice() {
            this.debounceGetItems();
        },
        maxPrice() {
            this.debounceGetItems();
        },
        selectedStatus() {
            this.page = 1;
            this.getItems();
        },
        selectedSort() {
            this.page = 1;
            this.getItems();
        },
    },
    methods: {
        handleClickOutside(event) {
            if (!event.target.closest(".dropdown")) {
                this.showStatusDropdown = false;
                this.showSortDropdown = false;
            }
        },

        changeStatus(status) {
            this.selectedStatus = status;
            this.showStatusDropdown = false;
        },

        changeSort(sort) {
            this.selectedSort = sort;
            this.showSortDropdown = false;
        },

        getStatusText(status) {
            const statusMap = {
                "": "Все предметы",
                STOCK: "Доступные",
                SELL: "Проданные",
                WITHDRAWN: "Выведенные",
            };
            return statusMap[status] || "Все предметы";
        },

        getSortText(sort) {
            const sortMap = {
                newest: "По дате (по убыв.)",
                oldest: "По дате (по возр.)",
                price_desc: "По цене (по убыв.)",
                price_asc: "По цене (по возр.)",
            };
            return sortMap[sort] || "По дате (по убыв.)";
        },

        changePage(newPage) {
            if (newPage < 1 || newPage > this.totalPages) return;
            this.getItems(newPage);
        },

        async getItems(newPage = null) {
            if (newPage !== null) {
                this.page = newPage;
            }

            this.isLoading = true;
            try {
                const params = {
                    page: this.page,
                    market_name: this.filterInputSearch,
                    min: this.minPrice ? this.minPrice * 100 : null,
                    max: this.maxPrice ? this.maxPrice * 100 : null,
                    status: this.selectedStatus,
                    sort: this.selectedSort,
                };

                // Убираем пустые параметры
                Object.keys(params).forEach((key) => {
                    if (
                        params[key] === null ||
                        params[key] === "" ||
                        params[key] === undefined
                    ) {
                        delete params[key];
                    }
                });

                const { data } = await request("GET", "/user/items", params);

                if (!data.success) {
                } else {
                    this.userItems = data.items.data;
                    this.hasMorePages = data.hasMorePages;
                    this.totalPages = data.items.last_page;
                }
            } catch (error) {
                console.error("Ошибка при загрузке предметов:", error);
            } finally {
                this.isLoading = false;
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

        debounceGetItems() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.page = 1;
                this.getItems();
            }, 600);
        },
        async sellItem(liveId) {
            if (this.isSelling) return;
            this.isSelling = true;
            try {
                await request("POST", "/case/sell/item", {
                    liveId: liveId,
                }).then(({ data }) => {
                    if (!data.success) {
                        this.$toastr.error(data.message);
                        return;
                    } else {
                        this.$toastr.success(data.message);
                        this.getItems();
                    }
                });
            } finally {
                this.isSelling = false;
            }
        },
        async withdraw(liveId) {
            if (this.isSelling) return;
            this.isSelling = true;
            try {
                const item = this.userItems.find((i) => i.id === liveId);
                if (item) item.status = "SENDING";
                await request("POST", "/market/withdraw", {
                    liveId: liveId,
                }).then(({ data }) => {
                    if (!data.success) {
                        this.$toastr.error(data.message);
                        return;
                    } else {
                        this.$toastr.success(data.message);
                        this.getItems();
                    }
                });
            } finally {
                this.isSelling = false;
            }
        },
        async sellAllItem() {
            if (this.isSelling) return;
            this.isSelling = true;
            try {
                await request("POST", "/case/sell/allItem").then(({ data }) => {
                    if (!data.success) {
                        this.$toastr.error(data.message);
                        return;
                    } else {
                        this.$toastr.success(data.message);
                        this.getItems();
                    }
                });
            } finally {
                this.isSelling = false;
            }
        },
        subscribeSocket() {
            const socket = this["socket-client"];
            if (!socket) return;

            if (!socket.connected) {
                socket.connect();
            }

            socket.on("setItemsStatus", (items) => {

                if (!this.isAuth) return;

                items.forEach((data) => {
                    if (Number(this.user.id) !== Number(data.user_id)) return;

                    const index = this.userItems.findIndex(
                        (item) => item.id === data.id
                    );

                    if (index > -1) {
                        if (data.status === "ORDER_READY" && data.trade_id) {
                            this.userItems[index].trade_id = data.trade_id;
                        }
                        this.userItems[index].status = data.status;
                    }
                });
            });
        },

        resetAllFilters() {
            // Сбрасываем все фильтры
            this.filterInputSearch = "";
            this.minPrice = null;
            this.maxPrice = null;
            this.selectedStatus = "";
            this.selectedSort = "newest";
            this.page = 1;

            // Закрываем dropdown'ы
            this.showStatusDropdown = false;
            this.showSortDropdown = false;

            // Загружаем данные с сброшенными фильтрами
            this.getItems();
        },

        getItemRarityClass,
    },
};
</script>
