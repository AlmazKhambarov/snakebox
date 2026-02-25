<template>
    <div class="page deposit">
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
                        <span>Депозит</span>
                        <p>Пополнение баланса</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="page__body">
            <div class="deposit__body">
                <div class="deposit__body-wrapp">
                    <div class="deposit__methods">
                        <button
                            type="button"
                            v-for="method in methods"
                            :key="method.id"
                            class="deposit__method"
                            :class="{
                                active: method.id === selectedMethod?.id,
                            }"
                            @click="selectMethod(method)"
                            :data-method="method.id"
                            :data-name="method.name"
                            :data-min="method.min_amount"
                        >
                            <div class="deposit__method-min" v-if="method.system !== 'cryptocloud'">
                                {{ method.min_amount }} RUB
                            </div>
                            <div class="deposit__method-min" v-if="method.system === 'cryptocloud'">
                                {{ method.name }}
                            </div>
                            <img
                                :src="method.icon"
                                class="deposit__method-image"
                                alt=""
                            />
                            <div class="deposit__method-bg"></div>
                        </button>
                    </div>
                </div>
                <div class="deposit__form">
                    <div class="deposit__form-card">
                        <div class="deposit__select-pay">
                            <div class="deposit__select-pay-info">
                                <p>Выбранный способ оплаты</p>
                                <div class="deposit__select-pay-system">
                                    <img
                                        :src="
                                            selectedMethod?.icon ||
                                            '/assets/icons/snake.svg'
                                        "
                                        alt="Inwizo"
                                    />
                                    <span>{{
                                        selectedMethod?.name || "Отсутствует"
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="deposit__form-card">
                        <div class="form-input">
                            <div class="form-input__label">
                                Промокод или реферальный код
                            </div>
                            <div
                                class="form-input__wrapp"
                                :class="{ success: success }"
                            >
                                <div class="form-input__icon">
                                    <div
                                        class="icon"
                                        style="
                                            mask-image: url('images/icons/promocode.svg');
                                        "
                                    ></div>
                                </div>
                                <div class="form-input__button">
                                    <button
                                        @click="checkPromoCode"
                                        type="button"
                                        class="page__header-back checkPaymentPromo"
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
                                    class="paymentPromo"
                                    v-model="promoCode"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="deposit__form-card">
                        <div class="form-input">
                            <div class="form-input__label">
                                Сумма пополнения
                                <div class="form-input__label-right">
                                    Минимум
                                    <span id="minDeposit">
                                        {{
                                            selectedMethod
                                                ? selectedMethod.min_amount.toFixed(
                                                      2
                                                  )
                                                : "1.00"
                                        }}
                                        RUB
                                    </span>
                                </div>
                            </div>
                            <div class="deposit__sum">
                                <div class="form-input__wrapp">
                                    <div class="form-input__icon">
                                        <div
                                            class="icon"
                                            style="
                                                mask-image: url('/images/icons/rub.svg');
                                            "
                                        ></div>
                                    </div>
                                    <input
                                        type="number"
                                        placeholder="50"
                                        v-model="amount"
                                        class="paymentAmountRUB"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="deposit__you-earn">
                        <div class="deposit__you-earn-text">
                            <div class="sum">
                                <div
                                    class="icon coin"
                                    style="
                                        mask-image: url('/assets/icons/coin.svg');
                                    "
                                ></div>
                                <span class="totalAmountPromo">{{
                                    amountWithPercent.toFixed(2)
                                }}</span>
                            </div>
                            <span>Вы получите</span>
                        </div>
                        <div class="deposit__you-earn-promo" v-show="success">
                            <span
                                >С учётом промокода
                                <span class="promoPercent"
                                    >+{{ percent }}%</span
                                ></span
                            >
                        </div>
                    </div>
                    <button
                        @click="createPayment"
                        type="button"
                        class="btn btn--justify createPayment"
                    >
                        <div class="btn__inner">
                            <div class="btn__inner-left">
                                <span>Пополнить баланс</span>
                                <p>Зачисление до 5 минут</p>
                            </div>
                            <div class="sum sum--sm">
                                <div
                                    class="icon coin"
                                    style="
                                        mask-image: url('images/icons/coin.svg');
                                    "
                                ></div>
                                <span class="totalAmount">{{ amount }}</span>
                            </div>
                        </div>
                    </button>
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
        // Инициализируем SEO для страницы депозита
        const { seoData, updateOpenGraph } = useSeo('deposit');
        
        return {
            seoData,
            updateOpenGraph
        };
    },
    data() {
        return {
            amount: 50,
            methods: [],
            selectedMethod: null,
            promoCode: null,
            debounceTimer: null,
            success: false,
            percent: 0,
        };
    },

    mounted() {
        this.getMethods();
    },
    computed: {
        amountWithPercent() {
            if (this.success) {
                return this.amount * (1 + this.percent / 100);
            } else {
                return this.amount;
            }
        },
    },

    watch: {
        success(newVal) {
           
        },

        percent(newVal) {
           
        },

        amountWithPercent(newVal) {
        },
    },
    methods: {
        async getMethods() {
            await request("GET", "/payment/methods").then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                } else {
                    this.methods = data.methods;
                }
            });
        },
        async checkPromoCode() {
            await request("POST", "/payment/check-promo", {
                code: this.promoCode,
            }).then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                    this.success = false;
                    this.percent = 0;
                } else {
                    this.$toastr.success(data.message);
                    this.success = data.success;
                    this.percent = data.percent;
                }
            });
        },
        async createPayment() {
            if (!this.selectedMethod) {
                this.$toastr.error("Выберите способ оплаты");
                return;
            }
            await request("POST", this.selectedMethod.api_url, {
                payment_method: this.selectedMethod.method,
                system: this.selectedMethod.system,
                amount: this.amount,
                promocode: this.promoCode,
            }).then(({ data }) => {
                if (!data.success) {
                    this.$toastr.error(data.message);
                } else {
                    this.$toastr.success(data.message);
                    setTimeout(() => {
                        window.location.href = data.link;
                    }, 1000);
                }
            });
        },
        selectMethod(method) {
            this.selectedMethod = method;
        },
    },
};
</script>
