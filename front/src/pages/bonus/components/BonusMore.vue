<template>
  <div class="bonus__more">
    <div class="banners__list-item-top gray">
      <span>Получай еще больше бонусов</span>
    </div>
    <div class="bonus__tasks-list scroll-0 dragscroll">
      <div class="bonus__more-item">
        <div class="bonus__more-item-desc">
          <span>Загрузите один из аватаров</span>
          <p>Установите загруженное изображение в качестве своего аватара в Steam</p>
        </div>
        <div class="bonus__more-avatars">
          <a
            href="/assets/avatars/1.png"
            class="tooltip-bottom"
            data-tippy-content="Нажмите, чтобы скачать"
            download=""
            ><img src="/assets/avatars/1.png" alt=""
          /></a>
          <a
            href="/assets/avatars/2.png"
            class="tooltip-bottom"
            data-tippy-content="Нажмите, чтобы скачать"
            download=""
            ><img src="/assets/avatars/2.png" alt=""
          /></a>
          <a
            href="/assets/avatars/3.png"
            class="tooltip-bottom"
            data-tippy-content="Нажмите, чтобы скачать"
            download=""
            ><img src="/assets/avatars/3.png" alt=""
          /></a>
        </div>
        <div class="bonus__task-bottom" v-if="isAuth">
          <button
            class="bonus__task-button checkAvatar"
            :disabled="loading.avatar"
            @click="checkAvatar"
          >
            <span v-if="!loading.avatar">Проверить</span>
            <span v-else>Проверяем...</span>
          </button>
          <a
            href="https://steamcommunity.com/id/me/edit/avatar"
            target="_blank"
            class="bonus__task-button"
          >
            <span>Установить в Steam</span>
          </a>
          <div class="bonus__top-right-points">
            <div
              class="icon coin"
              style="mask-image: url('/assets/icons/coin.svg')"
            ></div>
            <span>0.5</span>
          </div>
        </div>
        <div class="bonus__more-item-desc">
          <p>
            * Получить бонус можно только раз в 24 часа, если за неделю вы пополнили
            баланс от 150 рублей.
          </p>
        </div>
      </div>
      <div class="bonus__more-item">
        <div class="bonus__more-item-desc">
          <span>Добавьте в ник {{ settings.domain }}</span>
          <p>
            Пример вашего никнейма в Steam с припиской
            {{ settings.domain }}
          </p>
        </div>
        <div class="bonus__nickname">
          <img
            :src="user?.avatar || '/assets/avatars/1.png'"
            alt=""
          />
          <span> {{ user?.username || 'Пользователь' }} {{ settings.domain }} </span>
          <button type="button" @click="copyExampleUsername">
            <div class="icon" style="mask-image: url('/images/icons/copy.svg')"></div>
          </button>
        </div>
        <div class="bonus__task-bottom" v-if="isAuth">
          <button
            class="bonus__task-button checkUsername"
            :disabled="loading.username"
            @click="checkUsername"
          >
            <span v-if="!loading.username">Проверить</span>
            <span v-else>Проверяем...</span>
          </button>
          <a
            href="https://steamcommunity.com/id/me/edit/info"
            target="_blank"
            class="bonus__task-button"
          >
            <span>Установить в Steam</span>
          </a>
          <div class="bonus__top-right-points">
            <div
              class="icon coin"
              style="mask-image: url('/assets/icons/coin.svg')"
            ></div>
            <span>0.5</span>
          </div>
        </div>
        <div class="bonus__more-item-desc">
          <p>
            * Получить бонус можно только раз в 24 часа, если за неделю вы пополнили
            баланс от 150 рублей.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useMainStore } from "@/stores/main.store.js";
import { useAuthStore } from "@/stores/auth.store.js";
import { request } from "@/utils/request.js";

export default {
  props: { user: Object, isAuth: Boolean },
  data() {
    return {
      loading: {
        avatar: false,
        username: false,
      },
    };
  },
  computed: {
    ...mapState(useMainStore, ["settings"]),
  },
  methods: {
    ...mapActions(useMainStore, ["getSettings"]),

    async checkAvatar() {
      this.loading.avatar = true;
      try {
        const response = await request("POST", "/bonus/check-avatar");

        if (response.data.success) {
          this.$toastr.success(response.data.message);
        } else {
          this.$toastr.error(response.data.message);
        }
      } catch (error) {
        this.$toastr.error("Произошла ошибка при проверке аватара");
        console.error("Avatar check error:", error);
      } finally {
        this.loading.avatar = false;
      }
    },

    async checkUsername() {
      this.loading.username = true;
      try {
        const response = await request("POST", "/bonus/check-username");

 console.log('response',response);
        if (response.data.success) {
          this.$toastr.success(response.data.message);
          console.log('true',response.data.message);
        } else {
          this.$toastr.error(response.data.message);
        }
      } catch (error) {
        this.$toastr.error("Произошла ошибка при проверке ника");
        console.error("Username check error:", error);
      } finally {
        this.loading.username = false;
      }
    },

    async copyExampleUsername() {
      const exampleText = `Typ6oKpoJIuK ${this.settings.domain}`;
      try {
        await navigator.clipboard.writeText(exampleText);
        this.$toastr.success("Никнейм скопирован в буфер обмена");
      } catch (error) {
        // Fallback для старых браузеров
        const textArea = document.createElement("textarea");
        textArea.value = exampleText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        this.$toastr.success("Никнейм скопирован в буфер обмена");
      }
    },
  },
};
</script>
