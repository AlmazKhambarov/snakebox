<template>
    <div class="page__header">
        <div class="page__header-left">
            <router-link :to="{ name: 'index' }" class="page__header-back">
                <div
                    class="icon"
                    style="mask-image: url('/images/icons/arrow-left.svg')"
                ></div>
            </router-link>
            <div class="page__header-info">
                <div class="page__header-info-inner">
                    <span>Профиль</span>
                    <p>Управление аккаунтом</p>
                </div>
            </div>
        </div>
    </div>
</template>
