<template>
  <div class="cases">
    <div class="cases__top gpu-boost">
      <div class="cases__top-left">
        <div class="cases__select-game">
          <button
            type="button"
            class="cases__select-game-btn"
            :class="{ active: selectedGame === 'cs' }"
            @click="selectGame('cs')"
          >
            <div
              class="icon"
              style="mask-image: url('/images/icons/cs2.svg')"
            ></div>
          </button>
          <button
            type="button"
            class="cases__select-game-btn"
            :class="{ active: selectedGame === 'pubg' }"
            @click="selectGame('pubg')"
          >
            <img src="/images/icons/pubglogo.png" style="width: 18px; height: 18px; object-fit: contain;" />
          </button>
        </div>
        <button @click="resetFilters" type="button" class="btn-gray clearAll">
          <div
            class="icon"
            style="mask-image: url('/images/icons/clear.svg')"
          ></div>
          Сбросить всё
        </button>
      </div>
      <div class="cases__top-right">
        <div class="checkbox">
          <input
            type="checkbox"
            id="available"
            v-model="available"
            true-value="1"
            false-value="0"
          />
          <label for="available" class="checkbox__button">
            <div class="checkbox__button-el">
              <div class="checkbox__button-el-inner"></div>
            </div>
            <div class="checkbox__button-desc">
              <span>Доступные мне</span>
              <p>Доступные вам</p>
            </div>
          </label>
        </div>

        <div class="form-input">
          <div class="form-input__wrapp">
            <div class="form-input__icon">
              <div
                class="icon"
                style="mask-image: url('images/icons/search.svg')"
              ></div>
            </div>
            <input
              v-model="filterInputSearch"
              type="text"
              placeholder="Название"
              class="searchCase"
            />
          </div>
        </div>
        <div class="cases__prices">
          <div class="form-input__wrapp">
            <input
              type="number"
              v-model="minPrice"
              placeholder="От"
              id="price-from"
            />
          </div>
          <div class="form-input__wrapp">
            <input
              v-model="maxPrice"
              type="number"
              placeholder="До"
              id="price-to"
            />
          </div>
          <div class="cases__prices-divider"></div>
          <div class="cases__prices-slider">
            <div id="price-slider"></div>
          </div>
        </div>
        <button type="button" class="btn-gray cases__clear-all-mobile">
          <div
            class="icon"
            style="mask-image: url('images/icons/clear.svg')"
          ></div>
          Сбросить всё
        </button>
      </div>
    </div>
    <div class="cases__list gpu-boost" id="cases">
      <LoadingSpinner v-if="isLoading" text="Загрузка кейсов..." />
      <div
        v-else
        v-for="(category, index) in categories"
        :key="category.category.id"
        class="cases__block"
      >
        <div class="cases__head">
          <div class="title">{{ category.category.name }}</div>
          <div class="cases__head-actions">
            <button
              v-if="selectedGame === 'pubg'"
              @click="showUcModal = true"
              type="button"
              class="uc-add-btn click"
            >
              <span>💰 Вывод UC</span>
            </button>
            <button
              @click="toggleCategory(index)"
              type="button"
              class="dropdown__btn--small click"
              x-bind:class="!show &amp;&amp; 'closed'"
            >
              <span> {{ isOpen[index] ? "Свернуть" : "Развернуть" }}</span>
              <i></i>
            </button>
          </div>
        </div>
        <div class="cases__cases" v-show="isOpen[index]">
          <router-link
            v-for="(box, boxIndex) in category.boxes"
            :key="box.id"
            :to="{ name: 'case', params: { url: box.url } }"
            class="cases__case"
          >
            <img
               v-lazy
              class="case-image"
              :data-src="box.image"
            src="/boxes/default.webp"
              :alt="box.name"
            />
            <div class="cases__case-name">{{ box.name }}</div>
            <div class="sum sum--sm sum--bgWhite cases__sum">
              <div
                class="icon"
                style="mask-image: url('/assets/icons/coin.svg')"
              ></div>
              {{ box.is_free ? "FREE" : box.price / 100 }}
            </div>
            <div class="sum sum--sm sum--bgWhite cases__view">
              <div
                class="icon"
                style="mask-image: url('/assets/icons/arrow-top-right.svg')"
              ></div>
              Открыть
            </div>
            <div
              class="icon gray cases__arrow-icon green"
              style="mask-image: url('/assets/icons/arrow-top-right.svg')"
            ></div>
          </router-link>
        </div>
      </div>
    </div>

    <!-- UC Purchase Modal -->
    <teleport to="body">
      <transition name="uc-modal-fade">
        <div v-if="showUcModal" class="uc-modal-overlay" @click.self="showUcModal = false">
          <div class="uc-modal">
            <button class="uc-modal__close" @click="showUcModal = false">&times;</button>

            <!-- Game selector -->
            <div class="uc-section">
              <div class="uc-section__label">O'YINNI TANLASH</div>
              <div class="uc-game-select">
                <img src="/images/icons/pubglogo.png" alt="PUBG" class="uc-game-select__icon" />
                <span class="uc-game-select__arrow">▼</span>
              </div>
            </div>

            <!-- UID input -->
            <div class="uc-section">
              <div class="uc-section__label">FOYDALANUVCHI UID
                <span class="uc-section__site">SNakedrop.uz</span>
              </div>
              <div class="uc-uid-input">
                <input
                  type="text"
                  v-model="pubgUid"
                  class="uc-uid-input__field"
                  placeholder="UID: 51234567"
                />
              </div>
              <div v-if="pubgUid && pubgUid.length >= 6" class="uc-uid-confirmed">
                ✓ UID TASDIQLANDI
              </div>
            </div>

            <!-- UC Amount presets -->
            <div class="uc-section">
              <div class="uc-presets">
                <div
                  v-for="preset in ucPresets"
                  :key="preset.value"
                  class="uc-preset"
                  :class="{ active: ucAmount === preset.value }"
                  @click="ucAmount = preset.value"
                >
                  <div v-if="preset.popular" class="uc-preset__badge">POPULYAR</div>
                  <div class="uc-preset__amount">{{ preset.value }}</div>
                  <div class="uc-preset__label"><span class="uc-coin">UC</span> UC</div>
                  <div class="uc-preset__btn">TANLASH</div>
                </div>
              </div>
            </div>

            <!-- Confirm -->
            <div class="uc-section">
              <div class="uc-confirm-row">
                <span>OLISHNI TASDIQLASH</span>
                <span v-if="ucAmount > 0" class="uc-cost-info">
                  Sizning balansingizdan <strong>{{ (ucAmount * 1.6).toFixed(0) }} coin</strong> yechiladi
                </span>
                <span v-else class="uc-min-text">Minimal summa yechish: <strong>60 UC</strong></span>
              </div>
              <button
                @click="buyUC"
                class="uc-submit-btn"
                :disabled="ucLoading || !ucAmount || ucAmount < 60 || !pubgUid || pubgUid.length < 6"
              >
                <span v-if="ucLoading" class="uc-submit-btn__text">Обработка...</span>
                <span v-else class="uc-submit-btn__text"><span class="uc-submit-btn__icon">UC</span> UC OLISH</span>
              </button>
              <div class="uc-min-bottom">
                🔒 Minimal summa yechish: <strong>60 UC</strong>
              </div>
            </div>

          </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script>
