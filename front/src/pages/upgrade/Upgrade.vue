<template>
    <div
        class="page upgrade"
        :class="state === 'win' ? 'win' : state === 'lose' ? 'lose' : ''"
    >
        <UpgradeHeader />
        <div class="page__body">
            <UpgradeWrap
                :selectedSiteItem="selectedSiteItem"
                :selectedUserItem="selectedUserItem"
                :percent="percent"
                v-model:state="stateActions"
                :refresh="refresh"
                @finish="handleFinish"
                :isBalanceMode="isBalanceMode"
                :balanceAmount="balanceAmount"
            />
            <UpgradeControls
                v-model:inventoryButtonFactor="inventoryButtonFactor"
                :createUpgrade="createUpgrade"
                :state="state"
            />
            <UpgradeSkins
                :siteSkins="siteSkins"
                :totalPages="totalPages"
                :page="page"
                :hasMorePages="hasMorePages"
                v-model:minPrice="minPrice"
                v-model:maxPrice="maxPrice"
                v-model:filterInputSearch="filterInputSearch"
                v-model:sort="sort"
                :selectedSiteItem="selectedSiteItem"
                @update:selectedSiteItem="selectedSiteItem = $event"
                @change-page="siteItems"
                :isSiteLoading="isSiteLoading"
                :userSkins="userSkins"
                :userTotalPages="userTotalPages"
                :userPage="userPage"
                :userHasMorePages="userHasMorePages"
                v-model:userMinPrice="userMinPrice"
                v-model:userMaxPrice="userMaxPrice"
                v-model:userFilterInputSearch="userFilterInputSearch"
                v-model:userSort="userSort"
                :selectedUserItem="selectedUserItem"
                @update:selectedUserItem="selectedUserItem = $event"
                @change-page-user="userItems"
                :isUserLoading="isUserLoading"
                :debounceGetItems="debounceGetItems"
                :inventoryButtonFactor="inventoryButtonFactor"
                v-model:isBalanceMode="isBalanceMode"
                v-model:balanceAmount="balanceAmount"
                @select-balance-mode="toggleBalanceMode"
            />
        </div>
    </div>
</template>
<script>
import UpgradeHeader from "@/pages/upgrade/components/UpgradeHeader.vue";
import UpgradeWrap from "@/pages/upgrade/components/UpgradeWrap.vue";
import UpgradeControls from "@/pages/upgrade/components/UpgradeControls.vue";
import UpgradeSkins from "@/pages/upgrade/components/UpgradeSkins.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import { useSeo } from "@/composables/useSeo.js";

import { request } from "@/utils/request.js";

