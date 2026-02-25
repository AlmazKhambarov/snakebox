<template>
    <div class="page giveaway">
        <PageHeader />
        <div class="page__body">
            <LoadingSpinner v-if="isLoading" text="Загрузка розыгрышей..." />
            <div v-else class="giveaway__wrapp">
                <GiveawayCard 
                    type="hourly" 
                    :initial-giveaway="giveaways.hourly"
                    :initial-winners="winners.hourly"
                    @update:giveaway="updateGiveaway('hourly', $event)"
                />
                <GiveawayCard 
                    type="daily" 
                    :initial-giveaway="giveaways.daily"
                    :initial-winners="winners.daily"
                    @update:giveaway="updateGiveaway('daily', $event)"
                />
                <GiveawayCard 
                    type="weekly" 
                    :initial-giveaway="giveaways.weekly"
                    :initial-winners="winners.weekly"
                    @update:giveaway="updateGiveaway('weekly', $event)"
                />
            </div>
        </div>
        
        <GiveawayWinnerAnimation
            v-if="animationData"
            ref="winnerAnimation"
            :type="animationData.type"
            :participants="animationData.participants"
            :winner="animationData.winner"
            :prize="animationData.prize"
            @close="onAnimationClose"
        />
    </div>
</template>
<script>
import { useSeo } from "@/composables/useSeo.js";
import PageHeader from "@/pages/raffle/components/PageHeader.vue";
import GiveawayCard from "@/pages/raffle/components/GiveawayCard.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import GiveawayWinnerAnimation from "@/pages/raffle/components/GiveawayWinnerAnimation.vue";
import { request } from "@/utils/request.js";

export default {
    inject: ["socket-client"],
    components: { PageHeader, GiveawayCard, LoadingSpinner, GiveawayWinnerAnimation },
    
    setup() {
        // Инициализируем SEO для страницы розыгрыша
        const { seoData, updateOpenGraph } = useSeo('raffle');

        return {
            seoData,
            updateOpenGraph
        };
    },

    data() {
        return {
            isLoading: true,
            giveaways: {
                hourly: null,
                daily: null,
                weekly: null
            },
            winners: {
                hourly: [],
                daily: [],
                weekly: []
            },
            animationData: null
        };
    },

    async mounted() {
        await this.loadAllData();
        this.subscribeSocket();
    },

    beforeUnmount() {
        this.unsubscribeSocket();
    },

    methods: {
        async loadAllData() {
            try {
                this.isLoading = true;
                
                // Загружаем розыгрыши и победителей параллельно
                const [giveawaysResponse, winnersResponse] = await Promise.all([
                    request('GET', '/giveaway'),
                    Promise.all([
                        request('GET', '/giveaway/winners', { type: 'hourly', per_page: 5 }),
                        request('GET', '/giveaway/winners', { type: 'daily', per_page: 5 }),
                        request('GET', '/giveaway/winners', { type: 'weekly', per_page: 5 })
                    ])
                ]);

                // Обрабатываем розыгрыши
                if (giveawaysResponse.data.success) {
                    const giveawaysList = giveawaysResponse.data.giveaways;
                    this.giveaways.hourly = giveawaysList.find(g => g.type === 'hourly') || null;
                    this.giveaways.daily = giveawaysList.find(g => g.type === 'daily') || null;
                    this.giveaways.weekly = giveawaysList.find(g => g.type === 'weekly') || null;
                }

                // Обрабатываем победителей
                if (winnersResponse[0].data.success) {
                    this.winners.hourly = winnersResponse[0].data.winners || [];
                }
                if (winnersResponse[1].data.success) {
                    this.winners.daily = winnersResponse[1].data.winners || [];
                }
                if (winnersResponse[2].data.success) {
                    this.winners.weekly = winnersResponse[2].data.winners || [];
                }
            } catch (error) {
                console.error('Error loading giveaway data:', error);
                this.$toastr.error('Ошибка при загрузке розыгрышей');
            } finally {
                this.isLoading = false;
            }
        },

        subscribeSocket() {
            const socket = this["socket-client"];
            if (!socket) return;

            if (!socket.connected) {
                socket.connect();
            }

            socket.on("giveawayFinished", this.handleGiveawayFinished);
        },

        unsubscribeSocket() {
            const socket = this["socket-client"];
            if (socket) {
                socket.off("giveawayFinished", this.handleGiveawayFinished);
            }
        },

        async handleGiveawayFinished(data) {
            console.log('Giveaway finished:', data);
            
            // Проверяем, что данные содержат необходимую информацию
            if (!data.type || !data.winner || !data.participants || !data.prize) {
                console.error('Invalid giveaway data:', data);
                return;
            }

            // Устанавливаем данные для анимации
            this.animationData = {
                type: data.type,
                participants: data.participants,
                winner: data.winner,
                prize: data.prize
            };

            // Показываем анимацию
            this.$nextTick(() => {
                if (this.$refs.winnerAnimation) {
                    this.$refs.winnerAnimation.show();
                }
            });
        },

        async onAnimationClose() {
            // Обнуляем данные анимации
            this.animationData = null;
            
            // Перезагружаем данные розыгрышей
            await this.loadAllData();
            
            this.$toastr.success('Новый розыгрыш начался!');
        },

        updateGiveaway(type, giveaway) {
            this.giveaways[type] = giveaway;
        }
    }
};
</script>