import { request } from "@/utils/request.js";
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

export default {
  components: { LoadingSpinner },
  computed: {
    ...mapState(useAuthStore, ["user"]),
  },
  data() {
    return {
      minPrice: null,
      maxPrice: null,
      filterInputSearch: "",
      categories: [],
      debounceTimer: null,
      available: 0,
      isOpen: [],
      observer: null,
      isLoading: true,
      selectedGame: "cs",
      showUcModal: false,
      ucAmount: 0,
      ucLoading: false,
      pubgUid: '',
      ucPresets: [
        { value: 60, popular: false },
        { value: 300, popular: false },
        { value: 660, popular: true },
        { value: 1800, popular: false },
        { value: 3850, popular: false },
      ],
    };
  },
  mounted() {
    this.getBoxes().then(() => {
      this.$nextTick(() => this.initLazyLoading());
    });
  },
  computed: {},
  watch: {
    filterCategory(newVal) {
      this.generateVisibleInventory();
      this.categoriesIsOpen = false;
    },
    filterCategory2(newVal) {
      this.generateVisibleInventory();
      this.categoriesIsOpen2 = false;
    },
    filterInputSearch() {
      this.debounceGetBoxes();
    },
    minPrice() {
      this.debounceGetBoxes();
    },
    maxPrice() {
      this.debounceGetBoxes();
    },
    available() {
      this.debounceGetBoxes();
    },
  },
  methods: {
    debounceGetBoxes() {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => {
        this.getBoxes();
      }, 300);
    },
    async getBoxes() {
      this.isLoading = true;
      try {
        const getBoxes = await request("GET", "/case/get", {
          url: this.$route.params.url,
          search: this.filterInputSearch,
          min_price: this.minPrice * 100,
          max_price: this.maxPrice * 100,
          available: this.available,
          user: this.user,
          game: this.selectedGame,
        }).then(({ data }) => {
          if (!data.success) {
            this.$toastr.error(data.message);
          } else {
            this.categories = data.categories;
            this.isOpen = this.categories.map(() => true);
          }
        });
      } finally {
        this.isLoading = false;
      }
    },
    toggleCategory(index) {
      this.isOpen[index] = !this.isOpen[index];
      this.$nextTick(() => this.initLazyLoading());
    },
    resetFilters() {
      this.minPrice = null;
      this.maxPrice = null;
      this.filterInputSearch = "";
      this.available = false;
      this.selectedGame = "cs";
      this.getBoxes();
    },
    selectGame(game) {
      this.selectedGame = game;
      this.getBoxes();
    },
    async buyUC() {
      if (!this.pubgUid || this.pubgUid.length < 6) {
        this.$toastr.error('Введите корректный PUBG UID');
        return;
      }
      if (!this.ucAmount || this.ucAmount < 60) {
        this.$toastr.error('Минимальная сумма: 60 UC');
        return;
      }
      this.ucLoading = true;
      try {
        const { data } = await request('POST', '/user/uc/buy', {
          amount: this.ucAmount,
          pubg_uid: this.pubgUid,
        });
        if (data.success) {
          this.$toastr.success('Заявка на покупку UC отправлена!');
          this.showUcModal = false;
          this.ucAmount = 0;
          this.pubgUid = '';
        } else {
          this.$toastr.error(data.message || 'Ошибка при покупке UC');
        }
      } catch (e) {
        this.$toastr.error('Ошибка сети');
      } finally {
        this.ucLoading = false;
      }
    },
initLazyLoading() {
  // если уже был observer — отключаем
  if (this.observer) {
    this.observer.disconnect();
  }

  const options = {
    root: null,
    rootMargin: "100px",
    threshold: 0.1,
  };

  this.observer = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        const realSrc = img.dataset.src;

        // если у картинки уже реальный src — пропускаем
        if (realSrc && img.src !== realSrc) {
          img.src = realSrc;
          img.removeAttribute("data-src");
        }

        observer.unobserve(img);
      }
    });
  }, options);

  const imgs = this.$el.querySelectorAll("img[data-src]");

  imgs.forEach((img) => {
    // если уже стоит реальное изображение — не трогаем
    if (img.src.includes("/images/case/revolver.webp")) {
      this.observer.observe(img);
    }
  });
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
.cases__rtp {
  position: absolute;
  top: 8px;
  right: 8px;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  backdrop-filter: blur(10px);
  z-index: 2;
}

