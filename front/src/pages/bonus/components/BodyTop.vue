<template>
    <div class="bonus__top">
        <div class="bonus__promocode">
            <div class="banners__list-item-top gray">
                <p>Активация промо</p>
                <span>Активируй промокод и бонус твой</span>
            </div>
            <div class="form-input">
                <div class="form-input__wrapp">
                    <div class="form-input__icon">
                        <div
                            class="icon"
                            style="
                                mask-image: url('/images/icons/promocode.svg');
                            "
                        ></div>
                    </div>
                    <div class="form-input__button">
                        <button
                            @click="useCode"
                            type="button"
                            class="page__header-back activatePromo"
                        >
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/check.svg');
                                "
                            ></div>
                        </button>
                    </div>
                    <input
                        type="text"
                        placeholder="Промокод"
                        v-model="code"
                        class="promocode"
                    />
                </div>
            </div>
        </div>
        <router-link :to="{ name: 'vip' }" class="banners__list-item bonus__vip">
            <div class="banners__list-item-info">
                <div class="banners__list-item-top">
                    <span>Вип-клуб</span>
                    <p>Присоединяйся к лучшим</p>
                </div>
                <div
                    class="icon gray green"
                    style="mask-image: url('/assets/icons/arrow-top-right.svg')"
                ></div>
            </div>
            <div class="banners__list-item-image">
                <img src="/assets/images/crown.png" alt="" />
            </div>
        </router-link>
        <router-link :to="{ name: 'raffle' }" class="banners__list-item bonus__vip">
            <div class="banners__list-item-info">
                <div class="banners__list-item-top">
                    <span>Розыгрыши</span>
                    <p>3 активных розыгрыша!</p>
                </div>
                <div
                    class="icon gray green"
                    style="mask-image: url('/assets/icons/arrow-top-right.svg')"
                ></div>
            </div>
            <div class="banners__list-item-image">
                <img src="/assets/images/giveaway.png" alt="" />
            </div>
        </router-link>
       
        <router-link :to="{ name: 'event' }" class="banners__list-item event">
            <div class="banners__list-item-info">
                <div class="banners__list-item-top">
                    <span>В поиске сокровищ</span>
                    <p>Активный ивент</p>
                </div>
                <div
                    class="icon gray green"
                    style="mask-image: url('/assets/icons/arrow-top-right.svg')"
                ></div>
            </div>
            <div class="banners__list-item-image">
                <img src="/assets/images/snake-glass.png" alt="" />
            </div>
        </router-link>
    </div>
</template>

<script>
import { request } from "@/utils/request.js";
export default {
    data() {
        return {
            code: "",
        };
    },
    methods: {
        async useCode() {
            await request("POST", "/promocodes/activate", {
                code: this.code,
            }).then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                    return;
                } else {
                    this.$toastr.success(data.message);
                }
            });
        },
    },
};
</script>
