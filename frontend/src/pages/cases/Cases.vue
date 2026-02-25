<template>
  <div class="page case">
    <CasesHeader :box="box" />
    <div class="page__body">
        <CasesTop :isLoading="isLoading" :box="box" :caseContent="caseContent" />
        <Inventory :caseContent="caseContent" />
    </div>
  </div>
</template>
<script>
import CasesHeader from "@/pages/cases/components/CasesHeader.vue";
import CasesTop from "@/pages/cases/components/CasesTop.vue";
import Inventory from "@/pages/cases/components/Inventory.vue";
import { request } from "@/utils/request.js";
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useCaseSeo } from "@/composables/useSeo.js";
import { getItemRarityClass } from "@/helpers/helpers.js";

export default {
  components: { CasesHeader, CasesTop, Inventory },
  setup() {
    // Инициализируем SEO для страницы кейса
    const { seoData, updateCaseData, updateOpenGraph } = useCaseSeo();

    return {
      seoData,
      updateCaseData,
      updateOpenGraph
    };
  },
  data() {
    return {
      caseContent: [],
      box: {},
      isLoading: true,
    };
  },
  mounted() {
    this.getBox();
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
  methods: {
    ...mapActions(useAuthStore, ["logOut", "getUser"]),
    sortItemsByRarity(items) {
      // Определяем порядок раритетности от самого редкого к самому распространенному
      const rarityOrder = {
        'rare': 1,
        'covert': 2,
        'classified': 3,
        'restricted': 4,
        'milspec': 5,
        'industrial': 6,
        'consumer': 7
      };

      return items.sort((a, b) => {
        const rarityA = getItemRarityClass(a.rarity);
        const rarityB = getItemRarityClass(b.rarity);
        return rarityOrder[rarityA] - rarityOrder[rarityB];
      });
    },
    async getBox() {
      this.isLoading = true;
      try {
        await request("GET", "/case/one", {
          url: this.$route.params.url,
        }).then(({ data }) => {
          if (!data.success) {
            this.$toastr.error(data.message);
          } else {
            this.caseContent = this.sortItemsByRarity(data.items);
            this.box = data.case;
            
            // Обновляем SEO данные после загрузки информации о кейсе
            this.updateCaseData({
              name: data.case.name,
              image: data.case.image,
              price: data.case.price,
              items_count: data.items.length,
              caseName: data.case.name,
              casePrice: data.case.price
            });

            // Динамически обновляем Open Graph теги для лучшего превью в соцсетях
            this.updateOpenGraph({
              title: `${data.case.name} - Открыть кейс КС 2 на SNAKEBOX`,
              description: `Открой ${data.case.name} кейс и получи шанс выиграть редкие скины CS2. Моментальный вывод в Steam, честные шансы.`,
              image: data.case.image,
              url: window.location.href
            });
          }
        });
      } finally {
        this.isLoading = false;
      }
    },
  },
};
</script>
