<template>
    <div class="page event">
        <div class="page__header">
            <div class="page__header-left">
                <router-link :to="{ name: 'index' }" class="page__header-back">
                    <div class="icon" style="mask-image: url('/images/icons/arrow-left.svg')"></div>
                </router-link>
                <div class="page__header-info">
                    <div class="page__header-info-inner">
                        <span>{{ name }}</span>
                        <p>Активный ивент</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="page__body">
            <div class="event__top">
                <div class="banners__event-image">
                    <div class="banners__event-overlay eventTimer">
                        <h2>{{ name }}</h2>
                        <div class="banners__event-timer">
                            <div class="banners__event-timer-item">
                                <span class="timer-days">{{
                                    formatTime(days)
                                    }}</span>
                                <p>дни</p>
                            </div>
                            <div class="banners__event-timer-item">
                                <span class="timer-hours">{{
                                    formatTime(hours)
                                    }}</span>
                                <p>часы</p>
                            </div>
                            <div class="banners__event-timer-item">
                                <span class="timer-minutes">{{
                                    formatTime(minutes)
                                    }}</span>
                                <p>мин</p>
                            </div>
                            <div class="banners__event-timer-item">
                                <span class="timer-seconds">{{
                                    formatTime(seconds)
                                    }}</span>
                                <p>сек</p>
                            </div>
                        </div>
                    </div>
                    <img src="/assets/images/event.png" class="banners__event-image-bg" alt="Фон события и турнира" />
                </div>
                <div class="event__top-info">
                    <div class="event__course">
                        <div class="event__course-your">
                            <span>У вас</span>
                            <div class="sum">
                                <div class="icon energy" style="
                                        mask-image: url('/assets/icons/points.svg');
                                    "></div>
                                <span>{{ user.event_points / 100 }}</span>
                            </div>
                        </div>
                        <div class="event__course-calc tooltip" data-tippy-content="Текущий курс">
                            <div class="sum">
                                <div class="icon coin" style="
                                        mask-image: url('/assets/icons/coin.svg');
                                    "></div>
                                <span>100</span>
                            </div>
                            <span>=</span>
                            <div class="sum">
                                <div class="icon energy" style="
                                        mask-image: url('/assets/icons/points.svg');
                                    "></div>
                                <span>10</span>
                            </div>
                        </div>
                        <a href="#who-job-event" class="event__who-btn" data-fancybox>Как это работает?</a>
                    </div>
                </div>
            </div>
            <div class="event_line"></div>
            <div v-if="topThree.length > 0" class="leaderboard__prize-places flex justify-center items-end gap-15">
                <!-- 2 место -->
                <div v-if="topThree[1]">
                    <div class="flex flex-col items-center place place_2">
                        <div class="place__circle relative flex flex-col items-center">
                            <router-link :to="{ name: 'OtherProfile', params: { id: topThree[1].id } }"
                                class="place__avatar absolute rounded-lg flex items-center justify-center text-5xl is-user">
                                <img :src="topThree[1].avatar" alt="" loading="lazy" style="
                                        position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        inset: 0px;
                                    " />
                            </router-link>
                            <div v-if="topThree[1].prize_item" class="place__skin absolute">
                                <img :src="topThree[1].prize_item.image" alt="" loading="lazy" style="
                                        position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        inset: 0px;
                                    " />
                            </div>
                            <div
                                class="place__number flex items-center justify-center absolute text-xl font-bold md:text-base">
                                {{ topThree[1].position }}
                            </div>
                        </div>
                        <div v-if="topThree[1].prize_item" class="place__amount gap-1 mt-12 currency currency_USD currency_anim">
                            {{ topThree[1].prize_item.steam_price / 100 }}
                        </div>
                        <div v-else class="place__amount gap-1 mt-12">
                            —
                        </div>
                        <div class="place__nickname text-lg  lh-10 md:hidden">
                            {{ topThree[1].user_name }}
                        </div>
                        <div class="place__points flex items-center gap-2 text-md font-semi-bold">
                            <div name="points" class="inline-icon" style="
                                    mask-image: url('/assets/icons/points.svg');
                                "></div>
                            <span>{{ topThree[1].points / 100 }}</span>
                        </div>
                    </div>
                </div>
                <!-- 1 место -->
                <div v-if="topThree[0]">
                    <div class="flex flex-col items-center place place_1">
                        <div class="place__circle relative flex flex-col items-center">
                            <div class="place__crown absolute">
                                <img src="/assets/images/leaderboards/crown.webp" alt="" loading="lazy" style="
                                        position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        inset: 0px;
                                    " />
                            </div>
                            <router-link :to="{ name: 'OtherProfile', params: { id: topThree[0].id } }"
                                class="place__avatar absolute rounded-lg flex items-center justify-center text-5xl is-user">
                                <img :src="topThree[0].avatar" alt="" loading="lazy" style="
                                        position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        inset: 0px;
                                    " />
                            </router-link>
                            <div v-if="topThree[0].prize_item" class="place__skin absolute">
                                <img :src="topThree[0].prize_item.image" alt="" loading="lazy" style="
                                        position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        inset: 0px;
                                    " />
                            </div>
                            <div
                                class="place__number flex items-center justify-center absolute text-xl font-bold md:text-base">
                                {{ topThree[0].position }}
                            </div>
                        </div>
                        <div v-if="topThree[0].prize_item" class="place__amount gap-1 mt-12 currency currency_USD currency_anim">
                            {{ topThree[0].prize_item.steam_price / 100 }}
                        </div>
                        <div v-else class="place__amount gap-1 mt-12">
                            —
                        </div>
                        <div class="place__nickname text-lg  lh-10 md:hidden">
                            {{ topThree[0].user_name }}
                        </div>
                        <div class="place__points flex items-center gap-2 text-md font-semi-bold">
                            <div name="points" class="inline-icon" style="
                                    mask-image: url('/assets/icons/points.svg');
                                "></div>
                            <span>{{ topThree[0].points / 100 }}</span>
                        </div>
                    </div>
                </div>
                <!-- 3 место -->
                <div v-if="topThree[2]">
                    <div class="flex flex-col items-center place place_3">
                        <div class="place__circle relative flex flex-col items-center">
                            <router-link :to="{ name: 'OtherProfile', params: { id: topThree[2].id } }"
                                class="place__avatar absolute rounded-lg flex items-center justify-center text-5xl is-user">
                                <img :src="topThree[2].avatar" alt="" loading="lazy" style="
                                        position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        inset: 0px;
                                    " />
                            </router-link>
                            <div v-if="topThree[2].prize_item" class="place__skin absolute">
                                <img :src="topThree[2].prize_item.image" alt="" loading="lazy" style="
                                        position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        inset: 0px;
                                    " />
                            </div>
                            <div
                                class="place__number flex items-center justify-center absolute text-xl font-bold md:text-base">
                                {{ topThree[2].position }}
                            </div>
                        </div>
                        <div v-if="topThree[2].prize_item" class="place__amount gap-1 mt-12 currency currency_USD currency_anim">
                            {{ topThree[2].prize_item.steam_price / 100 }}
                        </div>
                        <div v-else class="place__amount gap-1 mt-12">
                            —
                        </div>
                        <div class="place__nickname text-lg  lh-10 md:hidden">
                            {{ topThree[2].user_name }}
                        </div>
                        <div class="place__points flex items-center gap-2 text-md font-semi-bold">
                            <div name="points" class="inline-icon" style="
                                    mask-image: url('/assets/icons/points.svg');
                                "></div>
                            <span>{{ topThree[2].points / 100 }}</span>
                        </div>
                    </div>
                </div>
                <img src="/assets/images/top-event-bg.png" alt="line" class="event_bg_img"></img>
            </div>
            <div class="event__leaderboard">
                <div class="title">
                    <span>Таблица лидеров</span>
                </div>



                <div class="event__leaderboard-table">
                    <table>
                        <thead>
                            <tr>
                                <td>Место</td>
                                <td>Игрок</td>
                                <td>Кол-во очков</td>
                                <td>Награда</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in restLeaderboard" :key="index">
                                <td>
                                    <div class="sum">
                                        <span>{{ user.position }}</span>
                                    </div>
                                </td>
                                <td>
                                    <router-link :to="{ name: 'OtherProfile', params: { id: user.id } }" class="event__leaderboard-user">
                                        <img :src="user.avatar" alt="" />
                                        <span>{{ user.user_name }}</span>
                                    </router-link>
                                </td>
                                <td>
                                    <div class="sum">
                                        <div class="icon energy" style="
                                                mask-image: url('/assets/icons/points.svg');
                                            "></div>
                                        <span>{{ user.points / 100 }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="user.prize_item" class="event__prize-item">
                                        <div class="event__prize-item-wrapper" :class="getItemRarityClass(
                                            user.prize_item.rarity
                                        )
                                            ">
                                            <img :src="user.prize_item.image" :alt="user.prize_item.title"
                                                class="event__prize-item-image" />
                                            <img :src="`/images/case/shadow-${getItemRarityClass(
                                                user.prize_item.rarity
                                            )}-circle.png`" class="event__prize-item-shadow" alt="rarity" />
                                        </div>
                                    </div>
                                    <div v-else class="event__prize-empty">
                                        <span>—</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { request } from "@/utils/request.js";
import { useSeo } from "@/composables/useSeo.js";
import { getItemRarityClass } from "@/helpers/helpers.js";

export default {
    setup() {
        // Инициализируем SEO для страницы событий
        const { seoData, updateOpenGraph } = useSeo("event");

        return {
            seoData,
            updateOpenGraph,
        };
    },
    data() {
        return {
            days: 0,
            hours: 0,
            minutes: 0,
            seconds: 0,
            countdownInterval: null,
            endDate: null,
            name: "",
            leaderboard: [],
        };
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
        // Первые 3 места для отдельного отображения
        topThree() {
            return this.leaderboard.filter(user => user.position <= 3).sort((a, b) => a.position - b.position);
        },
        // Остальные места начиная с 4-го для таблицы
        restLeaderboard() {
            return this.leaderboard.filter(user => user.position >= 4);
        },
    },
    mounted() {
        this.getEvent();
    },
    beforeUnmount() {
        // Очищаем интервал при уничтожении компонента
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
    },
    methods: {
        async getEvent() {
            await request("GET", "/event/get").then(({ data }) => {
                if (!data.success) {
                    console.warn("event error", data);
                } else {
                    this.name = data.event.name;
                    this.leaderboard = data.leaderboard;
                    this.startCountdown(data.event.end_date);
                }
            });
        },

        startCountdown(endDate) {
            this.endDate = new Date(endDate).getTime();

            // Запускаем обновление каждую секунду
            this.countdownInterval = setInterval(() => {
                this.updateCountdown();
            }, 1000);

            // Сразу обновляем таймер
            this.updateCountdown();
        },

        updateCountdown() {
            const now = new Date().getTime();
            const distance = this.endDate - now;

            if (distance < 0) {
                // Время истекло
                this.days = 0;
                this.hours = 0;
                this.minutes = 0;
                this.seconds = 0;
                clearInterval(this.countdownInterval);
                return;
            }

            // Рассчитываем дни, часы, минуты, секунды
            this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
            this.hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            this.minutes = Math.floor(
                (distance % (1000 * 60 * 60)) / (1000 * 60)
            );
            this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
        },

        formatTime(time) {
            // Добавляем ведущий ноль если число меньше 10
            return time < 10 ? `0${time}` : time;
        },
        getItemRarityClass,
    },
};
</script>

<style scoped>
.event__prize-item {
    display: flex;
    align-items: center;
    justify-content: center;
}

.event__prize-item-wrapper {
    position: relative;
    width: 60px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.event__prize-item-image {
    width: 50px;
    height: 35px;
    object-fit: contain;
    z-index: 1;
    position: relative;
}

.event__prize-item-shadow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    opacity: 0.8;
}

.event__prize-empty {
    text-align: center;
    color: #999;
}

.event__prize-item-wrapper.consumer {
    filter: drop-shadow(0 0 5px rgba(176, 195, 217, 0.5));
}

.event__prize-item-wrapper.industrial {
    filter: drop-shadow(0 0 5px rgba(94, 152, 217, 0.5));
}

.event__prize-item-wrapper.milspec {
    filter: drop-shadow(0 0 5px rgba(75, 105, 255, 0.5));
}

.event__prize-item-wrapper.restricted {
    filter: drop-shadow(0 0 5px rgba(136, 71, 255, 0.5));
}

.event__prize-item-wrapper.classified {
    filter: drop-shadow(0 0 5px rgba(211, 44, 230, 0.5));
}

.event__prize-item-wrapper.covert {
    filter: drop-shadow(0 0 5px rgba(235, 75, 75, 0.5));
}

.event__prize-item-wrapper.rare {
    filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.5));
}
</style>
