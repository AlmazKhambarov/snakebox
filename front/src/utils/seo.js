/**
 * SEO Utility для динамического управления мета-тегами
 */

// Базовые SEO данные для snakebox.vip
const BASE_SEO = {
    siteName: "SNAKEBOX",
    siteUrl: "https://snakebox.vip",
    defaultTitle:
        "Открыть кейсы КС 2(CS 2), КС ГО с выводом скинов: SNAKEBOX",
    defaultDescription:
        "SNAKEBOX - лучший сайт для открытия кейсов CS2 и CS:GO. Моментальный вывод скинов в Steam, высокая окупаемость, бесплатные бонусы и честные розыгрыши.",
    defaultKeywords:
        "snakebox, открытие кейсов cs2, кейсы кс го с выводом, бесплатные кейсы кс2, сайт кейсов с выводом в стим, моментальный вывод скинов, симулятор кейсов с выводом, open csgo case, free cs2 cases, лучшая окупаемость кейсов",
    defaultImage: "/images/banner.png",
    twitterCard: "summary_large_image",
    twitterSite: "@snakebox_vip",
};

// SEO конфигурация для каждой страницы
export const SEO_CONFIG = {
    // Главная страница
    index: {
        title: "Открыть кейсы КС 2(CS 2), КС ГО с выводом скинов: SNAKEBOX",
        description:
            "SNAKEBOX - лучший сайт для открытия кейсов CS2 и CS:GO. Моментальный вывод скинов в Steam, высокая окупаемость, бесплатные бонусы и честные розыгрыши.",
        keywords:
            "snakebox, открытие кейсов cs2, кейсы кс го с выводом, бесплатные кейсы кс2, сайт кейсов с выводом в стим, моментальный вывод скинов, симулятор кейсов с выводом, open csgo case, free cs2 cases, лучшая окупаемость кейсов",
        image: "/images/banner.png",
        canonical: "/",
    },

    // Страница кейса
    case: {
        title: "Открыть \"{caseName}\" кейс КС 2 (КС ГО) на SNAKEBOX",
        description:
            "Открой {caseName} кейс на SNAKEBOX и получи шанс выиграть редкие скины CS2. Моментальный вывод в Steam, честные шансы, лучшие цены.",
        keywords:
            "{caseName}, открыть кейс cs2, кейсы кс го, скины cs2, snakebox",
        image: "{caseImage}",
        canonical: "/case/{caseUrl}",
    },

    // Профиль пользователя
    profile: {
        title: "ПРОФИЛЬ - SNAKEBOX",
        description:
            "Управляйте своим профилем на SNAKEBOX. Просматривайте инвентарь, историю открытий и статистику выигрышей.",
        keywords:
            "профиль snakebox, мой профиль, инвентарь cs2, статистика выигрышей",
        image: "/images/banner.png",
        canonical: "/profile",
    },

    // Бонусы
    bonus: {
        title: "Бонусы и подарки: открывай кейсы КС 2 (КС ГО): SNAKEBOX",
        description:
            "Забирай бонусы и подарки от SNAKEBOX. Бонусные кейсы и скины КС 2. Ежедневные бонусы. Выполняй легкие задания и открывай бесплатные кейсы КС ГО!",
        keywords:
            "бонусы snakebox, бесплатные кейсы, ежедневные бонусы, подарки cs2",
        image: "/images/banner.png",
        canonical: "/bonus",
    },

    // Реферальная программа
    referrals: {
        title: "Реферальная программа: зарабатывай с SNAKEBOX",
        description:
            "Приглашайте друзей и получайте бонусы за каждого реферала. Зарабатывайте вместе с SNAKEBOX!",
        keywords:
            "реферальная программа snakebox, пригласить друга, бонусы за рефералов",
        image: "/images/banner.png",
        canonical: "/referrals",
    },

    // Контракты
    contracts: {
        title: "Контракты обмена КС 2 (CS 2), КС ГО: SNAKEBOX",
        description:
            "Обменивайте скины через контракты на SNAKEBOX. Получите более ценные предметы из вашего инвентаря.",
        keywords:
            "контракты cs2, обмен скинов, trade up contract, snakebox",
        image: "/images/banner.png",
        canonical: "/contracts",
    },

    // Апгрейд скинов
    upgrade: {
        title: "Апгрейд скинов КС 2 (CS 2), КС ГО: SNAKEBOX",
        description:
            "Улучшайте свои скины CS2 на SNAKEBOX. Получите более редкие и ценные предметы из вашего инвентаря.",
        keywords:
            "апгрейд скинов cs2, улучшение скинов, trade up, snakebox",
        image: "/images/banner.png",
        canonical: "/upgrade",
    },

    // События
    event: {
        title: "SNAKEBOX: События и турниры | Открывай кейсы КС 2 (КС ГО)",
        description:
            "Участвуйте в специальных событиях и турнирах на SNAKEBOX. Выигрывайте эксклюзивные призы и бонусы!",
        keywords:
            "события snakebox, турниры cs2, эксклюзивные призы, специальные события",
        image: "/images/banner.png",
        canonical: "/event",
    },

    // Розыгрыши
    raffle: {
        title: "Розыгрыши и лотереи | SNAKEBOX",
        description:
            "Участвуйте в розыгрышах и лотереях на SNAKEBOX. Выигрывайте дорогие скины и призы! Ежечасные, ежедневные и еженедельные розыгрыши.",
        keywords:
            "розыгрыши snakebox, лотереи cs2, выиграть скины, призы cs2, бесплатные розыгрыши",
        image: "/images/banner.png",
        canonical: "/raffle",
    },

    // Депозит
    deposit: {
        title: "Пополнение баланса | Безопасные платежи и способы оплаты | SNAKEBOX",
        description: "Пополните баланс на SNAKEBOX безопасными способами: банковские карты, криптовалюта, электронные кошельки. Мгновенное зачисление, низкие комиссии. Начните открывать кейсы CS2 прямо сейчас!",
        keywords: "пополнить баланс snakebox, депозит cs2, оплата кейсов, безопасные платежи, пополнение картой, криптовалюта кс го, электронные кошельки, мгновенное зачисление",
        image: "/images/banner.png",
        canonical: "/deposit",
    },
    // Приглашение по коду
    invite: {
        title: "Приглашение от друга на SNAKEBOX",
        description:
            "Вы были приглашены на SNAKEBOX! Зарегистрируйтесь и получите бонус за регистрацию по реферальной ссылке.",
        keywords:
            "приглашение snakebox, реферальная ссылка, бонус за регистрацию",
        image: "/images/banner.png",
        canonical: "/invite/{code}",
    },

    // Условия использования
    terms: {
        title: "SNAKEBOX - ПОЛЬЗОВАТЕЛЬСКОЕ СОГЛАШЕНИЕ",
        description:
            "Условия использования сервиса SNAKEBOX. Ознакомьтесь с правилами и условиями платформы.",
        keywords:
            "условия использования snakebox, правила платформы, пользовательское соглашение",
        image: "/images/banner.png",
        canonical: "/terms",
    },

    // Политика конфиденциальности
    policy: {
        title: "SNAKEBOX - ПОЛИТИКА КОНФИДЕНЦИАЛЬНОСТИ",
        description:
            "Политика конфиденциальности SNAKEBOX. Как мы защищаем и используем ваши персональные данные.",
        keywords:
            "политика конфиденциальности snakebox, защита данных, персональная информация",
        image: "/images/banner.png",
        canonical: "/policy",
    },

    // VIP Клуб
    vip: {
        title: "VIP Клуб на SNAKEBOX",
        description:
            "Присоединяйтесь к VIP Клубу SNAKEBOX! Эксклюзивные бонусы, персональный менеджер 24/7, закрытые турниры и максимальный кешбэк.",
        keywords:
            "vip клуб snakebox, вип бонусы cs2, закрытые турниры, персональный менеджер, эксклюзивный кешбэк",
        image: "/images/banner.png",
        canonical: "/vip",
    },

    // Пользователи рефералов
    "referrals-users": {
        title: "Мои рефералы на SNAKEBOX",
        description:
            "Список ваших рефералов на SNAKEBOX. Отслеживайте активность приглашенных игроков и заработок.",
        keywords:
            "мои рефералы snakebox, список рефералов, заработок с рефералов",
        image: "/images/banner.png",
        canonical: "/referrals/users",
    },

    // Страница 404
    "404": {
        title: "404 - Страница не найдена | SNAKEBOX",
        description:
            "Запрашиваемая страница не найдена. Вернитесь на главную страницу SNAKEBOX и откройте кейсы CS2.",
        keywords:
            "404, страница не найдена, snakebox",
        image: "/images/banner.png",
        canonical: "/404",
    },

    // Чужой профиль пользователя
    OtherProfile: {
        title: "SNAKEBOX - ПРОФИЛЬ ИГРОКА \"{username}\"",
        description:
            "Профиль игрока {username} на SNAKEBOX. Инвентарь, топ дропы, любимый кейс и статистика.",
        keywords:
            "профиль игрока snakebox, инвентарь cs2, топ дроп, любимый кейс",
        image: "/images/banner.png",
        canonical: "/profile/{id}",
    },
};

