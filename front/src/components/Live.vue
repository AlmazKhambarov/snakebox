<template>
    <div class="header__live">
        <div class="header__live-buttons gpu-boost">
            <button
                type="button"
                @click="tab = 1"
                :class="['header__live-button', { active: tab === 1 }]"
            >
                <div
                    class="icon gray"
                    style="mask-image: url('/images/icons/live-all.svg')"
                ></div>
            </button>
            <button
                type="button"
                @click="tab = 2"
                :class="['header__live-button', { active: tab === 2 }]"
            >
                <div
                    class="icon gray"
                    style="mask-image: url('/images/icons/live-top.svg')"
                ></div>
            </button>
            <div
                class="header__live-button-tab"
                :class="{ active: tab === 2 }"
            ></div>
        </div>
        <template v-if="tab === 1">
            <div class="header__live-wrapp" id="live_all">
                <div
                    class="itemHorizontal__item gpu-boost"
                    v-for="(live, index) in mainStore.lives"
                    :key="index"
                    :class="getItemRarityClass(live.item.rarity)"
                >
                    <router-link :to="{ name: 'OtherProfile', params: { id: live.user.id } }" class="itemHorizontal__user">
                        <img
                            :src="live.user.avatar"
                            :alt="live.user.username"
                        />
                        <span>{{ live.user.username }}</span>
                    </router-link>
                    <router-link
                        :to="getLiveRoute(live)"
                        class="itemHorizontal"
                        :class="getItemRarityClass(live.item.rarity)"
                    >
                        <div class="itemHorizontal__left">
                            <div class="sum sum--xs sum--bgWhite">
                                <div
                                    class="icon gray"
                                    style="
                                        mask-image: url('/assets/icons/coin.svg');
                                    "
                                ></div>
                                {{ live.item.steam_price / 100 }}
                            </div>
                            <div class="itemHorizontal__info">
                                <span>{{ live.item.weapon }}</span>
                                <p>{{ live.item.skin_name }}</p>
                            </div>
                        </div>
                        <div class="itemHorizontal__images">
                            <div class="itemHorizontal__case">
                                <img
                                    v-if="live.from_where === 'CONTRACTS'"
                                    src="/assets/images/contract.png"
                                    alt=""
                                />
                                <img
                                    v-if="live.from_where === 'UPGRADE'"
                                    src="/assets/images/upgrade.png"
                                    alt=""
                                />
                                <img
                                    v-if="live.from_where === 'BOX'"
                                    :src="getImageFromStorage(live.box.image)"
                                    :alt="live.box.name"
                                />
                            </div>
                            <img
                                :src="live.item.image"
                                class="itemHorizontal__weapon"
                                :alt="live.item.weapon + ' | ' + live.item.skin_name"
                            />
                            <div
                                class="icon gray"
                                style="
                                    mask-image: url('/assets/icons/snake.svg');
                                "
                            ></div>
                        </div>
                    </router-link>
                </div>
            </div>
        </template>
        <template v-else-if="tab === 2">
            <div class="header__live-wrapp" id="live_top">
                <div
                    class="itemHorizontal__item gpu-boost"
                    v-for="(live, index) in mainStore.livesBest"
                    :key="index"
                    :class="getItemRarityClass(live.item.rarity)"
                >
                     <router-link :to="{ name: 'OtherProfile', params: { id: live.user.id } }" class="itemHorizontal__user">
                        <img
                            :src="live.user.avatar"
                            :alt="live.user.username"
                        />
                        <span>{{ live.user.username }}</span>
                    </router-link>
                    <router-link
                        :to="getLiveRoute(live)"
                        class="itemHorizontal"
                        :class="getItemRarityClass(live.item.rarity)"
                    >
                        <div class="itemHorizontal__left">
                            <div class="sum sum--xs sum--bgWhite">
                                <div
                                    class="icon gray"
                                    style="
                                        mask-image: url('/assets/icons/coin.svg');
                                    "
                                ></div>
                                {{ live.item.steam_price / 100 }}
                            </div>
                            <div class="itemHorizontal__info">
                                <span>{{ live.item.weapon }}</span>
                                <p>{{ live.item.skin_name }}</p>
                            </div>
                        </div>
                        <div class="itemHorizontal__images">
                            <div class="itemHorizontal__case">
                                <img
                                    v-if="live.from_where === 'CONTRACTS'"
                                    src="/assets/images/contract.png"
                                    alt=""
                                />
                                <img
                                    v-if="live.from_where === 'UPGRADE'"
                                    src="/assets/images/upgrade.png"
                                    alt=""
                                />
                                <img
                                    v-if="live.from_where === 'BOX'"
                                    :src="getImageFromStorage(live.box.image)"
                                    :alt="live.box.name"
                                />
                            </div>
                            <img
                                :src="live.item.image"
                                class="itemHorizontal__weapon"
                                :alt="live.item.weapon + ' | ' + live.item.skin_name"
                            />
                            <div
                                class="icon gray"
                                style="
                                    mask-image: url('/assets/icons/snake.svg');
                                "
                            ></div>
                        </div>
                    </router-link>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import { useMainStore } from "../stores/main.store.js";
import {
    getItemImageUrl,
    getItemName,
    getItemRarityClass,
    getItemType,
} from "../utils/items.js";
import { getImageFromStorage } from "../utils/images.js";

export default {
    inject: ["socket-client"],
    data() {
        return {
            tab: 1,
        };
    },
    computed: {
        mainStore() {
            return useMainStore();
        },
    },
    mounted() {
        this.subscribeSocket();
    },
    methods: {
        getItemImageUrl,
        getItemName,
        getItemRarityClass,
        getItemType,
        getImageFromStorage,
        getLiveRoute(live) {
            switch (live.from_where) {
                case "CONTRACTS":
                    return { name: "contracts" };
                case "UPGRADE":
                    return { name: "upgrade" };
                case "BOX":
                    return { name: "case", params: { url: live.box.url } };
                default:
                    return { name: "index" };
            }
        },
        subscribeSocket() {
            const socket = this["socket-client"];
            if (!socket) return;

            if (!socket.connected) {
                socket.connect();
            }

            socket.on("liveFeed", (data) => {
                this.mainStore.handleLiveFeed(data);
            });
        },
    },
};
</script>
