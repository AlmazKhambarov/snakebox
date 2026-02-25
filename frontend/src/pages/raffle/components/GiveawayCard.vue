<template>
  <div class="giveaway__wrapp-item" v-if="giveaway">
    <div class="giveaway__wrapp-item-top">
      <div class="giveaway__wrapp-item-skin" :class="rarityClass">
        <div class="giveaway__wrapp-item-top-left">
          <div class="giveaway__wrapp-item-skin-left">
            <span>{{ giveaway.item.weapon }} | {{ giveaway.item.skin_name }}</span>
            <p>{{ giveaway.item.quality || ''}}</p>
            <div class="sum sum--sm">
              <div class="icon coin" style="mask-image: url('/assets/icons/coin.svg');"></div>
              {{ giveaway.item.price / 100 }}
            </div>
          </div>
          <div class="giveaway__wrapp-users">
            <div class="icon energy" style="mask-image: url('/images/icons/users.svg');"></div>
            <div class="giveaway__wrapp-users-info">
              <span>{{ giveaway.participants_count }}</span>
              <p>игроков</p>
            </div>
          </div>
        </div>
        <div class="item__center">
          <img :src="giveaway.item.image" class="item__image" :alt="giveaway.item.name">
          <img :src="`/images/case/shadow-${getItemRarityClass(giveaway.item.rarity)}-circle.png`" class="item__rarity-img" alt="">
          <div class="icon item__center-snake" style="mask-image: url('/assets/icons/snake.svg');"></div>
        </div>
        <div class="banners__event-timer">
          <div class="banners__event-timer-item">
            <span class="days">{{ timeLeft.days }}</span>
            <p>дни</p>
          </div>
          <div class="banners__event-timer-item">
            <span class="hours">{{ timeLeft.hours }}</span>
            <p>часы</p>
          </div>
          <div class="banners__event-timer-item">
            <span class="minutes">{{ timeLeft.minutes }}</span>
            <p>мин</p>
          </div>
          <div class="banners__event-timer-item">
            <span class="seconds">{{ timeLeft.seconds }}</span>
            <p>сек</p>
          </div>
        </div>
        <button 
          type="button" 
          class="profile__user-deposit-btn raffleParticipate" 
          @click="joinGiveaway"
          :disabled="giveaway.is_participating || isJoining"
          :style="{ opacity: giveaway.is_participating || isJoining ? 0.5 : 1 }"
        >
          {{ giveaway.is_participating ? 'Вы участвуете' : 'Принять участие' }}
          <div class="icon" style="mask-image: url('images/icons/plus.svg');"></div>
        </button>
        <div class="giveaway__usl">
          Условие:
          депозит от {{ giveaway.min_deposit }}
          <div class="icon coin" style="mask-image: url('images/icons/coin.svg');"></div>
        </div>
      </div>
    </div>
    <div class="giveaway__wrapp-last-winners" v-if="winners.length > 0">
      <span>Последние победители</span>
      <div class="giveaway__wrapp-users-list">
        <div class="giveaway__wrapp-users-list-wrapp">
          <div class="giveaway__wrapp-winner-item" v-for="winner in winners" :key="winner.id">
            <router-link :to="{ name: 'OtherProfile', params: { id: winner.winner.id } }" class="giveaway__wrapp-winner-item-user">
              <img :src="winner.winner.avatar || '/images/default-avatar.png'" :alt="winner.winner.username">
              <div class="giveaway__wrapp-winner-user">
                <span>{{ winner.winner.username }}</span>
                <div class="sum sum--sm">
                  <div class="icon coin" style="mask-image: url('images/icons/coin.svg');"></div>
                  <span>{{ winner.item.price / 100 }}</span>
                </div>
              </div>
            </router-link>
            <div class="giveaway__wrapp-winner-item-image tooltip-bottom" :data-tippy-content="`<p>${winner.item.price}₽</p> ${winner.item.name}`">
              <img :src="winner.item.image" :alt="winner.item.name">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="giveaway__wrapp-item">
    <p>Розыгрыш скоро начнется...</p>
  </div>
</template>

<script>
import { request } from '@/utils/request.js'
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { getItemRarityClass } from "../../../helpers/helpers";

export default {
  name: 'GiveawayCard',
  
  props: {
    type: {
      type: String,
      required: true,
      validator: (value) => ['hourly', 'daily', 'weekly'].includes(value)
    },
    initialGiveaway: {
      type: Object,
      default: null
    },
    initialWinners: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      giveaway: null,
      winners: [],
      timeLeft: { days: '00', hours: '00', minutes: '00', seconds: '00' },
      isJoining: false,
      timerInterval: null
    }
  },

  computed: {
    rarityClass() {
      const classes = {
        hourly: 'restricted',
        daily: 'classified',
        weekly: 'covert'
      }
      return classes[this.type] || 'restricted'
    },
    ...mapState(useAuthStore, ["user"]),
  },

  mounted() {
    // Используем переданные данные если они есть, иначе загружаем
    if (this.initialGiveaway) {
      this.giveaway = this.initialGiveaway
      this.updateTimer()
    } else {
      this.loadGiveaway()
    }

    if (this.initialWinners && this.initialWinners.length > 0) {
      this.winners = this.initialWinners
    } else {
      this.loadWinners()
    }

    this.timerInterval = setInterval(() => {
      this.updateTimer()
    }, 1000)
  },

  beforeUnmount() {
    if (this.timerInterval) {
      clearInterval(this.timerInterval)
    }
  },

  methods: {
    getItemRarityClass,
    async loadGiveaway() {
      try {
        const { data } = await request('GET', '/giveaway')
        if (data.success) {
          const giveawayData = data.giveaways.find(g => g.type === this.type)
          if (giveawayData) {
            this.giveaway = giveawayData
            this.updateTimer()
          }
        }
      } catch (error) {
        console.error('Error loading giveaway:', error)
      }
    },

    async loadWinners() {
      try {
        const { data } = await request('GET', '/giveaway/winners', { type: this.type, per_page: 5 })
        if (data.success) {
          this.winners = data.winners || []
        }
      } catch (error) {
        console.error('Error loading winners:', error)
      }
    },

    async joinGiveaway() {
      if (!this.giveaway || this.isJoining) return

      this.isJoining = true
      try {
        const { data } = await request('POST', `/giveaway/${this.giveaway.id}/join`)
        if (data.success) {
          this.$toastr.success(data.message || 'Вы успешно участвуете в розыгрыше!')
          this.giveaway.is_participating = true
          this.giveaway.participants_count = data.participants_count
        } else {
          this.$toastr.error(data.message || 'Ошибка при участии в розыгрыше')
        }
      } catch (error) {
        const message = error.data?.message || 'Ошибка при участии в розыгрыше'
        this.$toastr.error(message)
      } finally {
        this.isJoining = false
      }
    },

    updateTimer() {
      if (!this.giveaway) return

      const now = new Date().getTime()
      const end = new Date(this.giveaway.finished_at).getTime()
      const distance = end - now

      if (distance < 0) {
        this.timeLeft = { days: '00', hours: '00', minutes: '00', seconds: '00' }
        this.loadGiveaway() // Перезагрузить розыгрыш
        return
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24))
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
      const seconds = Math.floor((distance % (1000 * 60)) / 1000)

      this.timeLeft = {
        days: String(days).padStart(2, '0'),
        hours: String(hours).padStart(2, '0'),
        minutes: String(minutes).padStart(2, '0'),
        seconds: String(seconds).padStart(2, '0')
      }
    }
  }
}
</script>

