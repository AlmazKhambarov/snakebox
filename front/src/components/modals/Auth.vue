<template>
  <div id="login" style="display: none" class="fancybox__content">
    <div class="modal auth">
      <div class="auth__image">
        <div class="auth__image-overlay">
          <div class="header__logotype">
            <div class="header__logotype-wrapp">
              <img
                src="/assets/icons/logo.png"
                srcset="
                  /assets/icons/logo.png      47w,
                  /assets/icons/logo.png      54w,
                  /assets/icons/logo.png      74w,
                  /assets/icons/logo.png    155w,
                  /assets/icons/logo.png   94w,
                  /assets/icons/logo.png 108w,
                  /assets/icons/logo.png 148w,
                  /assets/icons/logo.png 310w
                "
                sizes="
      (min-width: 5120px) 155px,
      (min-width: 2560px)  74px,
      (min-width: 1920px)  54px,
      (min-width: 1366px)  47px,
      47px
    "
                alt="Авторизация"
                loading="lazy"
                decoding="async"
              />
            </div>
            <span>{{ settings.site_name }}</span>
          </div>
          <div class="auth__image-bottom">
            <div class="auth__image-title">Бонус при регистрации</div>
            <div class="sum sum--sm sum--bgWhite">
              <div class="icon" style="mask-image: url('/assets/icons/coin.svg')"></div>
              до 500
            </div>
          </div>
        </div>
        <img src="/assets/images/auth.png" alt="Авторизация" />
      </div>
      <div
        class="modal__body"
      >
        <div class="auth__desc">
          <span>Вход в аккаунт</span>
          <p>Добро пожаловать на {{ settings.site_name }}</p>
        </div>
        <div class="checkbox" :class="{ active: age }">
          <button type="button" @click="age = !age" class="checkbox__button">
            <div class="checkbox__button-el">
              <div class="checkbox__button-el-inner"></div>
            </div>
            <div class="checkbox__button-desc">
              <p>Мне уже исполнилось 18 лет</p>
            </div>
          </button>
        </div>
        <div class="checkbox" :class="{ active: privacy }">
          <button type="button" @click="privacy = !privacy" class="checkbox__button">
            <div class="checkbox__button-el">
              <div class="checkbox__button-el-inner"></div>
            </div>
            <div class="checkbox__button-desc">
              <p>
                Я соглашаюсь с
                <a href="/terms">правилами сайта</a> и
                <a href="/policy">политикой конфиденциальности</a>
              </p>
            </div>
          </button>
        </div>
        <div class="auth__buttons" :class="{ disabled: !age || !privacy }">
          <button @click="logIn" class="btn fill">
            <div class="btn__inner">
              <div class="icon" style="mask-image: url('/images/icons/steam.svg')"></div>
              <div class="btn__inner-left">
                <span>Войти через Steam</span>
                <p>Добро пожаловать!</p>
              </div>
            </div>
          </button>
          <!-- <a href="/auth/vkontakte" class="auth__button fill">
            <div class="icon" style="mask-image: url('/images/icons/vk.svg')"></div>
            Вконтакте
          </a> -->
        </div>
      </div>
    </div>
    <button class="carousel__button is-close" data-fancybox-close title="Close">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" tabindex="-1">
        <path d="M20 20L4 4m16 0L4 20"></path>
      </svg>
    </button>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useMainStore } from "@/stores/main.store.js";
export default {
  data() {
    return { age: false, privacy: false };
  },
  computed: {
    ...mapState(useMainStore, ["settings"]),
  },
  methods: {
    ...mapActions(useMainStore, ["getSettings"]),
    logIn() {
      window.location.href = import.meta.env.VITE_APP_BACKEND_URL + "/api/auth/steam/";
    },
  },
};
</script>
