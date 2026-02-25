<template>
    <div class="gamesLeft gpu-boost">
        <router-link
            :to="{ name: 'index' }"
            class="gamesLeft__link tooltip"
            :class="{ active: $route.name === 'index' }"
            data-tippy-content="Кейсы"
        >
            <img src="/assets/images/wheel.png" alt="" />
        </router-link>
        <router-link
            :to="{ name: 'contracts' }"
            :class="{ active: $route.name === 'contracts' }"
            class="gamesLeft__link tooltip"
            data-tippy-content="Контракты"
        >
            <img src="/assets/images/contract.png" alt="" />
        </router-link>
        <router-link
            :to="{ name: 'upgrade' }"
            :class="{ active: $route.name === 'upgrade' }"
            class="gamesLeft__link tooltip"
            data-tippy-content="Апгрейд"
        >
            <img src="/assets/images/upgrade.png" alt="" />
        </router-link>
    </div>
</template>

<script>
import tippy from "tippy.js";

export default {
    mounted() {
        tippy(".tooltip", {
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
