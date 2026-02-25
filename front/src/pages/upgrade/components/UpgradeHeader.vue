<template>
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
                    <span>Апгрейд</span>
                    <p>Испытай свою удачу</p>
                </div>
            </div>
        </div>
        <div class="page__header-right">
            <button
                @click="toggleSound"
                :class="{ off: !$frontSettings.sounds }"
                class="page__header-back sound click"
            >
                <div
                    class="icon"
                    style="mask-image: url('/images/icons/sound-on.svg')"
                ></div>
                <div
                    class="icon off"
                    style="mask-image: url('/images/icons/sound-off.svg')"
                ></div>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        toggleSound() {
            this.$frontSettings.sounds = !this.$frontSettings.sounds;
            this.$playSound("/sounds/click.mp3");
        },
    },
};
</script>
