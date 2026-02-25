<template>
  <div class="page__header">
    <div class="page__header-left">
      <router-link :to="{ name: 'index' }" class="page__header-back">
        <div class="icon" style="mask-image: url('/images/icons/arrow-left.svg')"></div>
      </router-link>
      <div class="page__header-info">
        <div class="page__header-info-inner">
          <span>Бонусы</span>
          <p>Халява от {{ settings.site_name }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useMainStore } from "@/stores/main.store.js";

export default {
  computed: {
    ...mapState(useMainStore, ["settings"]),
  },
  methods: {
    ...mapActions(useMainStore, ["getSettings"]),
  },
};
</script>
