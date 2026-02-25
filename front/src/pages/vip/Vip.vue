<template>
    <div class="page profile vip-club">
        <div class="page__header">
            <div class="page__header-left">
                <router-link :to="{ name: 'index' }" class="page__header-back">
                    <div
                        class="icon"
                        style="mask-image: url('images/icons/arrow-left.svg')"
                    ></div>
                </router-link>
                <div class="page__header-info">
                    <div class="page__header-info-inner">
                        <span>V.I.P Клуб</span>
                        <p>Зарабатывай на выгодных условиях</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="page__body">
            <div
                class="car-banner-block_root-TOvyO vip_block-eCImg"
                id="car-banner"
            >
                <h1 class="car-banner-block_title-TOvyO">
                    <div class="custom-html_root-jGMem">
                        Почему {{ settings.site_name }} <br />
                        <span>VIP CLUB</span> <br />
                        лучший?
                    </div>
                </h1>
            </div>
            <div
                class="vip-manager-block_root-W6fLa vip_block-eCImg"
                id="vip-manager-banner"
            >
                <div class="vip-manager-block_textContainer-W6fLa">
                    <div class="vip-manager-block_titleContainer-W6fLa">
                        <h2 class="vip-manager-block_title-W6fLa">
                            VIP-менеджер
                        </h2>
                        <span class="vip-manager-block_badge-W6fLa"
                            >24/7<span
                                class="vip-manager-block_dot-W6fLa"
                            ></span
                        ></span>
                    </div>
                    <div class="vip-manager-block_description-W6fLa">
                        Вознаградит за игровую активность и поможет во время
                        игры
                    </div>
                </div>
                <div class="vip-manager-block_managers-W6fLa">
                    <a class="tooltip-bottom"
                        ><img
                            srcset="
                                /assets/avatars/1.png,
                                /assets/avatars/1.png 2x
                            "
                            class="" /></a
                    ><a class="tooltip-bottom"
                        ><img
                            srcset="
                                /assets/avatars/2.png,
                                /assets/avatars/2.png 2x
                            "
                            class="" /></a
                    ><a class="tooltip-bottom"
                        ><img
                            srcset="
                                /assets/avatars/3.png,
                                /assets/avatars/3.png 2x
                            "
                            class=""
                    /></a>
                </div>
            </div>
            <div class="vip_block-eCImg" id="exclusive-gifts">
                <h2 class="exclusive-gifts-block_title-PK8jg">
                    Эксклюзивные условия
                </h2>
                <div class="exclusive-gifts-block_container-PK8jg">
                    <div
                        class="exclusive-gifts-card_root-9LqCC"
                        style="
                            background: linear-gradient(180deg, #5E008C 0%, #A300F2 100%);
                            grid-area: a1;
                        "
                    >
                        <picture
                            class="picture-v2_root-uw4G6"
                            style="--5e29e696: none"
                            ><img
                                class="exclusive-gifts-card_image-9LqCC"
                                srcset="
                                    /assets/images/vipclub/gift.png,
                                    /assets/images/vipclub/gift.png      2x
                                "
                        /></picture>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                            Еженедельный гарантированный бонус
                        </p>
                    </div>
                    <div
                        class="exclusive-gifts-card_root-9LqCC"
                        style="
                            background: linear-gradient(180deg, #004670 0%, #0085D2 100%);
                            grid-area: a2;
                        "
                    >
                        <picture
                            class="picture-v2_root-uw4G6"
                            style="--5e29e696: none"
                            ><img
                                class="exclusive-gifts-card_image-9LqCC"
                                srcset="
                                    /assets/images/vipclub/aid.png,
                                   /assets/images/vipclub/aid.png      2x
                                "
                        /></picture>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                            Срочную аптечку  
                        </p>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                             в случае проигрыша
                        </p>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                             для возможности отыграться
                        </p>
                    </div>
                    <div
                        class="exclusive-gifts-card_root-9LqCC"
                        style="
                            background: linear-gradient(180deg, #007342 0%, #038C52 100%);
                            grid-area: a3;
                        "
                    >
                        <picture
                            class="picture-v2_root-uw4G6"
                            style="--5e29e696: none"
                            ><img
                                class="exclusive-gifts-card_image-9LqCC"
                                srcset="
                                    /assets/images/vipclub/manager.png,
                                   /assets/images/vipclub/manager.png      2x
                                "
                        /></picture>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                            Персональный Vip-менеджер
                        </p>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                         для решения любых проблем
                        </p>
                    </div>
                    <div
                        class="exclusive-gifts-card_root-9LqCC"
                        style="
                            background: linear-gradient(180deg, #66002A 0%, #AC0046 100%);
                            grid-area: a4;
                        "
                    >
                        <picture
                            class="picture-v2_root-uw4G6"
                            style="--5e29e696: none"
                            ><img
                                class="exclusive-gifts-card_image-9LqCC"
                                srcset="
                                     /assets/images/vipclub/bonus.png,
                                    /assets/images/vipclub/bonus.png      2x
                                "
                        /></picture>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                            Ежемесячный 
                        </p>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                             повышенный
                        </p>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                             гарантированный бонус
                        </p>
                    </div>
                    <div
                        class="exclusive-gifts-card_root-9LqCC"
                        style="
                            background: linear-gradient(180deg, #B22D00 0%, #EA6539 100%);
                            grid-area: a5;
                        "
                    >
                        <picture
                            class="picture-v2_root-uw4G6"
                            style="--5e29e696: none"
                            ><img
                                class="exclusive-gifts-card_image-9LqCC"
                                srcset="
                                    /assets/images/vipclub/cashback.png,
                                    /assets/images/vipclub/cashback.png      2x
                                "
                        /></picture>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                            Еженедельный кешбек в случае минуса за неделю
                        </p>
                    </div>
                    <div
                        class="exclusive-gifts-card_root-9LqCC"
                        style="
                            background: linear-gradient(180deg, #005F66 0%, #009DA7 100%);
                            grid-area: a6;
                        "
                    >
                        <picture
                            class="picture-v2_root-uw4G6"
                            style="--5e29e696: none"
                            ><img
                                class="exclusive-gifts-card_image-9LqCC"
                                srcset="
                                    /assets/images/vipclub/cashback-2.png,
                                    /assets/images/vipclub/cashback-2.png      2x
                                "
                        /></picture>
                        <p class="exclusive-gifts-card_subTitle-9LqCC">
                            Ежемесячный повышенный кешбек в случае минуса за месяц
                        </p>
                    </div>
                </div>
            </div>
            <div
                v-if="user.is_vip"
                class="vip-status-block_root-n2tuS vip_block-eCImg"
                id="vip-status-component"
                style="background: #005f66"
            >
                <picture class="picture-v2_root-uw4G6" style="--5e29e696: none"
                    ><img
                        class="vip-status-block_image-n2tuS vip-status-block_bg-n2tuS"
                        srcset="
                            /images/vipclub/bg-complete.png,
                            /images/vipclub/bg-complete.png 2x
                        " /></picture
                ><picture class="picture-v2_root-uw4G6" style="--5e29e696: none"
                    ><img
                        class="vip-status-block_image-n2tuS vip-status-block_start-n2tuS"
                        srcset="
                            /images/vipclub/left.png,
                            /images/vipclub/left.png 2x
                        " /></picture
                ><picture class="picture-v2_root-uw4G6" style="--5e29e696: none"
                    ><img
                        class="vip-status-block_image-n2tuS vip-status-block_end-n2tuS"
                        srcset="
                            /images/vipclub/right.png,
                            /images/vipclub/right.png 2x
                        "
                /></picture>
                <div class="vip-status-block_container-n2tuS">
                    <picture
                        class="picture-v2_root-uw4G6"
                        style="--5e29e696: none"
                        ><img
                            class="vip-status-block_middleImage-n2tuS"
                            srcset="
                                /images/vipclub/middle-completed.png,
                                /images/vipclub/middle-completed.png 2x
                            "
                    /></picture>
                    <h2 class="vip-status-block_title-n2tuS">
                        <div class="custom-html_root-jGMem">
                            {{ settings.site_name }} <br /><span>
                                Private Club
                            </span>
                        </div>
                    </h2>
                    <p class="vip-status-block_subTitle-n2tuS">
                        Закрытые турниры и эксклюзивные подарки в закрытом клубе
                    </p>
                    <div class="vip-status-block_button-n2tuS">
                        Перейти в Telegram
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useMainStore } from "@/stores/main.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
    setup() {
        // Инициализируем SEO для страницы VIP клуба
        const { seoData, updateOpenGraph } = useSeo("vip");

        return {
            seoData,
            updateOpenGraph
        };
    },
    computed: {
        ...mapState(useAuthStore, ["isAuth", "user"]),
        ...mapState(useMainStore, ["settings"]),
    },
    methods: {
        ...mapActions(useMainStore, ["getSettings"]),
    },
};
</script>
