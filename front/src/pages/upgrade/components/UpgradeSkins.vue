<template>
    <div class="upgrade__skins-wrapp">
        <div class="upgrade__skins-wrapp-item">
            <div class="upgrade__skins-wrapp-title">Ваш инвентарь</div>
            <div class="cases__top-right cases__top-right--upgrade">
                <div class="upgrade__balance-toggle clickable" @click="$emit('select-balance-mode')" :class="{ active: isBalanceMode }">
                    <div class="icon" style="mask-image: url('images/icons/coin.svg');"></div>
                    <span>Баланс</span>
                </div>
                <div class="form-input upgrade__search" v-if="!isBalanceMode">
                    <div class="form-input__wrapp">
                        <div class="form-input__icon">
                            <div
                                class="icon"
                                style="
                                    mask-image: url('images/icons/search.svg');
                                "
                            ></div>
                        </div>
                        <input
                            type="text"
                            v-model="userLocalFilter"
                            placeholder="Название"
                            class="searchUpgradeInventory"
                        />
                    </div>
                </div>
                <div class="cases__prices">
                    <div class="form-input__wrapp">
                        <input
                            type="number"
                            v-model="userLocalMinPrice"
                            id="price-from-inventory"
                            placeholder="От"
                        />
                    </div>
                    <div class="form-input__wrapp">
                        <input
                            type="number"
                            v-model="userLocalMaxPrice"
                            id="price-to-inventory"
                            placeholder="До"
                        />
                    </div>
                    <div class="cases__prices-divider"></div>
                </div>
                <button
                    type="button"
                    class="upgrade__cases-price changeSortPricesInventory"
                    @click="userToggleSort"
                    :class="{ active: userLocalSort === 'desc' }"
                >
                    Цена
                    <div
                        class="icon"
                        style="mask-image: url('images/icons/arrow-down.svg')"
                    ></div>
                </button>
            </div>
            <div class="mines__inventory">
                <div class="mines__inventory-wrapp">
                    <LoadingSpinner v-if="isUserLoading" text="Загрузка инвентаря..." />
                    <div
                        v-else-if="isBalanceMode"
                        class="mines__inventory-scroll"
                    >
                        <div class="upgrade__balance-input-wrap">
                            <div class="upgrade__balance-title">Введите сумму для апгрейда</div>
                            <div class="form-input upgrade__balance-field">
                                <div class="form-input__wrapp">
                                    <div class="form-input__icon">
                                        <div class="icon" style="mask-image: url('images/icons/coin.svg');"></div>
                                    </div>
                                    <input 
                                        type="number" 
                                        :value="balanceAmount" 
                                        @input="$emit('update:balanceAmount', Number($event.target.value))"
                                        placeholder="Сумма" 
                                    />
                                </div>
                            </div>
                            <div class="upgrade__balance-hint">Минимальная сумма: 0.01</div>
                        </div>
                    </div>
                    <div
                        v-else-if="userSkins.length >= 1"
                        class="mines__inventory-scroll"
                    >
                        <div class="items my">
                            <div
                                class="case-win-item"
                                v-for="(item, index) in userSkins"
                                :key="index"
                            >
                                <button
                                    type="button"
                                    class="item withdraw__item"
                                    @click="setSelectedUserItem(item)"
                                    :class="[
                                        getItemRarityClass(item.item.rarity), // класс по редкости
                                        {
                                            selected:
                                                selectedUserItem &&
                                                selectedUserItem.id === item.id,
                                        }, // класс выбранного
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
                                                    <div
                                                        class="icon__wrapp"
                                                    ></div>
                                                </div>
                                                {{ item.price / 100 }}
                                            </div>
                                        </div>
                                        <div class="item__center">
                                            <img
                                                :src="item.item.image"
                                                class="item__image"
                                                alt="userSkin"
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
                        <div class="pagination">
                            <button
                                :disabled="userPage <= 1"
                                @click="$emit('change-page-user', userPage - 1)"
                                type="button"
                                class="pagination__button pagination__prev"
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
                                <span
                                    ><span>{{ userPage }}</span> из
                                    {{ userTotalPages }}</span
                                >
                            </div>
                            <button
                                :disabled="!userHasMorePages"
                                @click="$emit('change-page-user', userPage + 1)"
                                type="button"
                                class="pagination__button pagination__next"
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
                    <div
                        v-if="userSkins.length === 0 && !isUserLoading"
                        class="withdraw__empty empty my"
                        style=""
                    >
                        <div
                            class="icon"
                            style="mask-image: url('images/icons/skins.svg')"
                        ></div>
                        <span>В инвентаре сейчас нет предметов</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="upgrade__skins-wrapp-item">
            <div class="upgrade__skins-wrapp-title">Предметы для апгрейда</div>
            <div class="cases__top-right cases__top-right--upgrade">
                <div class="form-input upgrade__search">
                    <div class="form-input__wrapp">
                        <div class="form-input__icon">
                            <div
                                class="icon"
                                style="
                                    mask-image: url('images/icons/search.svg');
                                "
                            ></div>
                        </div>
                        <input
                            type="text"
                            placeholder="Название"
                            class="searchUpgradeShop"
                            v-model="localFilter"
                        />
                    </div>
                </div>
                <div class="cases__prices">
                    <div class="form-input__wrapp">
                        <input
                            type="number"
                            v-model="localMinPrice"
                            id="price-from-shop"
                            placeholder="От"
                        />
                    </div>
                    <div class="form-input__wrapp">
                        <input
                            type="number"
                            v-model="localMaxPrice"
                            id="price-to-shop"
                            placeholder="До"
                        />
                    </div>
                    <div class="cases__prices-divider"></div>
                </div>
                <button
                    @click="toggleSort"
                    :class="{ active: localSort === 'desc' }"
                    type="button"
                    class="upgrade__cases-price changeSortPricesShop"
                >
                    Цена
                    <div
                        class="icon"
                        style="mask-image: url('images/icons/arrow-down.svg')"
                    ></div>
                </button>
            </div>
            <div class="mines__inventory">
                <div class="mines__inventory-wrapp">
                    <LoadingSpinner v-if="isSiteLoading" text="Загрузка предметов..." />
                    <div
                        v-else-if="siteSkins.length >= 1"
                        class="mines__inventory-scroll"
                    >
                        <div class="items shop">
                            <button
                                type="button"
                                class="item withdraw__item"
                                v-for="(item, index) in siteSkins"
                                :key="index"
                                @click="setSelectedSiteItem(item)"
                                :class="{
                                    selected:
                                        selectedSiteItem &&
                                        selectedSiteItem.id === item.id,
                                    locked: isItemLocked(item),
                                }"
                                :disabled="isItemLocked(item)"
                            >
                                <div class="item__inner">
                                    <div class="item__top">
                                        <div class="item__quality-top">
                                            {{ item.quality }}
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
                                            {{ item.steam_price / 100 }}
                                        </div>
                                    </div>
                                    <div class="item__center">
                                        <img
                                            :src="item.image"
                                            class="item__image"
                                            alt=""
                                        />
                                        <div
                                            class="icon item__center-snake"
                                            style="
                                                mask-image: url('/assets/icons/snake.svg');
                                            "
                                        ></div>
                                        <!-- Замочек для заблокированных предметов -->
                                        <div
                                            v-if="isItemLocked(item)"
                                            class="item__lock"
                                        >
                                            <div
                                                class="icon"
                                                style="
                                                    mask-image: url('/images/icons/lock.svg');
                                                "
                                            ></div>
                                        </div>
                                    </div>
                                    <div class="item__bottom">
                                        <div class="item__model">
                                            {{ item.weapon }}
                                        </div>
                                        <div class="item__name">
                                            {{ item.skin_name }}
                                        </div>
                                        <!-- <div class="item__quality">Field-Tested</div> -->
                                    </div>
                                </div>
                                <img
                                    :src="`/images/case/shadow-${getItemRarityClass(
                                        item.rarity
                                    )}.webp`"
                                    class="item__rarity-img"
                                    alt="rarity"
                                />
                            </button>
                        </div>
                        <div class="pagination">
                            <button
                                :disabled="page <= 1"
                                @click="$emit('change-page', page - 1)"
                                type="button"
                                class="pagination__button pagination__prev"
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
                                <span
                                    ><span>{{ page }}</span> из
                                    {{ totalPages }}</span
                                >
                            </div>
                            <button
                                :disabled="!hasMorePages"
                                @click="$emit('change-page', page + 1)"
                                type="button"
                                class="pagination__button pagination__next"
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
                    <div
                        v-if="siteSkins.length === 0 && !isSiteLoading"
                        class="withdraw__empty empty shop"
                    >
                        <div
                            class="icon"
                            style="mask-image: url('images/icons/skins.svg')"
                        ></div>
                        <span>В инвентаре сейчас нет предметов</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { getItemRarityClass } from "../../../helpers/helpers";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

export default {
    components: { LoadingSpinner },
    props: {
        siteSkins: Array,
        page: Number,
        hasMorePages: Boolean,
        totalPages: Number,
        filterInputSearch: String,
        minPrice: Number,
        maxPrice: Number,
        sort: String,
        selectedSiteItem: Object,
        isSiteLoading: Boolean,
        // user
        userSkins: Array,
        userPage: Number,
        userHasMorePages: Boolean,
        userTotalPages: Number,
        userFilterInputSearch: String,
        userMinPrice: Number,
        userMaxPrice: Number,
        userSort: String,
        selectedUserItem: Object,
        isUserLoading: Boolean,
        //other
        inventoryButtonFactor: String,
        debounceGetItems: Function,
        // balance
        isBalanceMode: Boolean,
        balanceAmount: Number,
    },
    emits: [
        "update:filterInputSearch",
        "update:minPrice",
        "update:maxPrice",
        "update:sort",
        // user
        "update:userFilterInputSearch",
        "update:userMinPrice",
        "update:userMaxPrice",
        "update:userSort",
        "update:selectedSiteItem",
        "update:selectedUserItem",
        "change-page",
        "change-page-user",
        "select-balance-mode",
        "update:balanceAmount",
    ],
    computed: {
        localSort: {
            get() {
                return this.sort;
            },
            set(value) {
                this.$emit("update:sort", value);
            },
        },
        localMinPrice: {
            get() {
                return this.minPrice;
            },
            set(value) {
                this.$emit("update:minPrice", Number(value));
            },
        },
        localMaxPrice: {
            get() {
                return this.maxPrice;
            },
            set(value) {
                this.$emit("update:maxPrice", Number(value));
            },
        },
        localFilter: {
            get() {
                return this.filterInputSearch;
            },
            set(value) {
                this.$emit("update:filterInputSearch", value);
            },
        },
        userLocalSort: {
            get() {
                return this.userSort;
            },
            set(value) {
                this.$emit("update:userSort", value);
            },
        },
        userLocalMinPrice: {
            get() {
                return this.userMinPrice;
            },
            set(value) {
                this.$emit("update:userMinPrice", Number(value));
            },
        },
        userLocalMaxPrice: {
            get() {
                return this.userMaxPrice;
            },
            set(value) {
                this.$emit("update:userMaxPrice", Number(value));
            },
        },
        userLocalFilter: {
            get() {
                return this.userFilterInputSearch;
            },
            set(value) {
                this.$emit("update:userFilterInputSearch", value);
            },
        },
    },
    watch: {
        inventoryButtonFactor: "updatePriceRange",
        selectedUserItem: "updatePriceRange",
    },
    methods: {
        isItemLocked(item) {
            // Если пользователь не выбрал свой предмет, ничего не блокируем
            if (!this.selectedUserItem || !this.selectedUserItem.item) {
                return false;
            }

            // Получаем цену выбранного предмета пользователя
            const userItemPrice = this.selectedUserItem.item.steam_price;
            
            // Предмет заблокирован, если его цена меньше или равна цене предмета пользователя + 1 монета (100 в формате API)
            // Например: если выбран предмет за 100 монет (10000), то блокируются предметы <= 101 монеты (10100)
            return item.steam_price <= userItemPrice + 100;
        },
        updatePriceRange() {
            if (
                !this.selectedUserItem?.item?.steam_price ||
                !this.inventoryButtonFactor
            )
                return;

            const userPrice =
                Number(this.selectedUserItem.item.steam_price) / 100;
            const factor = this.inventoryButtonFactor.toString().trim();

            let min = 0;
            let max = 0;

            if (factor.startsWith("x") || factor.startsWith("х")) {
                // множитель
                const multiplier = parseFloat(factor.slice(1));
                if (!isNaN(multiplier)) {
                    min = userPrice * multiplier;
                    max = min * 1.1;
                }
            } else if (factor.endsWith("%")) {
                // процент
                const percent = parseFloat(factor.slice(0, -1));
                if (!isNaN(percent)) {
                    const target = userPrice * (1 + percent / 100);
                    min = target * 0.9;
                    max = target * 1.1;
                }
            }

            this.localMinPrice = isNaN(min) ? 0 : Number(min.toFixed(2));
            this.localMaxPrice = isNaN(max) ? 0 : Number(max.toFixed(2));

            this.debounceGetItems();
        },
        setSelectedSiteItem(item) {
            // Не позволяем выбрать заблокированный предмет
            if (this.isItemLocked(item)) {
                this.$toastr.error("Этот предмет нельзя выбрать. Выберите предмет дороже!");
                return;
            }
            this.$emit("update:selectedSiteItem", item);
            this.$playSound("/sounds/click.mp3");
        },
        setSelectedUserItem(item) {
            if (this.selectedSiteItem) {
                if ((item.price / this.selectedSiteItem.price) * 100 > 75) {
                    return;
                }
            }
            this.$emit("update:selectedUserItem", item);
            this.$playSound("/sounds/click.mp3");
        },
        userToggleSort() {
            this.userLocalSort = this.userLocalSort === "asc" ? "desc" : "asc";
            this.$playSound("/sounds/click.mp3");
        },
        toggleSort() {
            this.localSort = this.localSort === "asc" ? "desc" : "asc";
            this.$playSound("/sounds/click.mp3");
        },
        getItemRarityClass,
    },
};
</script>

<style scoped>
/* Стили для заблокированных предметов */
.item.locked {
    opacity: 0.5;
    cursor: not-allowed !important;
    pointer-events: none;
    position: relative;
}

.item.locked::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
    border-radius: inherit;
}

/* Замочек */
.item__lock {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
    width: 48px;
    height: 48px;
    background: rgba(0, 0, 0, 0.8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.item__lock .icon {
    width: 24px;
    height: 24px;
    background: #fff;
    mask-size: contain;
    mask-repeat: no-repeat;
    mask-position: center;
}

/* Анимация появления замочка */
@keyframes lockPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
    }
}

.item__lock {
    animation: lockPulse 2s ease-in-out infinite;
}

/* Отключаем ховер для заблокированных предметов */
.item.locked:hover {
    transform: none !important;
    box-shadow: none !important;
}

/* Фикс фильтров на мобилке */
@media (max-width: 768px) {
    .upgrade__skins-wrapp-title {
        display: block !important;
        width: 100% !important;
        margin-bottom: 15px !important;
        font-size: 20px !important;
    }

    .cases__top-right--upgrade {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-bottom: 15px;
        align-items: center;
    }
    
    .upgrade__search {
        width: 100% !important;
        flex: 1 1 100% !important;
        order: 1 !important;
    }

    .upgrade__balance-toggle {
        order: 2 !important;
        width: auto !important;
    }

    .upgrade__cases-price {
        order: 2 !important;
        margin-left: auto !important;
        display: flex !important;
        align-items: center !important;
    }

    .cases__prices {
        width: 100% !important;
        display: flex !important;
        gap: 8px !important;
        order: 3 !important;
        margin-top: 5px !important;
    }

    .cases__prices .form-input__wrapp {
        flex: 1 !important;
        display: block !important;
        min-width: 0 !important;
    }

    .cases__prices input {
        width: 100% !important;
        padding: 8px 12px !important;
        height: 40px !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 8px !important;
        color: #fff !important;
    }
}

/* Стили для выбора баланса */
.upgrade__balance-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.05);
    padding: 8px 16px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.upgrade__balance-toggle.active {
    background: var(--primary-color, #ffb800);
    border-color: var(--primary-color, #ffb800);
    color: #000;
}

.upgrade__balance-toggle.active .icon {
    background: #000;
}

.upgrade__balance-toggle .icon {
    width: 18px;
    height: 18px;
    background: #fff;
}

.upgrade__balance-input-wrap {
    padding: 40px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 250px;
    text-align: center;
}

.upgrade__balance-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #fff;
}

.upgrade__balance-field {
    max-width: 250px;
    width: 100%;
}

.upgrade__balance-hint {
    margin-top: 15px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.4);
}
</style>
