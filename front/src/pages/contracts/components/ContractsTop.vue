<template>
  <div class="contract__wrapp" :class="{ win: winItem }">
    <div v-if="winItem" class="contract__overlay-win">
      <div class="case__win-item">
        <div class="item industrial">
          <div class="item__inner">
            <div class="item__top">
              <div class="item__quality-top">
                {{ winItem.quality }}
              </div>
              <div class="sum sum--xs sum--bgWhite">
                <div class="icon" style="mask-image: url('/assets/icons/coin.svg')">
                  <div class="icon__wrapp"></div>
                </div>
                {{ winItem.steam_price / 100 }}
              </div>
            </div>
            <div class="item__center">
              <img :src="winItem.image" class="item__image" alt="skin" />
              <div
                class="icon item__center-snake"
                style="mask-image: url('/assets/icons/snake.svg')"
              >
                <div class="icon__wrapp"></div>
              </div>
            </div>
            <div class="item__bottom">
              <div class="item__model">{{ winItem.weapon }}</div>
              <div class="item__name">
                {{ winItem.skin_name }}
              </div>
            </div>
          </div>
          <img
            :src="`/images/case/shadow-${getItemRarityClass(winItem.rarity)}.webp`"
            class="item__rarity-img"
            alt="rarity"
          />
        </div>
        <button @click="sellItem(winItem.id)" type="button" class="case__win-item-button">
          <span>Продать за</span>
          <div class="sum sum--xs sum--bgWhite">
            <div class="icon coin" style="mask-image: url('/assets/icons/coin.svg')">
              <div class="icon__wrapp"></div>
            </div>
            {{ winItem.steam_price / 100 }}
          </div>
        </button>
      </div>
      <button @click="reset" type="button" class="btn btn--center saveSkinContract">
        <div class="btn__inner">
          <div class="btn__inner-left">
            <span>Продолжить играть</span>
            <p>Вещь останется в инвентаре</p>
          </div>
        </div>
      </button>
    </div>
    <div class="contract__center">
      <div class="contract__skins">
        <div
          class="contract__skin"
          v-for="index in 10"
          :key="index"
          :class="{ active: selectedItems[index - 1] }"
        >
          <button
            v-if="selectedItems[index - 1]"
            @click="toggleItem(selectedItems[index - 1])"
            type="button"
            class="contract__skin-delete"
          >
            <div class="icon" style="mask-image: url('/images/icons/close.svg')">
              <div class="icon__wrapp"></div>
            </div>
          </button>

          <div class="contract__skin-inner" v-if="selectedItems[index - 1]">
            <div class="item__center">
              <img
                :src="selectedItems[index - 1].item.image"
                class="item__image"
                alt="skin"
              />
              <div
                class="icon item__center-snake"
                style="mask-image: url('/assets/icons/snake.svg')"
              >
                <div class="icon__wrapp"></div>
              </div>
            </div>
            <div class="item__bottom">
              <div class="item__model">
                {{ selectedItems[index - 1].item.weapon }}
              </div>
              <div class="item__name">
                {{ selectedItems[index - 1].item.skin_name }}
              </div>
              <div class="item__quality">
                {{ selectedItems[index - 1].item.quality }}
              </div>
            </div>
            <img
              src="/images/case/shadow-consumer-circle.png"
              class="item__rarity-img"
              alt="circle"
            />
          </div>

          <div class="contract__skin-overlay">
            <img src="/assets/images/ak-47.svg" alt="" />
          </div>
        </div>
      </div>
    </div>
    <div class="contract__play">
      <div class="contract__modes">
        <button
          type="button"
          class="contract__mode low"
          :class="{ active: type === 'low' }"
          @click="$emit('change-type', 'low')"
        >
          <div class="contract__mode-coeff">x3</div>
          <div class="contract__mode-info">
            <span>Легкий</span>
            <p>Режим</p>
          </div>
          <img src="/assets/images/low.png" class="contract__mode-img" alt="" />
        </button>
        <button
          type="button"
          class="contract__mode medium"
          :class="{ active: type === 'medium' }"
          @click="$emit('change-type', 'medium')"
        >
          <div class="contract__mode-coeff">x5</div>
          <div class="contract__mode-info">
            <span>Средний</span>
            <p>Режим</p>
          </div>
          <img src="/assets/images/medium.png" class="contract__mode-img" alt="" />
        </button>
        <button
          type="button"
          class="contract__mode high"
          :class="{ active: type === 'high' }"
          @click="$emit('change-type', 'high')"
        >
          <div class="contract__mode-coeff">x10</div>
          <div class="contract__mode-info">
            <span>Тяжелый</span>
            <p>Режим</p>
          </div>
          <img src="/assets/images/high.png" class="contract__mode-img" alt="" />
        </button>
      </div>
      <div class="contract__info">
        <div class="withdraw__info-left">
          <div class="withdraw__info-item">
            <span>Предметов</span>
            <div class="withdraw__info-item-text">
              <div
                class="icon energy"
                style="mask-image: url('/images/icons/skins.svg')"
              ></div>
              <span
                ><span class="selected_count">{{ selectedItems.length }}</span> из
                10</span
              >
            </div>
          </div>
          <div class="withdraw__info-item">
            <span>На сумму</span>
            <div class="withdraw__info-item-text">
              <div
                class="icon coin"
                style="mask-image: url('/assets/icons/coin.svg')"
              ></div>
              <span class="selected_price">{{ totalSelectedPrice }}</span>
            </div>
          </div>
          <div class="withdraw__info-item">
            <span>Получите</span>
            <div class="withdraw__info-item-text">
              <div
                class="icon coin"
                style="mask-image: url('/assets/icons/coin.svg')"
              ></div>
              <span
                ><span class="min_amount">{{ minAmount }}</span>
                -
                <span class="max_amount">{{ maxAmount }}</span>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="contract__buttons">
        <button
          @click="createContract"
          type="button"
          class="btn btn--center subscribeContract"
        >
          <div class="btn__inner">
            <div class="btn__inner-left">
              <span>Подписать контракт</span>
              <p>Желаем вам успехов!</p>
            </div>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { getItemRarityClass } from "../../../helpers/helpers";
import { request } from "@/utils/request";
export default {
  props: {
    selectedItems: Array,
    toggleItem: Function,
    type: String,
    createContract: Function,
    winItem: Object,
    reset: Function,
    resetItems: Function,
  },
  computed: {
    totalSelectedPrice() {
      const sum = this.selectedItems.reduce((total, item) => total + item.price, 0);
      return sum / 100; // делим на 100, чтобы получить нормальную цену
    },
    minAmount() {
      switch (this.type) {
        case "low":
          return (this.totalSelectedPrice / 3).toFixed(2);
        case "medium":
          return (this.totalSelectedPrice / 5).toFixed(2);
        case "high":
          return (this.totalSelectedPrice / 10).toFixed(2);
        default:
          return 0;
      }
    },
    maxAmount() {
      switch (this.type) {
        case "low":
          return (this.totalSelectedPrice * 3).toFixed(2);
        case "medium":
          return (this.totalSelectedPrice * 5).toFixed(2);
        case "high":
          return (this.totalSelectedPrice * 10).toFixed(2);
        default:
          return 0;
      }
    },
  },
  methods: {
    async sellItem(liveId) {
      await request("POST", "/case/sell/item", {
        liveId: liveId,
      }).then(({ data }) => {
        if (!data.success) {
          this.$toastr.error(data.message);
          return;
        } else {
          this.$toastr.success(data.message);
          this.reset();
          this.resetItems();
          this.refresh();
        }
      });
    },
    getItemRarityClass,
  },
};
</script>
