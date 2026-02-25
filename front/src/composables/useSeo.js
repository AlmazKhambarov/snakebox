/**
 * Vue Composable для SEO управления
 *
 * НОВЫЕ ВОЗМОЖНОСТИ:
 * - updateOpenGraph(ogData) - динамическое обновление только Open Graph тегов
 *   ogData: { title?, description?, image?, url? }
 *
 * ПРИМЕРЫ ИСПОЛЬЗОВАНИЯ:
 *
 * // Обновление только Open Graph тегов
 * const { updateOpenGraph } = useSeo('case');
 * updateOpenGraph({
 *   title: "Новый заголовок для соцсетей",
 *   image: "/images/new-preview.jpg",
 *   url: window.location.href
 * });
 *
 * // В useCaseSeo и useDynamicSeo метод тоже доступен
 * const { updateOpenGraph } = useCaseSeo();
 * const { updateOpenGraph } = useDynamicSeo('pageName');
 */

import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { initSeo, getSeoConfig } from '@/utils/seo.js';

/**
 * Функция для обновления структурированных данных
 */
function updateStructuredData(seoData, variables) {
  // Удаляем старые структурированные данные
  const oldScript = document.querySelector(
      'script[type="application/ld+json"]'
  );
  if (oldScript) {
      oldScript.remove();
  }

  // Создаем новые структурированные данные
  const structuredData = {
      "@context": "https://schema.org",
      "@type": "WebSite",
      name: 'snakebox.vip', // Замените на ваше значение
      url: window.location.origin,
      description: seoData?.description || '',
      potentialAction: {
          "@type": "SearchAction",
          target: `${window.location.origin}/search?q={search_term_string}`,
          "query-input": "required name=search_term_string",
      },
  };

  // Добавляем специфичные данные для кейсов
  if (variables.caseName) {
      structuredData["@type"] = "Product";
      structuredData.name = variables.caseName;
      structuredData.description = seoData?.description || '';
      structuredData.category = "CS2 Case";
      structuredData.brand = {
          "@type": "Brand",
          name: "Counter-Strike 2",
      };
      
      // Рассчитываем цену (делим на 100)
      const calculatedPrice = variables.casePrice ? (parseFloat(variables.casePrice) / 100).toFixed(2) : null;
      
      // Проверяем наличие обязательных полей для Product
      const hasOffers = calculatedPrice && calculatedPrice !== "0.00";
      const hasAggregateRating = true; // у вас жестко заданные значения
      const hasReview = false; // у вас нет отзывов
      
      if (!hasOffers && !hasAggregateRating && !hasReview) {
          console.error("Задайте значение для одного из следующих элементов данных: 'offers', 'review' или 'aggregateRating'.");
          // Не создаем структурированные данные для Product если нет обязательных полей
          return;
      }
      
      // Добавляем offers если есть цена
      if (hasOffers) {
          structuredData.offers = {
              "@type": "Offer",
              price: calculatedPrice,
              priceCurrency: "RUB",
              availability: "https://schema.org/InStock",
              seller: {
                  "@type": "Organization",
                  name: "snakebox.vip",
              },
          };
      }
      
      // Добавляем aggregateRating
      if (hasAggregateRating) {
          structuredData.aggregateRating = {
              "@type": "AggregateRating",
              ratingValue: "4.8",
              reviewCount: "1250",
          };
      }
      
      // Добавляем изображение если есть
      if (variables.caseImage) {
          structuredData.image = variables.caseImage;
      }
  }

  const script = document.createElement("script");
  script.type = "application/ld+json";
  script.textContent = JSON.stringify(structuredData);
  document.head.appendChild(script);
}

/**
 * Composable для управления SEO мета-тегами
 * @param {string} pageName - Название страницы из SEO_CONFIG
 * @param {Object} variables - Переменные для подстановки в шаблоны
 * @param {Object} options - Дополнительные опции
 */