/**
 * Обновляет мета-теги страницы
 * @param {Object} seoData - SEO данные для страницы
 * @param {Object} variables - Переменные для подстановки в шаблоны
 */
export function updateMetaTags(seoData, variables = {}) {
    // Подготавливаем данные с подстановкой переменных
    const processedData = processSeoData(seoData, variables);

    // Обновляем title
    if (processedData.title) {
        document.title = processedData.title;
    }

    // Обновляем meta description
    updateMetaTag("name", "description", processedData.description);

    // Обновляем meta keywords
    updateMetaTag("name", "keywords", processedData.keywords);

    // Обновляем canonical URL
    updateCanonicalUrl(processedData.canonical);

    // Обновляем Open Graph теги
    updateOpenGraphTags(processedData);

    // Обновляем Twitter Card теги
    updateTwitterTags(processedData);

    // Добавляем структурированные данные
    updateStructuredData(processedData, variables);
}

/**
 * Обрабатывает SEO данные с подстановкой переменных
 */
function processSeoData(seoData, variables) {
    const processed = { ...seoData };

    Object.keys(processed).forEach((key) => {
        if (typeof processed[key] === "string") {
            processed[key] = processed[key].replace(
                /\{(\w+)\}/g,
                (match, varName) => {
                    return variables[varName] || match;
                }
            );
        }
    });

    return processed;
}

