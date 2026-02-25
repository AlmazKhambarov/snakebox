<template>
  <div class="page refferal">
    <div class="page__header">
      <div class="page__header-left">
        <router-link :to="{ name: 'index' }" class="page__header-back">
          <div class="icon" style="mask-image: url('/images/icons/arrow-left.svg')"></div>
        </router-link>
        <div class="page__header-info">
          <div class="page__header-info-inner">
            <span>Ваши рефералы</span>
            <p>Список ваших рефералов</p>
          </div>
        </div>
      </div>
    </div>
    <div class="page__body">
      <div class="tabs">
        <router-link :to="{ name: 'referrals' }" class="tabs__button">
          <div class="icon" style="mask-image: url('/images/icons/grid.svg')"></div>
          <span>Краткая сводка</span>
        </router-link>
        <router-link :to="{ name: 'referrals-users' }" class="tabs__button active">
          <div class="icon" style="mask-image: url('/images/icons/history.svg')"></div>
          <span>Пользователи</span>
        </router-link>
      </div>
      <div class="refferal__top">
        <div class="form-input">
          <div class="form-input__label">Поиск по имени игрока</div>
          <div class="form-input__wrapp">
            <div class="form-input__icon">
              <div class="icon" style="mask-image: url('/images/icons/search.svg')"></div>
            </div>
            <input
              type="text"
              placeholder="Имя игрока"
              class="searchReferral"
              v-model="search"
            />
          </div>
        </div>
      </div>
      <div class="table-wrapp">
        <table>
          <thead>
            <tr>
              <td>Игрок</td>
              <td>Дата регистрации</td>
              <td>Пополнил</td>
              <td>Заработок</td>
            </tr>
          </thead>
          <tbody class="referralsUsers">
            <tr v-for="(ref, index) in referrals" :key="index">
              <td>
                <router-link :to="{name: 'OtherProfile', params: {id: ref.id}}" class="event__leaderboard-user">
                  <img
                    :src="ref.avatar"
                    alt=""
                  />
                  <span>{{ ref.username }}</span>
                </router-link>
              </td>
              <td>{{ formatDateTime(ref.created_at) }}</td>
              <td>
                <div class="sum">
                  <div
                    class="icon coin"
                    style="mask-image: url('/assets/icons/coin.svg')"
                  ></div>
                  <span>{{ ref.total_deposited / 100 }}</span>
                </div>
              </td>
              <td>
                <div class="sum">
                  <div
                    class="icon coin"
                    style="mask-image: url('/assets/icons/coin.svg')"
                  ></div>
                  <span>{{ ref.earned_from_user / 100 }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <button type="button" class="pagination__button pagination__prev">
          <div class="icon" style="mask-image: url('/images/icons/arrow-left.svg')"></div>
          <span>Предыдущая страница</span>
        </button>
        <div class="pagination__current">
          <span><span>1</span> из 1</span>
        </div>
        <button type="button" class="pagination__button pagination__next">
          <span>Следующая страница</span>
          <div
            class="icon"
            style="mask-image: url('/images/icons/arrow-right.svg')"
          ></div>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { request } from "@/utils/request.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    // Инициализируем SEO для страницы пользователей рефералов
    const { seoData, updateOpenGraph } = useSeo('referrals-users');
    
    return {
      seoData,
      updateOpenGraph
    };
  },
  data() {
    return {
      referrals: [],
      search: "",
      searchTimer: null,
    };
  },
  mounted() {
    this.loadReferrals();
  },
  watch: {
    search() {
      this.debouncedSearch();
    },
  },
  methods: {
    async loadReferrals(page = 1) {
      const { data } = await request("GET", "/referral/referrals", {
        search: this.search,
        page: page,
      });
      if (data.success) {
        this.referrals = data.referrals.data;
        console.info("referrals", this.referrals);
      }
    },

    debouncedSearch() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => {
        this.loadReferrals(1);
      }, 500);
    },
    formatDateTime(dateString) {
      if (!dateString) return "-";

      const date = new Date(dateString);

      // Проверка на валидность даты
      if (isNaN(date.getTime())) return "-";

      return date.toLocaleString("ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
  },
};
</script>
