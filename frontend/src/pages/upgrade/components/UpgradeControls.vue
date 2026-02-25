<template>
  <div class="page__controls">
    <div class="page__controls-left">
      <div class="page__controls-left-desc">
        <span>КОЭФФИЦИЕНТ</span>
        <p>Настрой под себя</p>
      </div>
      <div class="page__controls-left-inner">
        <button
          v-for="factor in factors"
          :key="factor"
          type="button"
          class="page__controls-button"
          :class="{ active: inventoryButtonFactor === factor }"
          @click="selectFactor(factor)"
        >
          <span>{{ factor }}</span>
        </button>
      </div>
    </div>
    <div class="page__controls-center upgrade__buttons">
      <button
        @click="createUpgrade"
        class="btn btn--center page__controls-main-btn startUpgrade"
      >
        <div class="btn__inner">
          <div class="btn__inner-left">
            <span>Прокачать</span>
            <p>Желаем вам успехов!</p>
          </div>
        </div>
      </button>
      <div
        class="btn btn--center page__controls-main-btn waitUpgrade"
        style="display: none"
      >
        <div class="btn__inner">
          <div class="btn__inner-left">
            <span>Апгрейд...</span>
            <p>Желаем вам успехов!</p>
          </div>
        </div>
      </div>
    </div>
    <div class="page__controls-right">
      <!-- <div class="page__controls-right-inner">
                <div class="page__controls-right-text">
                    <div
                        class="icon"
                        style="mask-image: url('images/icons/energy.svg')"
                    ></div>
                    <span>ВКЛ / ВЫКЛ</span>
                </div>
                <div class="checkbox">
                    <input type="checkbox" id="fast" />
                    <label for="fast" class="checkbox__button">
                        <div class="checkbox__button-el">
                            <div class="checkbox__button-el-inner"></div>
                        </div>
                        <div class="checkbox__button-desc">
                            <span>Быстро</span>
                            <p>Быстрый режим</p>
                        </div>
                    </label>
                </div>
            </div> -->
    </div>
  </div>
</template>

<script>
export default {
  props: {
    inventoryButtonFactor: String,
    createUpgrade: Function,
    state: String,
  },
  data() {
    return {
      factors: ["x1.5", "x2", "x5", "x10"], // Можно легко добавить новые
    };
  },
  methods: {
    selectFactor(factor) {
      this.$playSound("/sounds/click.mp3");
      this.$emit("update:inventoryButtonFactor", factor);
    },
  },
};
</script>
