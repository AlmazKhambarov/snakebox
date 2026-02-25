/**
 * Примеры использования SEO системы для разных страниц
 * 
 * Этот файл содержит примеры того, как интегрировать SEO в различные компоненты
 */

// Пример 1: Простая страница (например, бонусы)
/*
import { useSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData } = useSeo('bonus');
    return { seoData };
  }
}
*/

// Пример 2: Страница с динамическими данными (например, кейс)
/*
import { useCaseSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData, updateCaseData, updateOpenGraph } = useCaseSeo();

    return {
      seoData,
      updateCaseData,
      updateOpenGraph
    };
  },
  methods: {
    async loadCaseData() {
      const caseData = await this.fetchCase();
      this.updateCaseData({
        name: caseData.name,
        image: caseData.image,
        price: caseData.price,
        items_count: caseData.items.length
      });
    }
  }
}
*/

// Пример 3: Страница с пользовательскими данными (например, профиль)
/*
import { useSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData, updateTitle, updateOpenGraph } = useSeo('profile');
    
    return {
      seoData,
      updateTitle,
      updateOpenGraph
    };
  },
  computed: {
    ...mapState(useAuthStore, ['user'])
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

// Пример 4: Страница с параметрами роута (например, приглашение)
/*
import { useDynamicSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData, updateVariables, updateOpenGraph } = useDynamicSeo('invite');

    return {
      seoData,
      updateVariables,
      updateOpenGraph
    };
  },
  mounted() {
    // Обновляем SEO с данными о пригласившем пользователе
    this.updateVariables({
      inviterName: this.$route.query.inviter || 'друг'
    });
  }
}
*/

// Пример 5: Страница с множественными обновлениями SEO
/*
import { useSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData, updateSeo, updateTitle, updateDescription, updateOpenGraph } = useSeo('event');
    
    return {
      seoData,
      updateSeo,
      updateTitle,
      updateDescription,
      updateOpenGraph
    };
  },
  methods: {
    async loadEventData() {
      const eventData = await this.fetchEvent();
      
      // Обновляем все SEO данные сразу
      this.updateSeo({
        eventName: eventData.name,
        eventPrize: eventData.prize,
        eventEndDate: eventData.end_date
      });
      
      // Или обновляем отдельные элементы
      this.updateTitle(`${eventData.name} - Событие | ZeusDrop`);
      this.updateDescription(`Участвуйте в событии "${eventData.name}" и выигрывайте ${eventData.prize}!`);
    }
  }
}
*/

// Пример 6: Использование в роутере для автоматического SEO
/*
// В router/index.js можно добавить глобальные guards для SEO
import { initSeo } from '@/utils/seo.js';

router.beforeEach((to, from, next) => {
  // Автоматически устанавливаем SEO для каждой страницы
  const pageName = to.name;
  const variables = to.params;
  
  if (pageName && SEO_CONFIG[pageName]) {
    initSeo(pageName, variables);
  }
  
  next();
});
*/

// Пример 7: SEO для страниц с фильтрацией и поиском
/*
import { useSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData, updateSeo, updateOpenGraph } = useSeo('index');

    return {
      seoData,
      updateSeo,
      updateOpenGraph
    };
  },
  watch: {
    '$route.query': {
      handler(newQuery) {
        // Обновляем SEO при изменении фильтров
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

// Пример 8: SEO для страниц с пагинацией
/*
import { useSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData, updateSeo, updateOpenGraph } = useSeo('index');

    return {
      seoData,
      updateSeo,
      updateOpenGraph
    };
  },
  watch: {
    '$route.query.page': {
      handler(newPage) {
        if (newPage && newPage > 1) {
          this.updateSeo({
            page: newPage
          });
        }
      }
    }
  }
}
*/

// Пример 9: SEO для модальных окон (если нужно)
/*
// В компоненте модального окна
import { useSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { updateTitle, updateOpenGraph } = useSeo('index');

    return {
      updateTitle,
      updateOpenGraph
    };
  },
  methods: {
    openModal() {
      // Временно обновляем title для модального окна
      this.updateTitle('Открыть кейс - ZeusDrop');
    },
    closeModal() {
      // Возвращаем оригинальный title
      this.updateTitle('ZeusDrop - Открытие кейсов CS2');
    }
  }
}
*/

// Пример 10: SEO для страниц ошибок
/*
import { useSeo } from '@/composables/useSeo.js';

export default {
  setup() {
    const { seoData, updateSeo, updateOpenGraph } = useSeo('index');

    return {
      seoData,
      updateSeo,
      updateOpenGraph
    };
  },
  mounted() {
    // Устанавливаем SEO для страницы ошибки
    this.updateSeo({
      errorCode: this.$route.params.code || '404',
      errorMessage: 'Страница не найдена'
    });
  }
}
*/
