<template>
    <div class="games gpu-boost">
        <router-link :to="{ name: 'contracts' }" class="games__item mines">
            <div class="games__item-top">
                <span>Контракты</span>
                <div
                    class="icon gray green"
                    style="mask-image: url('/assets/icons/arrow-top-right.svg')"
                ></div>
            </div>
            <div class="games__item-image">
                <img
                    src="/assets/images/contract.png"
                    loading="eager"
                    fetchpriority="high"
                    alt=""
                />
            </div>
        </router-link>
        <router-link :to="{ name: 'upgrade' }" class="games__item upgrade">
            <div class="games__item-top">
                <span>Апгрейд</span>
                <div
                    class="icon gray green "
                    style="mask-image: url('/assets/icons/arrow-top-right.svg')"
                ></div>
            </div>
            <div class="games__item-image">
                <img
                    src="/assets/images/upgrade.png"
                    loading="eager"
                    fetchpriority="high"
                    alt=""
                />
            </div>
        </router-link>
        <!-- <a href="#bonus-roll" data-fancybox="" class="games__item bonusroll">
            <div class="games__item-top">
                <span
                    >Барабан <br />
                    бонусов</span
                >
                <div
                    class="icon gray"
                    style="mask-image: url('/assets/icons/arrow-top-right.svg')"
                ></div>
            </div>
            <div class="games__item-image">
                <img
                    src="/images/wheel.webp"
                    loading="eager"
                    fetchpriority="high"
                    alt="Бонусное колесо - крути и выигрывай кейсы"
                />
            </div>
        </a> -->
    </div>
</template>