export default {
    components: { UpgradeHeader, UpgradeWrap, UpgradeControls, UpgradeSkins, LoadingSpinner },
    setup() {
        // Инициализируем SEO для страницы апгрейда
        const { seoData, updateOpenGraph } = useSeo('upgrade');
        
        return {
            seoData,
            updateOpenGraph
        };
    },

    data() {
        return {
            // site items
            siteSkins: [],
            page: 1,
            hasMorePages: true,
            totalPages: 999,

            filterInputSearch: "",

            minPrice: null,
            maxPrice: null,

            sort: "asc",

            selectedSiteItem: null,
            
            isSiteLoading: true,

            // user items

            userSkins: [],
            userPage: 1,
            userHasMorePages: true,
            userTotalPages: 999,

            userFilterInputSearch: "",

            userMinPrice: null,
            userMaxPrice: null,

            selectedUserItem: null,

            userSort: "asc",
            
            isUserLoading: true,

            // other

            inventoryButtonFactor: "x1.5",
            debounceTimer: null,
            state: "default",
            stateActions: "default",
            // balance
            isBalanceMode: false,
            balanceAmount: 0,
        };
    },
    computed: {
        gameChanceData() {
            let gameChance = 0;
            let transformChance = 1;

            if ((this.selectedUserItem || this.isBalanceMode) && this.selectedSiteItem) {
                const userPrice = this.isBalanceMode 
                    ? this.balanceAmount * 100 
                    : this.selectedUserItem.item.steam_price;

                gameChance =
                    (userPrice / this.selectedSiteItem.steam_price) * 100;
                gameChance = Math.max(0.01, Math.min(gameChance, 75));
                transformChance = gameChance / 100;
            }

            return {
                gameChance,
                transformChance,
            };
        },

        percent() {
            return this.gameChanceData.gameChance;
        },
    },
    mounted() {
        this.siteItems();
        this.userItems();
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
        sort() {
            this.siteItems();
        },
        userFilterInputSearch() {
            this.debounceUserGetItems();
        },
        userMinPrice() {
            this.debounceUserGetItems();
        },
        userMaxPrice() {
            this.debounceUserGetItems();
        },
        userSort() {
            this.userItems();
        },
    },
    methods: {
        async siteItems(newPage = null) {
            if (newPage !== null) {
                this.page = newPage;
            }
            this.isSiteLoading = true;
            try {
                await request("GET", "/upgrade/items", {
                    page: this.page,
                    market_name: this.filterInputSearch,
                    min: this.minPrice * 100,
                    max: this.maxPrice * 100,
                    sort: this.sort,
                }).then(({ data }) => {
                    if (!data.success) {
                        return;
                    } else {
                        this.siteSkins = data.items.data;
                        this.hasMorePages = data.hasMorePages;
                        this.totalPages = data.items.last_page;
                    }
                });
            } finally {
                this.isSiteLoading = false;
            }
        },

        async userItems(userNewPage = null) {
            if (userNewPage !== null) {
                this.userPage = userNewPage;
            }
            this.isUserLoading = true;
            try {
                await request("GET", "/upgrade/user/items", {
                    page: this.userPage,
                    market_name: this.userFilterInputSearch,
                    min: this.userMinPrice * 100,
                    max: this.userMaxPrice * 100,
                    sort: this.userSort,
                }).then(({ data }) => {
                    if (!data.success) {
                        return;
                    } else {
                        this.userSkins = data.items.data;
                        this.userHasMorePages = data.hasMorePages;
                        this.userTotalPages = data.items.last_page;
                    }
                });
            } finally {
                this.isUserLoading = false;
            }
        },

        async createUpgrade() {
            if (!this.selectedSiteItem) {
                this.$toastr.error("Выберите предмет для апгрейда");
                return;
            }
            if (!this.selectedUserItem && !this.isBalanceMode) {
                this.$toastr.error("Выберите свой предмет или используйте баланс");
                return;
            }
            if (this.isBalanceMode && this.balanceAmount <= 0) {
                this.$toastr.error("Введите корректную сумму");
                return;
            }

            await request("post", "/upgrade/create", {
                userItem: this.isBalanceMode ? null : this.selectedUserItem.id,
                siteItem: this.selectedSiteItem.id,
                balance_amount: this.isBalanceMode ? this.balanceAmount * 100 : null,
            }).then(({ data }) => {
                if (data.status !== 200) {
                    this.$toastr.error(data.message);
                } else {
                    this.$playSound("/sounds/upgrade-start.mp3");
                    // Сначала сбрасываем состояние, чтобы watch точно сработал
                    this.stateActions = "default";

                    // Через микротаймер обновляем на новое значение
                    setTimeout(() => {
                        if (data.isUpgrade) {
                            this.stateActions = "win";
                        } else {
                            this.stateActions = "lose";
                        }
                    }, 50); // 50 мс — достаточно, чтобы Vue заметил изменение
                }
            });
        },

        refresh() {
            this.selectedUserItem = null;
            this.selectedSiteItem = null;
            this.balanceAmount = 0;
            this.stateActions = "default";
            this.userItems();
        },
        handleFinish(result) {
            this.state = result; // или this.finalResult = result;
        },
        debounceUserGetItems() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.userItems();
            }, 600);
        },
        debounceGetItems() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.siteItems();
            }, 600);
        },
        toggleBalanceMode() {
            this.isBalanceMode = !this.isBalanceMode;
            if (this.isBalanceMode) {
                this.selectedUserItem = null;
            }
            this.$playSound("/sounds/click.mp3");
        },
    },
};
</script>
