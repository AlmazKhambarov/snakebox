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
              style="mask-image: url('images/icons/cs2.svg')"
            ></div>
          </button>
          <button
            type="button"
            class="cases__select-game-btn"
            :class="{ active: selectedGame === 'pubg' }"
            @click="selectGame('pubg')"
          >
            <div
              class="icon"
              style="mask-image: url('images/icons/pubg.svg')"
            ></div>
            <span style="font-size: 11px; font-weight: 600;">PUBG</span>
          </button>
        </div>
        <button @click="resetFilters" type="button" class="btn-gray clearAll">
          <div
            class="icon"
            style="mask-image: url('images/icons/clear.svg')"
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
</style>
