<template>
    <div id="cashback" style="display: none" class="fancybox__content">
        <div class="modal settings">
            <div class="modal__body">
                <div
                    class="modal-header_root-90muI"
                    style="--2232a396: #ffffff"
                >
                    <!---->
                    <div class="modal-header_primaryHeader-90muI">
                        <!---->

                        <div class="modal-header_info-90muI">
                            <div class="modal-header_titleButton-90muI">
                                <div
                                    class="modal-header_title-90muI"
                                    data-testid="modal-header-title"
                                >
                                    Индивидуальный кэшбэк
                                </div>
                                <!---->
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <div class="modal-info_root-p15an">
                    <div
                        class="modal-info_subtitle-p15an"
                        style="color: var(--gray-light)"
                    >
                        VIP-менеджер организует возврат средств на выгодных для
                        вас условиях. Получайте кэшбэк, полностью адаптированный
                        под вашу игровую активность
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
