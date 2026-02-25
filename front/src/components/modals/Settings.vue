<template>
    <div id="settings" style="display: none" class="fancybox__content">
        <div class="modal settings">
            <div class="modal__body">
                <div class="auth__desc">
                    <span>Настройки</span>
                    <p>Настройка визуализации</p>
                </div>
                <div class="form-input">
                    <div class="form-input__label">
                        Звуки
                        <div
                            class="icon tooltip-bottom"
                            data-tippy-content="Отключает все звуки на сайте"
                            style="mask-image: url('/images/icons/help.svg')"
                        ></div>
                    </div>
                    <div class="page__controls-right-inner">
                        <div class="page__controls-right-text">
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/energy.svg');
                                "
                            ></div>
                            <span>ВЫКЛ / ВКЛ</span>
                        </div>
                        <div class="checkbox">
                            <input
                                type="checkbox"
                                id="sounds"
                                v-model="$frontSettings.sounds"
                            />
                            <label
                                for="sounds"
                                class="checkbox__button soundCheckbox"
                            >
                                <div class="checkbox__button-el">
                                    <div
                                        class="checkbox__button-el-inner"
                                    ></div>
                                </div>
                                <div class="checkbox__button-desc">
                                    <span>Звуки</span>
                                    <p>Выкл. звуки</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-input">
                    <div class="form-input__label">
                        Стример мод
                        <div
                            class="icon tooltip-bottom"
                            data-tippy-content="Скрывает аватарки пользователей, а также скрывает отображение никнеймов."
                            style="mask-image: url('/images/icons/help.svg')"
                        ></div>
                    </div>
                    <div class="page__controls-right-inner">
                        <div class="page__controls-right-text">
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/energy.svg');
                                "
                            ></div>
                            <span>ВЫКЛ / ВКЛ</span>
                        </div>
                        <div class="checkbox">
                            <input
                                type="checkbox"
                                id="streamer"
                                v-model="$frontSettings.streamer"
                            />
                            <label
                                for="streamer"
                                class="checkbox__button streamerMode"
                            >
                                <div class="checkbox__button-el">
                                    <div
                                        class="checkbox__button-el-inner"
                                    ></div>
                                </div>
                                <div class="checkbox__button-desc">
                                    <span>Я стример</span>
                                    <p>Стример мод</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button
            class="carousel__button is-close"
            data-fancybox-close
            title="Close"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                tabindex="-1"
            >
                <path d="M20 20L4 4m16 0L4 20"></path>
            </svg>
        </button>
    </div>
</template>

<script>
import tippy from "tippy.js";
import Cookies from "js-cookie";

export default {
    watch: {
        "$frontSettings.streamer"(val) {
            Cookies.set("streamer", val, { expires: 365 });
        },
        "$frontSettings.sounds"(val) {
            Cookies.set("sounds", val, { expires: 365 });
        },
    },
    mounted() {
        const streamerCookie = Cookies.get("streamer");
        if (streamerCookie !== undefined)
            this.$frontSettings.streamer = streamerCookie === "true";
        const soundsCookie = Cookies.get("sounds");
        if (soundsCookie !== undefined)
            this.$frontSettings.sounds = soundsCookie === "true"; // true/false

        tippy(".tooltip-bottom", {
            animation: "scale-subtle",
            theme: "light-border",
            placement: "right",
            duration: [200, 150],
            delay: [100, 50],
            arrow: true,
        });
    },
};
</script>