.rtp-good {
  background: rgba(46, 204, 113, 0.9);
  color: white;
}

.rtp-medium {
  background: rgba(241, 196, 15, 0.9);
  color: #333;
}

.rtp-low {
  background: rgba(231, 76, 60, 0.9);
  color: white;
}

.cases__case {
  position: relative;
}

/* Мобильные карточки кейсов — больше */
@media (max-width: 495px) {
  .cases__cases {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 2px !important;
  }

  .cases__case {
    padding: 0 !important;
    margin: 0 !important;
  }

  .cases__case img {
    max-height: none !important;
    width: 100% !important;
    height: auto !important;
    object-fit: contain !important;
  }

  .cases__case-name {
    font-size: 14px !important;
    margin-top: 4px !important;
    padding: 0 4px !important;
  }

  .cases__case .sum {
    font-size: 14px !important;
    padding: 6px 10px !important;
    margin-bottom: 4px !important;
  }
}

/* ── UC Add Button ── */
.cases__head-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  position: absolute;
  right: 0;
  top: 12px;
}

.cases__head-actions .dropdown__btn--small {
  position: static !important;
}

.uc-add-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 8px;
  background: linear-gradient(135deg, #ffd700, #f5a623);
  color: #1a1a1a;
  border: none;
  cursor: pointer;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.25s ease;
  white-space: nowrap;
}

