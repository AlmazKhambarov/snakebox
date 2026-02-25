<template>
    <div id="bonus-roll" style="display: none" class="fancybox__content">
        <div class="modal bonus-roll" x-data="bonusRoll">
            <div class="modal__body">
                <div class="auth__desc">
                    <p>Барабан бонусов</p>
                    <span
                        >Раскрути барабан <br />
                        и получи бонус</span
                    >
                </div>
                <div class="bonus-roll__wrapp">
                    <div class="bonus-roll__wrapp-inner">
                        <div class="bonus-roll__wrapp-ctx">
                            <img
                                src="/images/wheel.svg?v=2"
                                class="bonus-roll__wheel"
                                alt="Бонусное колесо фортуны"
                            />
                        </div>
                        <img
                            src="/images/cursor.svg"
                            class="bonus-roll__cursor"
                            alt="Указатель для вращения колеса"
                        />
                    </div>
                    <div class="bonus-roll__bottom">
                        <button
                            type="button"
                            class="btn wheel-roll-button wheelBonusRoll"
                        >
                            <div class="btn__inner">
                                <div class="btn__inner-left">
                                    <span>Крутить барабан</span>
                                    <p>Желаем вам удачи!</p>
                                </div>
                            </div>
                        </button>

                        <div
                            class="bonus-roll__timer bonusWheelTimer"
                            style="display: none"
                        >
                            <span>До бесплатного открытия осталось:</span>
                            <div class="banners__event-timer">
                                <div class="banners__event-timer-item">
                                    <span class="timer-days">00</span>
                                    <p>дни</p>
                                </div>
                                <div class="banners__event-timer-item">
                                    <span class="timer-hours">00</span>
                                    <p>часы</p>
                                </div>
                                <div class="banners__event-timer-item">
                                    <span class="timer-minutes">00</span>
                                    <p>мин</p>
                                </div>
                                <div class="banners__event-timer-item">
                                    <span class="timer-seconds">00</span>
                                    <p>сек</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <img
                        src="/assets/images/snake-glass.png"
                        class="bonus-roll__dolphin"
                        alt="Талисман Dolphin Win"
                    />
                </div>
                <div class="bonus-roll__list">
                    <span>Что может выпасть?</span>
                    <div class="bonus-roll__drops">
                        <button
                            type="button"
                            @click="select('Предмет', 'skin')"
                            class="bonus-roll__drops-item"
                        >
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/skins.svg');
                                "
                            ></div>
                            <span>Предмет</span>
                        </button>
                        <button
                            type="button"
                            @click="select('Код', 'code')"
                            class="bonus-roll__drops-item"
                        >
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/promocode.svg');
                                "
                            ></div>
                            <span>Код</span>
                        </button>
                        <button
                            type="button"
                            @click="select('Деньги', 'money')"
                            class="bonus-roll__drops-item"
                        >
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/money.svg');
                                "
                            ></div>
                            <span>Деньги</span>
                        </button>
                        <button
                            type="button"
                            @click="select('Кейс', 'case')"
                            class="bonus-roll__drops-item"
                        >
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/case.svg');
                                "
                            ></div>
                            <span>Кейс</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="bonus-roll__open">
                <div class="bonus-roll__open-wrapp">
                    <div class="bonus-roll__open-title">
                        <button
                            type="button"
                            class="bonus-roll__open-close"
                            @click="close"
                        >
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/arrow-left.svg');
                                "
                            ></div>
                        </button>
                        <div class="auth__desc">
                            <span x-text="item.name"></span>
                            <p>Из этого бонуса выпадают такие призы:</p>
                        </div>
                    </div>
                    <div class="bonus-roll__open-wrapp-list">
                        <div
                            class="bonus-roll__open-wrapp-list-item"
                            x-show="item.key === 'skin'"
                            style="display: none"
                        >
                            <p>Предмет ценой до 500₽</p>
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/skins.svg');
                                "
                            ></div>
                        </div>
                        <div
                            class="bonus-roll__open-wrapp-list-item"
                            x-show="item.key === 'code'"
                            style="display: none"
                        >
                            <p>Секретный код 10% к пополнению</p>
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/promocode.svg');
                                "
                            ></div>
                        </div>
                        <div
                            class="bonus-roll__open-wrapp-list-item"
                            x-show="item.key === 'code'"
                            style="display: none"
                        >
                            <p>Секретный код 15% к пополнению</p>
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/promocode.svg');
                                "
                            ></div>
                        </div>
                        <div
                            class="bonus-roll__open-wrapp-list-item"
                            x-show="item.key === 'money'"
                            style="display: none"
                        >
                            <p>До 500₽ на баланс</p>
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/assets/icons/coin.svg');
                                "
                            ></div>
                        </div>
                        <div
                            class="bonus-roll__open-wrapp-list-item"
                            x-show="item.key === 'case'"
                            style="display: none"
                        >
                            <p>Любой кейс до 500₽</p>
                            <div
                                class="icon"
                                style="
                                    mask-image: url('/images/icons/refferal.svg');
                                "
                            ></div>
                        </div>
                    </div>
                </div>
                <div
                    class="bonus-roll__open-overlay js--bonus-roll-close"
                ></div>
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
