<template>
  <div class="page profile">
    <ContractsHeader />
    <div class="page__body">
      <ContractsTop
          :selectedItems="selectedItems"
          :toggleItem="toggleItem"
          :type="type"
          :createContract="createContract"
          :winItem="winItem"
          :reset="reset"
          :resetItems="resetItems"
          @change-type="type = $event"
        />
      <LoadingSpinner v-if="isLoading" text="Загрузка предметов..." />
      <template v-else>
        <Inventory
          :user-items="userItems"
          :page="page"
          :has-more-pages="hasMorePages"
          :total-pages="totalPages"
          :toggleItem="toggleItem"
          :isItemSelected="isItemSelected"
          @change-page="getItems"
        />
      </template>
    </div>
  </div>
</template>
<script>
import ContractsHeader from "@/pages/contracts/components/Header.vue";
import ContractsTop from "@/pages/contracts/components/ContractsTop.vue";
import Inventory from "@/pages/contracts/components/Inventory.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import { useSeo } from "@/composables/useSeo.js";

import { request } from "@/utils/request.js";

export default {
  components: { ContractsHeader, ContractsTop, Inventory, LoadingSpinner },
  setup() {
    // Инициализируем SEO для страницы контрактов
    const { seoData, updateOpenGraph } = useSeo('contracts');
    
    return {
      seoData,
      updateOpenGraph
    };
  },
  data() {
    return {
      userItems: [],
      page: 1,
      hasMorePages: true,
      totalPages: 999,
      selectedItems: [],
      type: "low",
      winItem: null,
      isLoading: true,
    };
  },
  mounted() {
    this.getItems();
  },
  computed: {
    sumPriceInSelectedItems() {
      return this.selectedItems.reduce((sum, item) => sum + item.price, 0);
    },
  },

  methods: {
    async getItems(newPage = null) {
      if (newPage !== null) {
        this.page = newPage;
      }

      this.isLoading = true;
      try {
        await request("GET", "/contracts/user/items", {
          page: this.page,
        }).then(({ data }) => {
          if (!data.success) {
            this.$toastr.error(data.message);
          } else {
            this.userItems = data.items.data;
            this.hasMorePages = data.hasMorePages;
            this.totalPages = data.items.last_page;
          }
        });
      } finally {
        this.isLoading = false;
      }
    },
    createContract() {
      const liveIds = this.selectedItems.map((item) => item.id);

      request("POST", "/contracts/create", {
        liveIds,
        type: this.type,
      }).then(async ({ data }) => {
        if (!data.success) {
          this.$toastr.error(data.message);
        } else {
          this.winItem = data.winItem;
          this.$playSound("/sounds/contract-run.mp3");
          this.resetItems();
        }
      });
    },
    resetItems() {
      this.page = 1;
      this.selectedItems = [];
      (this.type = "low"), this.getItems();
    },
    reset() {
      this.winItem = null;
    },
    toggleItem(item) {
      this.$playSound("/sounds/click.mp3");
      const index = this.selectedItems.indexOf(item);
      if (index !== -1) {
        this.selectedItems.splice(index, 1);
      } else {
        if (this.selectedItems.length === 10) return;
        this.selectedItems.push(item);
      }
    },
    isItemSelected(item) {
      return this.selectedItems.includes(item);
    },
  },
};
</script>