export function useSeo(pageName, variables = {}, options = {}) {
  const route = useRoute();
  const seoData = ref(null);
  const isLoading = ref(false);

  /**
   * Обновляет SEO данные
   */
  const updateSeo = (newVariables = {}) => {
    const mergedVariables = { ...variables, ...newVariables };
    seoData.value = initSeo(pageName, mergedVariables);
    
    // Обновляем структурированные данные
    updateStructuredData(seoData.value, mergedVariables);
  };

  /**
   * Получает SEO конфигурацию без обновления DOM
   */
  const getSeo = (newVariables = {}) => {
    const mergedVariables = { ...variables, ...newVariables };
    return getSeoConfig(pageName, mergedVariables);
  };

  /**
   * Обновляет только title
   */
  const updateTitle = (title) => {
    if (title) {
      document.title = title;
    }
  };

  /**
   * Обновляет только description
   */
  const updateDescription = (description) => {
    if (description) {
      const metaTag = document.querySelector('meta[name="description"]');
      if (metaTag) {
        metaTag.setAttribute('content', description);
      }
    }
  };

  /**
   * Обновляет только Open Graph теги
   * @param {Object} ogData - Данные для Open Graph: { title, description, image, url }
   */
  const updateOpenGraph = (ogData = {}) => {
    const currentData = seoData.value || {};

    // Используем переданные данные или текущие
    const title = ogData.title || currentData.title;
    const description = ogData.description || currentData.description;
    const image = ogData.image || currentData.image;
    const url = ogData.url || (currentData.canonical ? `${window.location.origin}${currentData.canonical}` : window.location.href);

    // Обновляем og:title
    if (title) {
      updateMetaProperty('og:title', title);
    }

    // Обновляем og:description
    if (description) {
      updateMetaProperty('og:description', description);
    }

    // Обновляем og:image
    if (image) {
      const fullImageUrl = image.startsWith('http') ? image : `${window.location.origin}${image}`;
      updateMetaProperty('og:image', fullImageUrl);
    }

    // Обновляем og:url
    if (url) {
      const fullUrl = url.startsWith('http') ? url : `${window.location.origin}${url}`;
      updateMetaProperty('og:url', fullUrl);
    }
  };

  /**
   * Вспомогательная функция для обновления мета-свойств
   */
  const updateMetaProperty = (property, content) => {
    let metaTag = document.querySelector(`meta[property="${property}"]`);
    if (metaTag) {
      metaTag.setAttribute('content', content);
    } else {
      metaTag = document.createElement('meta');
      metaTag.setAttribute('property', property);
      metaTag.setAttribute('content', content);
      document.head.appendChild(metaTag);
    }
  };

  // Автоматическое обновление при изменении переменных
  watch(
    () => variables,
    (newVariables) => {
      updateSeo(newVariables);
    },
    { deep: true }
  );

  // Автоматическое обновление при изменении роута
  watch(
    () => route.params,
    (newParams) => {
      if (options.autoUpdateOnRouteChange !== false) {
        updateSeo(newParams);
      }
    },
    { deep: true }
  );

  // Инициализация при монтировании
  onMounted(() => {
    updateSeo();
  });

  return {
    seoData,
    isLoading,
    updateSeo,
    getSeo,
    updateTitle,
    updateDescription,
    updateOpenGraph
  };
}

/**
 * Специальный composable для страниц кейсов
 */
export function useCaseSeo(caseData = {}) {
  const route = useRoute();
  
  const variables = ref({
    caseName: caseData.name || '',
    caseUrl: route.params.url || '',
    caseImage: caseData.image || '/images/case-default.png',
    casePrice: caseData.price || '',
    caseItems: caseData.items_count || 0
  });

  const { seoData, updateSeo, updateTitle, updateDescription, updateOpenGraph } = useSeo('case', variables.value);

  /**
   * Обновляет данные кейса
   */
  const updateCaseData = (newCaseData) => {
    variables.value = {
      ...variables.value,
      caseName: newCaseData.name || variables.value.caseName,
      caseUrl: route.params.url || variables.value.caseUrl,
      caseImage: newCaseData.image || variables.value.caseImage,
      casePrice: newCaseData.price || variables.value.casePrice,
      caseItems: newCaseData.items_count || variables.value.caseItems
    };
    updateSeo(variables.value);
  };

  return {
    seoData,
    variables,
    updateCaseData,
    updateTitle,
    updateDescription,
    updateOpenGraph
  };
}

/**
 * Специальный composable для динамических страниц
 */
export function useDynamicSeo(pageName, dynamicVariables = {}) {
  const route = useRoute();
  
  const variables = ref({
    ...dynamicVariables,
    // Автоматически добавляем параметры роута
    ...route.params,
    // Добавляем query параметры если нужно
    ...(route.query.id ? { id: route.query.id } : {})
  });

  const { seoData, updateSeo, updateTitle, updateDescription, updateOpenGraph } = useSeo(pageName, variables.value);

  /**
   * Обновляет динамические переменные
   */
  const updateVariables = (newVariables) => {
    variables.value = {
      ...variables.value,
      ...newVariables
    };
    updateSeo(variables.value);
  };

  return {
    seoData,
    variables,
    updateVariables,
    updateTitle,
    updateDescription,
    updateOpenGraph
  };
}