<template>
    <footer class="footer">
        <div class="wrapper">
            <div class="footer__wrapp">
                <div class="footer__left">
                    <div class="footer__top">
                        <div class="footer__top-left">
                             <router-link
                        :to="{ name: 'index' }"
                        class="header__logotype"
                    >
                            <img src="/dist/assets/icons/logonew.svg" alt="" />
                        <!-- <span>{{ settings.site_name }}</span> -->
                    </router-link>
                            <nav class="header__nav gpu-boost">
                                <li>
                            <router-link :to="{ name: 'event' }" class="event">
                                <div
                                    class="icon gold"
                                    style="
                                        mask-image: url('/assets/icons/event.svg');
                                    "
                                ></div>
                                События
                            </router-link>
                        </li>
                                <li>
                                    <router-link :to="{ name: 'bonus' }">
                                        <div
                                            class="icon gray"
                                            style="
                                                mask-image: url('/images/icons/bonus.svg');
                                            "
                                        ></div>
                                        Бонусы
                                    </router-link>
                                </li>
                                <li>
                                    <router-link :to="{ name: 'referrals' }">
                                        <div
                                            class="icon gray"
                                            style="
                                                mask-image: url('/images/icons/refferal.svg');
                                            "
                                        ></div>
                                        Партнёрство
                                    </router-link>
                                </li>
                                <li>
                                    <router-link
                                        :to="{ name: 'vip' }"
                                        class="vip"
                                    >
                                        <div
                                            class="icon gray"
                                            style="
                                                mask-image: url('/images/icons/vip.svg');
                                            "
                                        ></div>
                                        Вип-клуб
                                    </router-link>
                                </li>
                               
                            </nav>
                        </div>
                        <div class="footer__top-right">
                            <div class="socials gpu-boost">
                                <a :href="settings.vk_group" target="_blank">
                                    <div
                                        class="icon gray"
                                        style="
                                            mask-image: url('/images/icons/vk.svg');
                                        "
                                    ></div>
                                </a>
                                <a :href="settings.tg_group" target="_blank">
                                    <div
                                        class="icon gray"
                                        style="
                                            mask-image: url('/images/icons/telegram.svg');
                                        "
                                    ></div>
                                </a>
                            </div>
                            <div class="cases__select-game">
                                <button
                                    type="button"
                                    class="cases__select-game-btn active"
                                    data-game="0"
                                >
                                    <div
                                        class="icon"
                                        style="
                                            mask-image: url('/images/icons/cs2.svg');
                                        "
                                    ></div>
                                </button>
                                <!-- <button
                                    type="button"
                                    class="cases__select-game-btn"
                                    data-game="1"
                                >
                                    <div
                                        class="icon"
                                        style="
                                            mask-image: url('/images/icons/dota2.svg');
                                        "
                                    ></div>
                                </button>
                                <button
                                    type="button"
                                    class="cases__select-game-btn"
                                    data-game="2"
                                >
                                    <div
                                        class="icon"
                                        style="
                                            mask-image: url('/images/icons/rust.svg');
                                        "
                                    ></div>
                                </button> -->
                            </div>
                            <!-- <div class="dropdown dropdown--reverse gpu-boost" x-data="{ show: false }" @click.outside="show = false" x-bind:class="show && 'active'">
							<button @click="show = !show" type="button" class="header__language dropdown__btn--small">
								<img src="//images/flags/ru.svg" alt="" />
								<span>Русский</span>
								<i></i>
							</button>
							<div class="dropdown__inner">
								<a @click="show = false" href="javascript:;" class="dropdown__link">
									<img src="//images/flags/en.svg" alt="" />
									<span>English</span>
								</a>
							</div>
						</div> -->
                        </div>
                    </div>
                    <div class="footer__bottom">
                        <div class="footer__stats">
                            <div class="footer__stats-item">
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/online.svg');
                                    "
                                ></div>
                                <div class="footer__stats-item-info">
                                    <span class="online">{{
                                        currentOnline
                                    }}</span>
                                    <p>онлайн</p>
                                </div>
                            </div>
                            <div class="footer__stats-item">
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/game.svg');
                                    "
                                ></div>
                                <div class="footer__stats-item-info">
                                    <span>{{ stats.total_games }}</span>
                                    <p>всего игр</p>
                                </div>
                            </div>
                            <div class="footer__stats-item">
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/users.svg');
                                    "
                                ></div>
                                <div class="footer__stats-item-info">
                                    <span>{{ stats.players }}</span>
                                    <p>игроков</p>
                                </div>
                            </div>
                            <div class="footer__stats-item">
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/skins.svg');
                                    "
                                ></div>
                                <div class="footer__stats-item-info">
                                    <span>{{ stats.withdraws }}</span>
                                    <p>выведено скинов</p>
                                </div>
                            </div>
                            <div class="footer__stats-before"></div>
                            <div class="footer__stats-after"></div>
                        </div>
                        <div class="footer__age">
                            <div class="footer__age-item">
                                <div
                                    class="icon"
                                    style="
                                        mask-image: url('/images/icons/18+.svg');
                                    "
                                ></div>
                                <p>
                                    Заходя на этот сайт, вы подтверждаете, что
                                    вам исполнилось 18 лет.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <router-link
                    :to="{ name: 'event' }"
                    class="footer__right gpu-boost"
                >
                  
                  
                    <img
                        src="/assets/images/footer-event.png"
                        class="footer__right-image"
                        alt=""
                    />
                </router-link>
            </div>
            <div class="footer__links gpu-boost">
                <div class="footer__links-left">
                    <span>© {{ settings.site_name }} – 2025</span>
                    <a href="javascript:;">Обратиться за помощью</a>
                </div>
                <div class="footer__links-right">
                    <div class="footer__links-right">
                        <router-link :to="{ name: 'terms' }"
                            >Условия обслуживания</router-link
                        >
                        <router-link :to="{ name: 'policy' }"
                            >Политика конфиденциальности</router-link
                        >
                    </div>
                </div>
            </div>
            <!-- <span style="font-size: 11px; color: var(--gray)"
                >International Business Systems S.R.L. Reg. number:
                3-102-693823. Beneficiary’s address:San Jose-Santa Ana,Three
                Hundred And Fifty Meters Of The Restaurant Ceviche Del Rey,
                Costa-Rica</span
            > -->
        </div>
    </footer>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useMainStore } from "@/stores/main.store.js";
export default {
    props: { user: Object, stats: Object, currentOnline: Number },
    computed: {
        ...mapState(useMainStore, ["settings"]),
    },
    methods: {
        ...mapActions(useMainStore, ["getSettings"]),
    },
};
</script>
