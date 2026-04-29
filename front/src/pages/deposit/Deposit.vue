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
                                {{ method.min_amount }} {{ ['nirvana_uzs', 'payme'].includes(method.system) ? 'UZS' : 'RUB' }}
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
                                                ? selectedMethod.min_amount.toFixed(2)
                                                : "1.00"
                                        }}
                                        {{ ['nirvana_uzs', 'payme'].includes(selectedMethod?.system) ? 'UZS' : 'RUB' }}
                                    </span>
                                </div>
                            </div>
                            <div class="deposit__sum">
                                <div class="form-input__wrapp">
                                    <div class="form-input__icon">
                                        <div
                                            class="icon"
                                            style="
                                                mask-image: url('/images/icons/uzs.svg');
                                            "
                                        ></div>
                                    </div>
                                    <input
                                        type="number"
                                        placeholder="50"
                                        v-model="amount"
                                        class="paymentAmountRUB"
                                    />
                                    <div class="form-input__conversion" v-if="['nirvana_uzs', 'payme'].includes(selectedMethod?.system)">
                                        ≈ {{ (amount / 156.25).toFixed(2) }} RUB
                                    </div>
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
                                    (['nirvana_uzs', 'payme'].includes(selectedMethod?.system) ? amountWithPercent / 156.25 : amountWithPercent).toFixed(2)
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
                                <span class="totalAmount">{{ (['nirvana_uzs', 'payme'].includes(selectedMethod?.system) ? amountWithPercent / 156.25 : amountWithPercent).toFixed(2) }}</span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- P2P Card Details Modal -->
        <div class="deposit__card-modal" v-if="cardDetails">
            <div class="deposit__card-modal-overlay" @click="cardDetails = null"></div>
            <div class="deposit__card-modal-content">
                <div class="deposit__card-modal-header">
                    <span>Реквизиты для оплаты</span>
                    <button @click="cardDetails = null" class="deposit__card-modal-close">&times;</button>
                </div>
                <div class="deposit__card-modal-body">
                    <div class="deposit__card-modal-field">
                        <label>Банк</label>
                        <span>{{ cardDetails.bankName }}</span>
                    </div>
                    <div class="deposit__card-modal-field">
                        <label>Номер карты</label>
                        <div class="deposit__card-modal-copy">
                            <span>{{ cardDetails.receiver }}</span>
                            <button @click="copyCard(cardDetails.receiver)" class="deposit__card-modal-copy-btn">Копировать</button>
                        </div>
                    </div>
                    <div class="deposit__card-modal-field">
                        <label>Получатель</label>
                        <span>{{ cardDetails.recipientName }}</span>
                    </div>
                    <div class="deposit__card-modal-field">
                        <label>Сумма к оплате</label>
                        <span class="deposit__card-modal-amount">{{ cardDetails.amount }} {{ cardDetails.currency }}</span>
                    </div>
                    <div class="deposit__card-modal-warning">
                        ⚠️ Переведите <b>точную сумму</b> на указанную карту. После оплаты баланс пополнится автоматически в течение 5 минут.
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
            cardDetails: null,
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
                } else if (data.type === 'card') {
                    this.cardDetails = {
                        receiver: data.receiver,
                        bankName: data.bankName,
                        recipientName: data.recipientName,
                        amount: data.amount,
                        currency: data.currency,
                    };
                    this.$toastr.success(data.message);
                } else {
                    this.$toastr.success(data.message);
                    setTimeout(() => {
                        window.location.href = data.link;
                    }, 1000);
                }
            });
        },
        copyCard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.$toastr.success('Скопировано!');
            });
        },
        selectMethod(method) {
            this.selectedMethod = method;
        },
    },
};
</script>

<style scoped>
.deposit__card-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; display: flex; align-items: center; justify-content: center; }
.deposit__card-modal-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); }
.deposit__card-modal-content { position: relative; background: #1a1a2e; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; width: 90%; max-width: 420px; padding: 24px; z-index: 1; }
.deposit__card-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.deposit__card-modal-header span { font-size: 18px; font-weight: 600; color: #fff; }
.deposit__card-modal-close { background: none; border: none; color: #888; font-size: 24px; cursor: pointer; }
.deposit__card-modal-field { margin-bottom: 16px; }
.deposit__card-modal-field label { display: block; font-size: 12px; color: #888; margin-bottom: 4px; }
.deposit__card-modal-field span { font-size: 16px; color: #fff; }
.deposit__card-modal-copy { display: flex; align-items: center; gap: 10px; }
.deposit__card-modal-copy span { font-size: 20px; font-weight: 600; letter-spacing: 2px; color: #4ade80; }
.deposit__card-modal-copy-btn { background: rgba(74,222,128,0.15); border: 1px solid rgba(74,222,128,0.3); color: #4ade80; padding: 6px 14px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: background 0.2s; }
.deposit__card-modal-copy-btn:hover { background: rgba(74,222,128,0.25); }
.deposit__card-modal-amount { font-size: 22px !important; font-weight: 700; color: #4ade80 !important; }
.deposit__card-modal-warning { margin-top: 16px; padding: 12px; background: rgba(255,170,0,0.1); border: 1px solid rgba(255,170,0,0.2); border-radius: 10px; font-size: 13px; color: #fbbf24; line-height: 1.5; }
.form-input__conversion { font-size: 13px; color: #888; margin-top: 8px; font-weight: 500; }
</style>