.uc-add-btn:hover {
  background: linear-gradient(135deg, #ffe44d, #ffb833);
  box-shadow: 0 0 16px rgba(255, 215, 0, 0.4);
  transform: translateY(-1px);
}

/* ── UC Modal ── teleported to body, styles below are in unscoped block */
</style>

<style>
/* ── UC Modal (unscoped for teleport) ── */
.uc-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.uc-modal {
  position: relative;
  background: #0d0d0d;
  border: 1px solid rgba(0, 255, 100, 0.15);
  border-radius: 20px;
  padding: 28px 24px;
  width: 480px;
  max-width: 94vw;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6), 0 0 60px rgba(0, 255, 100, 0.04);
  animation: uc-modal-pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes uc-modal-pop {
  from { opacity: 0; transform: scale(0.9) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.uc-modal__close {
  position: absolute;
  top: 12px;
  right: 16px;
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.4);
  font-size: 28px;
  cursor: pointer;
  line-height: 1;
  transition: color 0.2s;
  z-index: 2;
}
.uc-modal__close:hover { color: #fff; }

/* Sections */
.uc-section {
  margin-bottom: 20px;
}

.uc-section__label {
  font-size: 13px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.9);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.uc-section__site {
  font-size: 11px;
  font-weight: 400;
  color: rgba(255, 255, 255, 0.35);
  text-transform: none;
  letter-spacing: 0;
}

/* Game Selector */
.uc-game-select {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  background: #1a1a1a;
  border: 1px solid rgba(0, 255, 100, 0.2);
  border-radius: 12px;
  cursor: pointer;
}

.uc-game-select__icon {
  width: 36px;
  height: 36px;
  filter: brightness(0) invert(1);
}

.uc-game-select__name {
  flex: 1;
  font-size: 16px;
  font-weight: 700;
  color: #fff;
}

.uc-game-select__arrow {
  color: rgba(255, 255, 255, 0.4);
  font-size: 12px;
}

/* UID Input */
.uc-uid-input {
  position: relative;
}

.uc-uid-input__field {
  width: 100%;
  padding: 16px 18px;
  font-size: 16px;
  font-weight: 600;
  background: #1a1a1a;
  border: 2px solid rgba(0, 255, 100, 0.3);
  border-radius: 12px;
  color: #fff;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}

.uc-uid-input__field:focus {
  border-color: rgba(0, 255, 100, 0.6);
  box-shadow: 0 0 16px rgba(0, 255, 100, 0.1);
}

.uc-uid-input__field::placeholder {
  color: rgba(255, 255, 255, 0.3);
  font-weight: 400;
}

.uc-uid-confirmed {
  margin-top: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #00ff64;
  letter-spacing: 0.5px;
}

/* UC Presets */
.uc-presets {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.uc-preset {
  flex: 1;
  min-width: 75px;
  max-width: 90px;
  padding: 12px 6px 10px;
  background: #1a1a1a;
  border: 2px solid rgba(0, 255, 100, 0.2);
  border-radius: 12px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.uc-preset:hover {
  border-color: rgba(0, 255, 100, 0.5);
  background: #1f1f1f;
}

.uc-preset.active {
  border-color: #00ff64;
  background: rgba(0, 255, 100, 0.08);
  box-shadow: 0 0 16px rgba(0, 255, 100, 0.15);
}

.uc-preset__badge {
  position: absolute;
  top: -9px;
  left: 50%;
  transform: translateX(-50%);
  background: #00ff64;
  color: #000;
  font-size: 8px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 4px;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.uc-preset__amount {
  font-size: 22px;
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
}

.uc-preset__label {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.5);
  margin: 2px 0 8px;
}

.uc-coin {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  background: #00cc52;
  border-radius: 50%;
  font-size: 7px;
  font-weight: 800;
  color: #fff;
  vertical-align: middle;
}

.uc-preset__btn {
  font-size: 10px;
  font-weight: 700;
  color: #00ff64;
  background: rgba(0, 255, 100, 0.1);
  border: 1px solid rgba(0, 255, 100, 0.3);
  border-radius: 6px;
  padding: 4px 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.uc-preset.active .uc-preset__btn {
  background: #00ff64;
  color: #000;
  border-color: #00ff64;
}

/* Confirm section */
.uc-confirm-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
  font-size: 12px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.8);
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.uc-min-text {
  font-size: 11px;
  font-weight: 400;
  color: rgba(255, 255, 255, 0.4);
  text-transform: none;
}

.uc-min-text strong {
  color: rgba(255, 255, 255, 0.7);
}

.uc-cost-info {
  font-size: 11px;
  font-weight: 600;
  color: #00ff64;
  text-transform: none;
}

.uc-cost-info strong {
  font-size: 13px;
  color: #fff;
  text-shadow: 0 0 10px rgba(0, 255, 100, 0.4);
}

/* Submit Button */
.uc-submit-btn {
  width: 100%;
  padding: 18px;
  font-size: 18px;
  font-weight: 800;
  background: linear-gradient(135deg, #00e55a, #00cc44);
  color: #fff;
  border: none;
  border-radius: 14px;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  transition: all 0.25s ease;
  box-shadow: 0 4px 20px rgba(0, 229, 90, 0.3);
}

.uc-submit-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #00ff64, #00e55a);
  box-shadow: 0 6px 30px rgba(0, 255, 100, 0.4);
  transform: translateY(-2px);
}

.uc-submit-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.uc-submit-btn:active:not(:disabled) {
  transform: translateY(0);
}

.uc-submit-btn__text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.uc-submit-btn__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: rgba(255, 255, 255, 0.25);
  border-radius: 50%;
  font-size: 10px;
  font-weight: 800;
}

.uc-min-bottom {
  text-align: center;
  margin-top: 14px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.4);
}

.uc-min-bottom strong {
  color: rgba(255, 255, 255, 0.6);
}

/* Transition */
.uc-modal-fade-enter-active,
.uc-modal-fade-leave-active {
  transition: opacity 0.25s ease;
}

.uc-modal-fade-enter-from,
.uc-modal-fade-leave-to {
  opacity: 0;
}
</style>