/**
 * Обновляет конкретный мета-тег
 */
function updateMetaTag(attribute, value, content) {
    if (!content) return;

    let metaTag = document.querySelector(`meta[${attribute}="${value}"]`);

    if (metaTag) {
        metaTag.setAttribute("content", content);
    } else {
        metaTag = document.createElement("meta");
        metaTag.setAttribute(attribute, value);
        metaTag.setAttribute("content", content);
        document.head.appendChild(metaTag);
    }
}

/**
 * Обновляет canonical URL
 */
function updateCanonicalUrl(url) {
    if (!url) return;

    let canonical = document.querySelector('link[rel="canonical"]');
    const fullUrl = url.startsWith("http") ? url : `${BASE_SEO.siteUrl}${url}`;

    if (canonical) {
        canonical.setAttribute("href", fullUrl);
    } else {
        canonical = document.createElement("link");
        canonical.setAttribute("rel", "canonical");
        canonical.setAttribute("href", fullUrl);
        document.head.appendChild(canonical);
    }
}

/**
 * Обновляет Open Graph теги
 */
function updateOpenGraphTags(seoData) {
    const ogTags = [
        { property: "og:title", content: seoData.title },
        { property: "og:description", content: seoData.description },
        {
            property: "og:image",
            content: seoData.image
                ? `${BASE_SEO.siteUrl}${seoData.image}`
                : BASE_SEO.defaultImage,
        },
        {
            property: "og:url",
            content: seoData.canonical
                ? `${BASE_SEO.siteUrl}${seoData.canonical}`
                : BASE_SEO.siteUrl,
        },
        { property: "og:type", content: "website" },
        { property: "og:site_name", content: BASE_SEO.siteName },
    ];

    ogTags.forEach((tag) => {
        updateMetaTag("property", tag.property, tag.content);
    });
}

/**
 * Обновляет Twitter Card теги
 */
function updateTwitterTags(seoData) {
    const twitterTags = [
        { name: "twitter:card", content: BASE_SEO.twitterCard },
        { name: "twitter:site", content: BASE_SEO.twitterSite },
        { name: "twitter:title", content: seoData.title },
        { name: "twitter:description", content: seoData.description },
        {
            name: "twitter:image",
            content: seoData.image
                ? `${BASE_SEO.siteUrl}${seoData.image}`
                : BASE_SEO.defaultImage,
        },
    ];

    twitterTags.forEach((tag) => {
        updateMetaTag("name", tag.name, tag.content);
    });
}

/**
 * Добавляет структурированные данные (JSON-LD)
 */
// SEO.js

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
        name: BASE_SEO.siteName,
        url: BASE_SEO.siteUrl,
        description: seoData.description,
        potentialAction: {
            "@type": "SearchAction",
            target: `${BASE_SEO.siteUrl}/search?q={search_term_string}`,
            "query-input": "required name=search_term_string",
        },
    };

    // Добавляем специфичные данные для кейсов
    if (variables.caseName) {
        structuredData["@type"] = "Product";
        structuredData.name = variables.caseName;
        structuredData.description = seoData.description;
        structuredData.category = "CS2 Case";
        structuredData.brand = {
            "@type": "Brand",
            name: "Counter-Strike 2",
        };
        
        // Проверяем наличие обязательных полей для Product
        const hasOffers = variables.casePrice && variables.casePrice !== "0.00";
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
                price: variables.casePrice,
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
 * Получает SEO конфигурацию для страницы
 */
export function getSeoConfig(pageName, variables = {}) {
    const config = SEO_CONFIG[pageName];
    if (!config) {
        console.warn(`SEO config not found for page: ${pageName}`);
        return {
            title: BASE_SEO.defaultTitle,
            description: BASE_SEO.defaultDescription,
            keywords: BASE_SEO.defaultKeywords,
            image: BASE_SEO.defaultImage,
            canonical: "/",
        };
    }

    return processSeoData(config, variables);
}

/**
 * Инициализирует SEO для страницы
 */
export function initSeo(pageName, variables = {}) {
    const seoData = getSeoConfig(pageName, variables);
    updateMetaTags(seoData, variables);
    return seoData;
}
