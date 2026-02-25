<template>
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
                                        @click="copyId"
                                        type="button"
                                        class="copyUserId"
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
                        <div class="profile__user-buttons">
                            <router-link
                                to="/deposit"
                                class="profile__user-deposit-btn"
                            >
                                Пополнить
                            </router-link>
                            <button @click="logOut" class="btn-border">
                                <span>Выйти</span>
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/arrow-right.svg');
                                    "
                                ></div>
                            </button>
                        </div>
                    </div>
                    <router-link :to="{ name: 'vip' }" class="profile__level">
                        <div class="profile__level-top">
                            <div class="profile__level-name">
                                <img
                                    v-if="user.is_vip"
                                    src="/assets/images/vipclub/none.svg"
                                    alt="Нет ранга"
                                />
                                <span>{{user.is_vip ? 'VIP' : 'Нет ранга'}}</span>
                            </div>
                            <div class="profile__level-next">
                                До след. уровня <span>20,000XP</span>
                            </div>
                        </div>
                        <img
                            src="/assets/images/crown.png"
                            class="profile__level-image"
                            alt=""
                        />
                        <div
                            class="icon profile__level-arrow"
                            style="
                                mask-image: url('/assets/icons/arrow-top-right.svg');
                            "
                        ></div>
                    </router-link>
                </div>
                <div class="profile__left-grid">
                    <div class="bonus__task">
                        <div class="bonus__task-left">
                            <div class="sum">
                                <div
                                    class="icon coin"
                                    style="
                                        mask-image: url('/assets/icons/coin.svg');
                                    "
                                ></div>
                                <span>{{ user.balance / 100 }}</span>
                            </div>
                            <div class="bonus__task-title">Текущий баланс</div>
                        </div>
                        <div class="bonus__task-image wallet">
                            <img src="/assets/images/wallet.png" alt="" />
                        </div>
                    </div>
                    <div class="bonus__task">
                        <div class="bonus__task-left">
                            <div class="sum">
                                <div
                                    class="icon coin"
                                    style="
                                        mask-image: url('/images/icons/game.svg');
                                    "
                                ></div>
                                <span>{{ user.lives_count }}</span>
                            </div>
                            <div class="bonus__task-title">Игр сыграно</div>
                        </div>
                        <div class="bonus__task-image games">
                            <img src="/assets/images/games.png" alt="" />
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
                                <span>{{ user.total_bet / 100 }}</span>
                            </div>
                            <div class="bonus__task-title">Сумма ставок</div>
                        </div>
                        <div class="bonus__task-image case3d">
                            <img src="/assets/images/case3d.png" alt="" />
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
                                    (user.top_drop?.steam_price || 0) / 100
                                }}</span>
                            </div>
                            <div class="bonus__task-title">Топ выигрыш</div>
                        </div>
                        <div class="bonus__task-image top-win">
                            <img src="/assets/images/top-win.png" alt="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile__right">
                <div
                    class="profile__refferal"
                
                >
                    <div class="banners__list-item-top gray">
                        <p>Ваш прогресс</p>
                        <span>Реферальная система</span>
                    </div>
                    <div class="profile__refferal-progress">
                        <div
                            class="refferal__level-item-progress"
                            x-ref="progress"
                        >
                            <div class="refferal__level-item-number">
                                {{ summary.current_level }}
                            </div>
                            <svg
                                viewBox="0 0 100 100"
                                style="stroke-linecap: round"
                            >
                                <path
                                    d="M 50,50 m 0,-47 a 47,47 0 1 1 0,94 a 47,47 0 1 1 0,-94"
                                    stroke="var(--button-color)"
                                    stroke-width="2"
                                    fill-opacity="0"
                                ></path>
                                <path
                                    d="M 50,50 m 0,-47 a 47,47 0 1 1 0,94 a 47,47 0 1 1 0,-94"
                                    stroke="var(--button-color)"
                                    stroke-width="6"
                                    fill-opacity="0"
                                    style="
                                        stroke-dasharray: 295.416, 295.416;
                                        stroke-dashoffset: 295.416;
                                    "
                                ></path>
                            </svg>
                        </div>
                        <div class="checkbox__button-desc">
                            <span>Текущий уровень</span>
                            <p>
                                До след.
                                {{ summary.next_level_requirement / 100 }}
                            </p>
                        </div>
                    </div>
                    <div class="profile__inner">
                        <div class="profile__grid-inner">
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
                                            summary.total_earned / 100
                                        }}</span>
                                    </div>
                                    <div class="bonus__task-title">
                                        Заработано
                                    </div>
                                </div>
                            </div>
                            <div class="bonus__task">
                                <div class="bonus__task-left">
                                    <div class="sum">
                                        <div
                                            class="icon energy"
                                            style="
                                                mask-image: url('/images/icons/users.svg');
                                            "
                                        ></div>
                                        <span>{{
                                            summary.referrals_count
                                        }}</span>
                                    </div>
                                    <div class="bonus__task-title">
                                        Рефералов
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <router-link :to="{ name: 'referrals' }" class="btn-border">
                        <span>Подробнее</span>
                        <div
                            class="icon"
                            style="
                                mask-image: url('/images/icons/arrow-right.svg');
                            "
                        ></div>
                    </router-link>
                </div>
                <div class="profile__skins">
                    <div class="profile__skins-item top">
                        <span>Топ дроп</span>
                        <div
                            class="profile__skins-item-skin-wrapp"
                            v-if="user.top_drop === null"
                        >
                            <div class="item__center">
                                <div class="empty-item">Нет предмета</div>
                            </div>
                        </div>
                        <div v-else class="profile__skins-item-skin-wrapp">
                            <div class="item__top">
                                <div class="sum sum--xs sum--bgWhite">
                                    <div
                                        class="icon"
                                        style="
                                            mask-image: url('/assets/icons/coin.svg');
                                        "
                                    ></div>
                                    {{
                                        (user.top_drop?.steam_price || 0) / 100
                                    }}
                                </div>
                            </div>
                            <div class="item__center">
                                <img
                                    :src="user.top_drop?.image || ''"
                                    class="item__image"
                                    :alt="user.top_drop?.title || 'skin'"
                                />

                                <img
                                    :src="`/images/case/shadow-${getItemRarityClass(
                                        user.top_drop?.rarity || 'consumer'
                                    )}-circle.png`"
                                    class="item__rarity-img"
                                    alt="rarity circle"
                                />
                            </div>
                            <div class="item__bottom">
                                <div class="item__model">
                                    {{ user.top_drop?.weapon || "weapon" }}
                                </div>
                                <div class="item__name">
                                    {{
                                        user.top_drop?.skin_name || "skin_name"
                                    }}
                                </div>
                                <div class="item__quality">
                                    {{ user.top_drop?.quality || "quality" }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile__skins-item love">
                        <span>Любимый кейс</span>
                        <div
                            v-if="user.favorite_box === null"
                            class="profile__skins-item-skin-wrapp no-favorite-case"
                        >
                            <div class="item__center">
                                <div class="no-case-text">
                                    Еще не открывали кейсы
                                </div>
                            </div>
                        </div>
                        <router-link
                            v-if="user.favorite_box"
                            :to="{
                                name: 'case',
                                params: { url: user.favorite_box?.url || '' },
                            }"
                            class="profile__skins-item-skin-wrapp"
                        >
                            <div class="item__center">
                                <img
                                    :src="user.favorite_box?.image || ''"
                                    class="item__image"
                                    :alt="user.favorite_box?.name || 'case'"
                                />
                                <div
                                    class="icon item__center-snake"
                                    style="
                                        mask-image: url('/assets/icons/snake.svg');
                                    "
                                ></div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile__trade-link">
            <div class="profile__trade-link-inner">
                <div class="profile__trade-link-head">
                    <span>Укажите ссылку на трейд</span>
                    <a
                        href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url"
                        target="_blank"
                        >Где получить?</a
                    >
                </div>
                <div class="form-input">
                    <div class="form-input__wrapp">
                        <div class="form-input__button">
                            <button
                                @click="saveTradeLink"
                                type="button"
                                class="page__header-back saveTrade"
                            >
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/check.svg');
                                    "
                                ></div>
                            </button>
                        </div>
                        <input
                            v-model="tradeLink"
                            type="text"
                            placeholder="Ссылка на трейд"
                            class="tradeUrl"
                        />
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="profile__settings">
            <div class="profile__trade-link-head">
                <span>Настройки</span>
            </div>

            <div class="profile__settings-list">
                <div class="bonus__task">
                    <div class="bonus__task-left">
                        <div class="bonus__task-title">Привязать Telegram</div>
                        <div class="bonus__task-bottom">
                            <iframe
                                id="telegram-login-Dolphinsite_bot"
                                src="https://oauth.telegram.org/embed/Dolphinsite_bot?origin=https%3A%2F%2Fdolphin.win&amp;return_to=https%3A%2F%2Fdolphin.win%2Fprofile&amp;size=large&amp;userpic=false&amp;request_access=write"
                                width="238"
                                height="40"
                                frameborder="0"
                                scrolling="no"
                                style="
                                    overflow: hidden;
                                    color-scheme: light dark;
                                    border: none;
                                    height: 40px;
                                    width: 234px;
                                "
                            ></iframe>
                            <script
                                async=""
                                src="https://telegram.org/js/telegram-widget.js"
                                data-telegram-login="Dolphinsite_bot"
                                data-size="large"
                                data-userpic="false"
                                data-auth-url="https://dolphin.win/auth/telegram/callback"
                                data-request-access="write"
                            ></script>
                        </div>
                    </div>
                    <div class="bonus__task-image">
                        <img src="/images/telegram.png" alt="" />
                    </div>
                </div>
                <div class="bonus__task">
                    <div class="bonus__task-left">
                        <div class="bonus__task-title">Привязать ВКонтакте</div>
                        <div class="bonus__task-bottom">
                            <a
                                href="/auth/vkontakte"
                                class="bonus__task-button"
                            >
                                <span>Привязать</span>
                            </a>
                        </div>
                    </div>
                    <div class="bonus__task-image">
                        <img src="/images/vk.png" alt="" />
                    </div>
                </div>
                <div class="bonus__task linked">
                    <div class="bonus__task-left">
                        <div class="bonus__task-title">Привязать Steam</div>
                        <div class="bonus__task-bottom">
                            <button class="bonus__task-button">
                                <span>Привязан</span>
                            </button>
                        </div>
                    </div>
                    <div class="bonus__task-image">
                        <img src="/images/steam.png" alt="" />
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</template>

<script>
import { request } from "@/utils/request.js";
import { getItemRarityClass } from "../../../helpers/helpers";

export default {
    props: { user: Object, logOut: Function },
    data() {
        return {
            summary: {
                referral_code: "default",
                referral_link: "default",
                current_level: 1,
                level_percentage: 0,
                next_level_requirement: 0,
                total_earned: 0,
                referral_balance: 0,
                referrals_count: 0,
                total_deposited: 0,
                bonus_per_referral: 25,
            },
        };
    },
    computed: {
        tradeLink: {
            get() {
                return this.user?.tradeLink || "";
            },
            set(value) {
                this.user.tradeLink = value;
            },
        },
    },
    mounted() {
        this.loadSummary();
    },
    methods: {
        copyId() {
            const input = this.$refs.copyInput;
            if (!input) return;
            input.select();
            navigator.clipboard.writeText(input.value);
             this.$toastr.success("ID скопирован в буфер обмена");
        },
        async saveTradeLink() {
            await request("POST", "/user/trade-link", {
                link: this.tradeLink,
            }).then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                } else {
                    // обновляем tradeLink через computed set
                    this.tradeLink = data.link;
                    this.$toastr.success(data.message);
                }
            });
        },
        async loadSummary() {
            try {
                const { data } = await request("GET", "/referral/summary");
                if (data.success) {
                    this.summary = data.summary;
                }
            } catch (error) {
                console.error("Failed to load referral summary:", error);
            }
        },
        getItemRarityClass,
    },
};
</script>
