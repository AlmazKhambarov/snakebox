<template>
  <div v-if="isVisible" class="giveaway-animation-overlay" @click="skipAnimation">
    <div class="giveaway-animation">
      <div class="giveaway-animation__header">
        <h2>🎉 Определяем победителя {{ typeText }}!</h2>
        <p>Нажмите для пропуска</p>
      </div>
      
      <div class="giveaway-animation__prize">
        <div class="giveaway-animation__prize-image">
          <img :src="prizeImage" :alt="prizeName">
        </div>
        <div class="giveaway-animation__prize-info">
          <h3>{{ prizeName }}</h3>
          <div class="sum">
            <div class="icon coin" style="mask-image: url('/assets/icons/coin.svg');"></div>
            {{ prizePrice / 100 }}
          </div>
        </div>
      </div>

      <div class="giveaway-animation__participants" ref="participantsContainer">
        <div 
          class="giveaway-animation__participants-list" 
          :style="{ transform: `translateY(${scrollPosition}px)` }"
        >
          <div 
            v-for="(participant, index) in displayParticipants" 
            :key="`participant-${index}`"
            class="giveaway-animation__participant"
            :class="{ 
              'giveaway-animation__participant--winner': isWinner(index),
              'giveaway-animation__participant--highlight': !isFinished && index === highlightIndex 
            }"
          >
            <img 
              :src="participant.avatar || '/images/default-avatar.png'" 
              :alt="participant.username"
              class="giveaway-animation__participant-avatar"
            >
            <span class="giveaway-animation__participant-name">{{ participant.username }}</span>
          </div>
        </div>
        <div class="giveaway-animation__selector"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GiveawayWinnerAnimation',
  
  props: {
    type: {
      type: String,
      required: true
    },
    participants: {
      type: Array,
      required: true
    },
    winner: {
      type: Object,
      required: true
    },
    prize: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      isVisible: false,
      isFinished: false,
      scrollPosition: 0,
      highlightIndex: 0,
      displayParticipants: [],
      animationInterval: null
    }
  },

  computed: {
    typeText() {
      const types = {
        hourly: 'часового розыгрыша',
        daily: 'дневного розыгрыша',
        weekly: 'недельного розыгрыша'
      }
      return types[this.type] || 'розыгрыша'
    },
    prizeName() {
      return this.prize.name || this.prize.title || 'Предмет'
    },
    prizePrice() {
      return this.prize.price || this.prize.steam_price / 100 || 0
    },
    prizeImage() {
      return this.prize.image || '/images/default-item.png'
    }
  },

  methods: {
    show() {
      this.isVisible = true
      this.isFinished = false
      this.scrollPosition = 0
      this.highlightIndex = 0
      
      // Создаем массив участников с повторениями для плавной прокрутки
      this.createParticipantsList()
      
      // Запускаем анимацию
      this.$nextTick(() => {
        this.startAnimation()
      })
    },

    createParticipantsList() {
      // Дублируем участников для создания эффекта бесконечной прокрутки
      const repeats = 10
      this.displayParticipants = []
      
      for (let i = 0; i < repeats; i++) {
        this.displayParticipants.push(...this.participants)
      }
      
      // Добавляем победителя в конец
      this.displayParticipants.push(this.winner)
    },

    startAnimation() {
      this.$playSound('/sounds/drum-roll.mp3', 0.3)
      
      const itemHeight = 80 // высота одного элемента
      const duration = 5000 // длительность анимации в мс
      const winnerIndex = this.displayParticipants.length - 1
      const targetPosition = -(winnerIndex * itemHeight) // центрируем победителя (padding и top: 50% компенсируют друг друга)
      
      let startTime = null
      let currentSpeed = 0
      const maxSpeed = 50
      
      const animate = (timestamp) => {
        if (!startTime) startTime = timestamp
        const elapsed = timestamp - startTime
        const progress = Math.min(elapsed / duration, 1)
        
        // Ease out cubic для плавного замедления
        const easeProgress = 1 - Math.pow(1 - progress, 3)
        
        this.scrollPosition = targetPosition * easeProgress
        this.highlightIndex = Math.floor(Math.abs(this.scrollPosition) / itemHeight)
        
        if (progress < 1) {
          requestAnimationFrame(animate)
        } else {
          this.finishAnimation()
        }
      }
      
      requestAnimationFrame(animate)
    },

    finishAnimation() {
      this.$playSound('/sounds/win.mp3', 0.5)
      
      setTimeout(() => {
        this.isFinished = true
      }, 500)
      
      // Автоматически закрываем через 5 секунд
      setTimeout(() => {
        this.close()
      }, 5000)
    },

    skipAnimation() {
      if (!this.isFinished) {
        const itemHeight = 80
        const winnerIndex = this.displayParticipants.length - 1
        this.scrollPosition = -(winnerIndex * itemHeight) // центрируем победителя
        this.finishAnimation()
      }
    },

    isWinner(index) {
      return this.isFinished && index === this.displayParticipants.length - 1
    },

    close() {
      this.isVisible = false
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
.giveaway-animation-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgb(7 14 9 / 87%);
  backdrop-filter: blur(10px);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.giveaway-animation {
  background: var(--container-color);
  border-radius: 20px;
  padding: 40px;
  max-width: 600px;
  width: 90%;
  box-shadow: 0 20px 60px var(--shadow-primary);
  position: relative;
  cursor: default;
}

.giveaway-animation__header {
  text-align: center;
  margin-bottom: 30px;
}

.giveaway-animation__header h2 {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 10px;
  background: var(--primary);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: pulse 2s ease-in-out infinite;
}

.giveaway-animation__header p {
  font-size: 14px;
  opacity: 0.7;
  margin: 0;
}

.giveaway-animation__prize {
  display: flex;
  align-items: center;
  gap: 20px;
  background: rgba(255, 255, 255, 0.05);
  padding: 20px;
  border-radius: 15px;
  margin-bottom: 30px;
}

.giveaway-animation__prize-image {
  width: 80px;
  height: 80px;
  flex-shrink: 0;
}

.giveaway-animation__prize-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.giveaway-animation__prize-info h3 {
  font-size: 18px;
  margin: 0 0 10px 0;
}

.giveaway-animation__participants {
  position: relative;
  height: 400px;
  overflow: hidden;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 15px;
  margin-bottom: 20px;
}

.giveaway-animation__participants-list {
  transition: transform 0.05s linear;
  padding: 160px 0;
}

.giveaway-animation__participant {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 20px;
  height: 80px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.giveaway-animation__participant--highlight {
  background: rgb(0 255 126 / 10%);
  transform: scale(1.02);
}

.giveaway-animation__participant--winner {
  background: linear-gradient(90deg, rgb(0 255 20 / 30%), rgb(78 255 108 / 30%));
  border: 2px solid #00ff1f;
  animation: winnerPulse 1s ease-in-out infinite;
}

.giveaway-animation__participant-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.giveaway-animation__participant-name {
  font-size: 18px;
  font-weight: 600;
}

.giveaway-animation__selector {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 80px;
  transform: translateY(-50%);
  border: 3px solid var(--primary);
  border-radius: 10px;
  pointer-events: none;
  box-shadow: 0 0 30px var(--primary);
}

.giveaway-animation__winner {
  text-align: center;
  animation: fadeIn 0.5s ease-in;
}

.giveaway-animation__winner-crown {
  font-size: 60px;
  margin-bottom: 20px;
  animation: bounce 1s ease-in-out infinite;
}

.giveaway-animation__winner h2 {
  font-size: 32px;
  margin-bottom: 20px;
  color: var(--primary);
}

.giveaway-animation__winner-card {
  background: rgba(255, 255, 255, 0.1);
  padding: 30px;
  border-radius: 15px;
  margin-bottom: 20px;
}

.giveaway-animation__winner-card img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border: 4px solid var(--primary);
  margin-bottom: 15px;
}

.giveaway-animation__winner-card h3 {
  font-size: 24px;
  margin: 0;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes winnerPulse {
  0%, 100% { box-shadow: 0 0 20px var(--primary); }
  50% { box-shadow: 0 0 40px var(--primary); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@media (max-width: 768px) {
  .giveaway-animation {
    padding: 20px;
  }

  .giveaway-animation__header h2 {
    font-size: 20px;
  }

  .giveaway-animation__participants {
    height: 300px;
  }
}
</style>

