/**
 * Быстрая интеграция SEO для всех страниц
 * 
 * Этот файл содержит готовые копи-паст решения для интеграции SEO
 * в существующие компоненты страниц
 */

// ========================================
// БОНУСЫ (frontend/src/pages/bonus/Bonus.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('bonus');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// РЕФЕРАЛЫ (frontend/src/pages/referrals/Referrals.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('referrals');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// КОНТРАКТЫ (frontend/src/pages/contracts/Contracts.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('contracts');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// АПГРЕЙД (frontend/src/pages/upgrade/Upgrade.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('upgrade');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// СОБЫТИЯ (frontend/src/pages/event/Event.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('event');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// РОЗЫГРЫШИ (frontend/src/pages/raffle/Raffle.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('raffle');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// ДЕПОЗИТ (frontend/src/pages/deposit/Deposit.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('deposit');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// ПРИГЛАШЕНИЕ (frontend/src/pages/referrals/Invite.vue)
// ========================================
/*
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useDynamicSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData, updateVariables } = useDynamicSeo('invite');
    return { seoData, updateVariables };
  },
  mounted() {
    // Обновляем SEO с кодом приглашения
    this.updateVariables({
      code: this.$route.params.code
    });
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
  },
};
</script>
*/

// ========================================
// УСЛОВИЯ ИСПОЛЬЗОВАНИЯ (frontend/src/pages/terms/Terms.vue)
// ========================================
/*
<script>
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('terms');
    return { seoData };
  },
};
</script>
*/

// ========================================
// ПОЛИТИКА КОНФИДЕНЦИАЛЬНОСТИ (frontend/src/pages/policy/Policy.vue)
// ========================================
/*
<script>
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('policy');
    return { seoData };
  },
};
</script>
*/

// ========================================
// VIP КЛУБ (frontend/src/pages/vip/Vip.vue)
// ========================================
/*
<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/stores/auth.store.js";
import { useMainStore } from "@/stores/main.store.js";
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('vip');
    return { seoData };
  },
  computed: {
    ...mapState(useAuthStore, ["isAuth", "user"]),
    ...mapState(useMainStore, ["settings"]),
  },
};
</script>
*/

// ========================================
// МОИ РЕФЕРАЛЫ (frontend/src/pages/referrals/ReferralsUsers.vue)
// ========================================
/*
<script>
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData } = useSeo('referrals-users');
    return { seoData };
  },
};
</script>
*/

// ========================================
// ПРИМЕРЫ ДОПОЛНИТЕЛЬНЫХ ВОЗМОЖНОСТЕЙ
// ========================================

// Обновление SEO при изменении данных пользователя
/*
export default {
  setup() {
    const { seoData, updateTitle } = useSeo('profile');
    return { seoData, updateTitle };
  },
  watch: {
    user: {
      handler(newUser) {
        if (newUser && newUser.username) {
          this.updateTitle(`${newUser.username} - Профиль | ZeusDrop`);
        }
      },
      immediate: true
    }
  }
}
*/

// Обновление SEO при загрузке данных события
/*
export default {
  setup() {
    const { seoData, updateSeo } = useSeo('event');
    return { seoData, updateSeo };
  },
  methods: {
    async loadEvent() {
      const eventData = await this.fetchEvent();
      this.updateSeo({
        eventName: eventData.name,
        eventPrize: eventData.prize,
        eventEndDate: eventData.end_date
      });
    }
  }
}
*/

// Обновление SEO для страниц с фильтрацией
/*
export default {
  setup() {
    const { seoData, updateSeo } = useSeo('index');
    return { seoData, updateSeo };
  },
  watch: {
    '$route.query': {
      handler(newQuery) {
        if (newQuery.category) {
          this.updateSeo({
            category: newQuery.category,
            filter: newQuery.filter || 'все'
          });
        }
      },
      immediate: true
    }
  }
}
*/

// ДИНАМИЧЕСКОЕ ОБНОВЛЕНИЕ OPEN GRAPH ТЕГОВ
// ========================================

// Пример: Обновление только Open Graph тегов при изменении контента
/*
import { useSeo } from "@/composables/useSeo.js";

export default {
  setup() {
    const { seoData, updateOpenGraph } = useSeo('case');
    return { seoData, updateOpenGraph };
  },
  methods: {
    async shareOnSocialMedia() {
      // Обновляем только Open Graph теги для лучшего превью в соцсетях
      this.updateOpenGraph({
        title: "Специальное предложение! " + this.caseName,
        description: "Только сегодня скидка 50% на этот кейс!",
        image: "/images/special-offer.jpg",
        url: window.location.href
      });
    }
  }
}
*/

// Пример: Обновление Open Graph при загрузке динамического контента
/*
export default {
  setup() {
    const { seoData, updateOpenGraph } = useCaseSeo();
    return { seoData, updateOpenGraph };
  },
  mounted() {
    this.loadCaseData();
  },
  methods: {
    async loadCaseData() {
      const caseData = await this.fetchCaseData();

      // Обновляем только Open Graph теги без изменения title и description страницы
      this.updateOpenGraph({
        title: `${caseData.name} - Лучший кейс CS2`,
        image: caseData.image,
        url: `/case/${caseData.url}`
      });
    }
  }
}
*/

// Пример: Обновление Open Graph при взаимодействии пользователя
/*
export default {
  setup() {
    const { updateOpenGraph } = useSeo('profile');
    return { updateOpenGraph };
  },
  methods: {
    onUserAchievement(achievement) {
      // Динамически обновляем Open Graph для шэринга достижения
      this.updateOpenGraph({
        title: `Достижение разблокировано: ${achievement.name}`,
        description: achievement.description,
        image: achievement.image,
        url: `${window.location.origin}/achievement/${achievement.id}`
      });
    }
  }
}
*/




