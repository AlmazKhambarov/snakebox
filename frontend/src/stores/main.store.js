import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { request } from "../utils/request.js";

export const useMainStore = defineStore("mainStore", () => {
    const livesList = ref([]);
    const livesListBest = ref([]);
    const promocode = ref(null);

    const settingsObject = ref({});

    const sitenameObject = ref({
        first: "",
        second: "",
    });

    const lives = computed(() => livesList.value);
    const livesBest = computed(() => livesListBest.value);
    const settings = computed(() => settingsObject.value);
    const promo = computed(() => promocode.value);
    const sitename = computed(() => sitenameObject.value);

    async function getSettings() {
        return request("GET", "/settings").then(({ data }) => {
            settingsObject.value = data.settings || {};
            return data.settings;
        });
    }
    async function getInformation() {
        request("GET", "/main").then(({ data }) => {

            livesList.value = data.lives.all;
            livesListBest.value = data.lives.best;
        });
    }

    const addToLives = (items) => {
        if (livesList.value.length > 25) {
            livesList.value.pop();
        }

        items.forEach((item) => {
            if (livesList.value.length >= 25) {
                livesList.value.pop();
            }

            livesList.value.unshift(item);
        });
    };

    // Новый метод для обработки данных из сокета
    const handleLiveFeed = (data) => {

        if (data.items && Array.isArray(data.items)) {
            data.items.forEach((item) => {
                // Определяем задержку в зависимости от типа
                const delays = {
                    BOX: 9500,
                    UPGRADE: 5500,
                    CONTRACTS: 0,
                };

                const delay = delays[item.from_where] || 0;

                const processItem = () => {
                    // Добавляем в основную ленту
                    addToLives([item]);

                    // Если это лучший предмет, добавляем в лучшие
                    if (item.item?.steam_price >= 100000) {
                        if (livesListBest.value.length >= 20) {
                            livesListBest.value.pop();
                        }
                        livesListBest.value.unshift(item);

                        // Сортируем лучшие предметы по цене
                        livesListBest.value.sort(
                            (a, b) =>
                                (b.item?.steam_price || 0) -
                                (a.item?.steam_price || 0)
                        );

                        // Ограничиваем до 20 элементов
                        if (livesListBest.value.length > 20) {
                            livesListBest.value = livesListBest.value.slice(
                                0,
                                20
                            );
                        }
                    }
                };

                if (delay > 0) {
                    setTimeout(processItem, delay);
                } else {
                    processItem();
                }
            });
        }
    };

    return {
        lives,
        livesBest,
        settings,
        promo,
        sitename,
        getSettings,
        getInformation,
        addToLives,
        handleLiveFeed, // Экспортируем новый метод
    };
});
