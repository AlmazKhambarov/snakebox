<template>
  <div class="page refferal">
    <div class="page__header">
      <div class="page__header-left">
        <router-link :to="{ name: 'index' }" class="page__header-back">
          <div class="icon" style="mask-image: url('/images/icons/arrow-left.svg')"></div>
        </router-link>
        <div class="page__header-info">
          <div class="page__header-info-inner">
            <span>Реферальная программа</span>
            <p>Система лояльности</p>
          </div>
        </div>
      </div>
    </div>
    <div class="page__body">
      <div class="tabs">
        <router-link :to="{ name: 'referrals' }" class="tabs__button active">
          <div class="icon" style="mask-image: url('/images/icons/grid.svg')"></div>
          <span>Краткая сводка</span>
        </router-link>
        <router-link :to="{ name: 'referrals-users' }" class="tabs__button">
          <div class="icon" style="mask-image: url('/images/icons/history.svg')"></div>
          <span>Пользователи</span>
        </router-link>
      </div>
      <div class="refferal__wrapp">
        <div class="refferal__body">
          <div class="refferal__main">
            <div class="refferal__main-inner">
              <div class="refferal__main-body">
                <span>{{ summary.referral_code }}</span>
                <button
                  @click="copyRefCode"
                  type="button"
                  class="refferal__main-btn copyRefCode click"
                >
                  <div
                    class="icon"
                    style="mask-image: url('/images/icons/copy.svg')"
                  ></div>
                </button>
              </div>
              <span>Ваш реферальный код</span>
            </div>
            <img src="/assets/images/refferal-bg.png" class="refferal__main-img" alt="" />
          </div>
          <div class="refferal__grid">
            <div class="bonus__task">
              <div class="bonus__task-left">
                <div class="sum">
                  <div
                    class="icon coin"
                    style="mask-image: url('/assets/icons/coin.svg')"
                  ></div>
                  <span>{{ summary.total_earned / 100 }}</span>
                </div>
                <div class="bonus__task-title">Ваш доход</div>
              </div>
              <div class="bonus__task-image">
                <img src="/assets/images/wallet.png" alt="" />
              </div>
            </div>
            <div class="bonus__task">
              <div class="bonus__task-left">
                <div class="sum">
                  <div
                    class="icon energy"
                    style="mask-image: url('/images/icons/users.svg')"
                  ></div>
                  <span>{{ summary.referrals_count }}</span>
                </div>
                <div class="bonus__task-title">Рефералов</div>
              </div>
              <div class="bonus__task-image">
                <img src="/assets/images/user.png" alt="" />
              </div>
            </div>
            <div class="bonus__task fill refferal__refill">
              <div class="bonus__task-left">
                <div class="sum">
                  <div
                    class="icon coin"
                    style="mask-image: url('/assets/icons/coin.svg')"
                  ></div>
                  <span class="refBalance">{{ summary.referral_balance / 100 }}</span>
                </div>
                <div class="bonus__task-title">Реферальный баланс</div>
              </div>
              <div class="bonus__task-bottom">
                <button
                  @click="transferToBalance"
                  class="bonus__task-button claimReferralBalance click"
                >
                  <span>Перевести</span>
                </button>
              </div>
            </div>
            <div class="refferal__banner">
              <div class="banners__list-item-top gray">
                <span>Бонус от нового игрока</span>
                <p>
                  За каждого нового игрока с первым разовым депозитом от 1000₽ вы получите
                  {{ summary.bonus_per_referral }}₽
                </p>
              </div>
              <div
                class="event__course-calc tooltip-bottom"
                data-tippy-content="Награда за реферала"
              >
                <div class="sum">
                  <div
                    class="icon coin"
                    style="mask-image: url('/assets/icons/coin.svg')"
                  ></div>
                  <span>{{ summary.bonus_per_referral }}</span>
                </div>
                <div class="refferal__banner-desc">За нового игрока</div>
              </div>
            </div>
          </div>
        </div>
        <div class="refferal__right">
          <div class="refferal__levels">
            <div class="banners__list-item-top gray">
              <p>Уровни реферальной системы</p>
              <span>Ваш уровень</span>
            </div>

            <div class="refferal__levels-list">
              <div
                v-for="level in levels"
                :key="level.id"
                class="refferal__level-item tooltip-bottom"
                :class="{
                  active: summary.current_level === level.id,
                }"
              >
                <div class="refferal__level-item-left">
                  <div class="refferal__level-item-progress">
                    <div class="refferal__level-item-number">
                      {{ level.id }}
                    </div>
                  </div>
                  <div class="sum">
                    <div
                      class="icon coin"
                      style="mask-image: url('/assets/icons/coin.svg')"
                    ></div>
                    <span>{{ level.sum }}</span>
                  </div>
                </div>
                <div class="refferal__level-procent">
                  {{ level.percent }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="refferal__inputs">
        <div class="form-input">
          <div class="form-input__label">Реферальная ссылка по умолчанию</div>
          <div class="form-input__wrapp">
            <div class="form-input__button">
              <button
                @click="copyRefLink"
                type="button"
                class="page__header-back copyRefLink click"
              >
                <div class="icon" style="mask-image: url('/images/icons/copy.svg')"></div>
              </button>
            </div>
            <input
              type="text"
              :value="summary.referral_link"
              class="refLinkCopy"
              disabled=""
            />
          </div>
        </div>
        <div class="form-input">
          <div class="form-input__label">Реферальный код по умолчанию</div>
          <div class="form-input__wrapp">
            <div class="form-input__button">
              <button
                @click="copyRefCode"
                type="button"
                class="page__header-back copyRefCode click"
              >
                <div class="icon" style="mask-image: url('/images/icons/copy.svg')"></div>
              </button>
            </div>
            <input
              type="text"
              :value="summary.referral_code"
              class="refCodeCopy"
              disabled=""
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { request } from "@/utils/request.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    // Инициализируем SEO для страницы рефералов
    const { seoData, updateOpenGraph } = useSeo('referrals');
    
    return {
      seoData,
      updateOpenGraph
    };
  },
  data() {
    return {
      summary: {
        referral_code: "default",
        referral_link: "default",
        current_level: 1,
        level_percentage: 0,
        next_level_requirement: 0,
        total_earned: 0,
        referral_balance: 0,
        referrals_count: 0,
        total_deposited: 0,
        bonus_per_referral: 25,
      },
      referrals: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
      searchQuery: "",
      showTransferModal: false,
      transferAmount: 0,
      searchTimer: null,
      levels: [
        { id: 1, sum: 0, percent: "0.5%" },
        { id: 2, sum: 50000, percent: "1.0%" },
        { id: 3, sum: 100000, percent: "1.5%" },
        { id: 4, sum: 500000, percent: "2.0%" },
        { id: 5, sum: 1000000, percent: "2.5%" },
      ],
    };
  },
  mounted() {
    this.loadSummary();
    this.loadReferrals();
  },
  methods: {
    async loadSummary() {
      try {
        const { data } = await request("GET", "/referral/summary");
        if (data.success) {
          this.summary = data.summary;
        }
      } catch (error) {
        console.error("Failed to load referral summary:", error);
      }
    },

    async loadReferrals(page = 1) {
      try {
        const params = { page };
        if (this.searchQuery) {
          params.search = this.searchQuery;
        }

        const { data } = await request("GET", "/referral/referrals", params);
        if (data.success) {
          this.referrals = data.referrals;
        }
      } catch (error) {
        console.error("Failed to load referrals:", error);
      }
    },

    debouncedSearch() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => {
        this.loadReferrals(1);
      }, 500);
    },

    async copyRefCode() {
      try {
        await navigator.clipboard.writeText(this.summary.referral_code);
        this.showCopySuccess("Код скопирован!");
      } catch (err) {
        console.error("Failed to copy code:", err);
        this.fallbackCopyText(this.summary.referral_code, "Код скопирован!");
      }
    },

    // Копирование реферальной ссылки
    async copyRefLink() {
      try {
        await navigator.clipboard.writeText(this.summary.referral_link);
        this.showCopySuccess("Ссылка скопирована!");
      } catch (err) {
        console.error("Failed to copy link:", err);
        this.fallbackCopyText(this.summary.referral_link, "Ссылка скопирована!");
      }
    },

    // Универсальный метод копирования с fallback
    fallbackCopyText(text, successMessage) {
      const textArea = document.createElement("textarea");
      textArea.value = text;
      textArea.style.position = "fixed";
      textArea.style.left = "-999999px";
      textArea.style.top = "-999999px";
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();

      try {
        document.execCommand("copy");
        this.showCopySuccess(successMessage);
      } catch (err) {
        console.error("Fallback copy failed:", err);
        alert("Не удалось скопировать текст");
      }

      document.body.removeChild(textArea);
    },

    // Показ уведомления об успешном копировании
    showCopySuccess(message) {
      // Создаем кастомное уведомление вместо alert
      this.$toastr.success(message);

    },

    async transferToBalance() {
      const { data } = await request("POST", "/referral/transfer");

      if (data.success) {
        this.loadSummary();
        this.$toastr.success(data.message);
      } else {
        this.$toastr.error(data.message);
      }
    },

    getLevelPercentage(level) {
      const percentages = { 1: 0.5, 2: 1, 3: 1.5, 4: 2, 5: 2.5 };
      return percentages[level];
    },

    getLevelRequirement(level) {
      const requirements = {
        1: 0,
        2: 50000,
        3: 100000,
        4: 500000,
        5: 1000000,
      };
      return requirements[level];
    },

    formatMoney(amount) {
      return new Intl.NumberFormat("ru-RU").format(amount);
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString("ru-RU");
    },
  },
};
</script>
