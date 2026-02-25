<template>
  <div class="page__header">
    <div class="page__header-left">
      <router-link :to="{ name: 'index' }" class="page__header-back">
        <div class="icon" style="mask-image: url('/images/icons/arrow-left.svg')"></div>
      </router-link>
      <div class="page__header-info">
        <img
          :src="box.image"
          :alt="box.name"
          class="page__header-info-image"
          loading="lazy"
          decoding="async"
        />

        <div class="page__header-info-inner">
          <h1>{{ box.name }}</h1>
          <h2>Открыть кейс</h2>
         
        </div>
      </div>
    </div>
    <div class="page__header-right">
      <button
        class="page__header-back sound click"
        @click="toggleSound"
        :class="{ off: !$frontSettings.sounds }"
      >
        <div class="icon" style="mask-image: url('/images/icons/sound-on.svg')"></div>
        <div
          class="icon off"
          style="mask-image: url('/images/icons/sound-off.svg')"
        ></div>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: { box: Object },
  methods: {
    toggleSound() {
      this.$frontSettings.sounds = !this.$frontSettings.sounds;
      this.$playSound("/sounds/click.mp3");
    },
    getRTPClass(rtp) {
      if (!rtp) return '';
      if (rtp >= 94) return 'rtp-good';
      if (rtp >= 90) return 'rtp-medium';
      return 'rtp-low';
    },
  },
};
</script>

<style scoped>
.case-rtp-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  margin-top: 8px;
}

.rtp-label {
  opacity: 0.8;
  font-size: 12px;
}

.rtp-good {
  background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(46, 204, 113, 0.3);
}

.rtp-medium {
  background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
  color: #333;
  box-shadow: 0 2px 8px rgba(241, 196, 15, 0.3);
}

.rtp-low {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
}
</style>
