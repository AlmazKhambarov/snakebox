/**
 * SEO оптимизация для поисковых систем
 *
 * Этот файл содержит решения для улучшения индексации
 * Яндексом и Google
 */

// ========================================
// 1. SITEMAP.XML - Карта сайта
// ========================================

// Создайте файл public/sitemap.xml
const sitemapContent = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://snakebox.vip/</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/bonus</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/referrals</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/profile</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/contracts</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/upgrade</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/event</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/raffle</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/deposit</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/terms</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>yearly</changefreq>
    <priority>0.3</priority>
  </url>
  <url>
    <loc>https://snakebox.vip/policy</loc>
    <lastmod>2024-01-01</lastmod>
    <changefreq>yearly</changefreq>
    <priority>0.3</priority>
  </url>
</urlset>`;

// ========================================
// 2. ROBOTS.TXT - Инструкции для ботов
// ========================================

const robotsContent = `User-agent: *
Allow: /

# Sitemap
Sitemap: https://snakebox.vip/sitemap.xml

# Disallow admin areas
Disallow: /admin/
Disallow: /api/
Disallow: /auth/callback

# Allow important pages
Allow: /case/
Allow: /bonus
Allow: /referrals
Allow: /profile
Allow: /contracts
Allow: /upgrade
Allow: /event
Allow: /raffle
Allow: /deposit
Allow: /terms
Allow: /policy`;

// ========================================
// 3. МЕТА-ТЕГИ ДЛЯ ПОИСКОВЫХ СИСТЕМ
// ========================================

// Добавьте в index.html дополнительные мета-теги
const additionalMetaTags = `
<!-- Дополнительные мета-теги для поисковых систем -->
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow">
<meta name="yandex" content="index, follow">
<meta name="bingbot" content="index, follow">


<!-- Язык -->
<meta name="language" content="ru">
<meta name="revisit-after" content="1 days">

<!-- Мобильная оптимизация -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Дополнительные Open Graph теги -->
<meta property="og:locale" content="ru_RU">
<meta property="og:site_name" content="SNAKEBOX">
<meta property="og:updated_time" content="2024-01-01T00:00:00+03:00">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@snakebox">
<meta name="twitter:creator" content="@snakebox">

<!-- Дополнительные структурированные данные -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "SNAKEBOX",
  "url": "https://snakebox.vip",
  "description": "SNAKEBOX - лучший сайт для открытия кейсов CS2 и CS:GO",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://snakebox.vip/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  },
  "sameAs": [
    "https://vk.com/snakebox_vip",
    "https://t.me/snakebox_vip",
    "https://discord.gg/snakebox_vip"
  ]
}
</script>
`;

// ========================================
// 4. ПРЕРЕНДЕРИНГ ДЛЯ ПОИСКОВЫХ БОТОВ
// ========================================

// Добавьте в index.html перед закрывающим тегом </head>
const prerenderScript = `
<script>
// Определяем поискового бота
function isSearchBot() {
  const userAgent = navigator.userAgent.toLowerCase();
  const bots = [
    'googlebot', 'bingbot', 'yandexbot', 'yandex', 'baiduspider',
    'facebookexternalhit', 'twitterbot', 'linkedinbot', 'whatsapp',
    'telegrambot', 'slackbot', 'discordbot'
  ];
  return bots.some(bot => userAgent.includes(bot));
}

// Если это поисковый бот, показываем статический контент
if (isSearchBot()) {
  document.addEventListener('DOMContentLoaded', function() {
    // Добавляем статический контент для ботов
    const staticContent = \`
      <div id="static-content">
        <h1>SNAKEBOX - Открытие кейсов CS2 с выводом скинов в Steam</h1>
        <p>SNAKEBOX - лучший сайт для открытия кейсов CS2 и CS:GO. Моментальный вывод скинов в Steam, высокая окупаемость, бесплатные бонусы и честные розыгрыши.</p>
        <h2>Основные возможности:</h2>
        <ul>
          <li>Открытие кейсов CS2 и CS:GO</li>
          <li>Моментальный вывод скинов в Steam</li>
          <li>Высокая окупаемость</li>
          <li>Бесплатные бонусы</li>
          <li>Ежедневные розыгрыши</li>
          <li>Реферальная программа</li>
          <li>Контракты и апгрейд скинов</li>
        </ul>
        <h2>Популярные кейсы:</h2>
        <ul>
          <li>Operation Bravo Case</li>
          <li>Chroma Case</li>
          <li>Gamma Case</li>
          <li>Spectrum Case</li>
          <li>Glove Case</li>
        </ul>
      </div>
    \`;
    
    document.body.innerHTML = staticContent;
  });
}
</script>
`;

// ========================================
// 5. ДИНАМИЧЕСКИЙ SITEMAP
// ========================================

// Создайте API endpoint для динамического sitemap
const dynamicSitemapAPI = `
// В Laravel (routes/api.php)
Route::get('/sitemap.xml', function() {
    $cases = \\App\\Models\\Boxes::all();
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Статические страницы
    $staticPages = [
        '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
        '/bonus' => ['priority' => '0.8', 'changefreq' => 'weekly'],
        '/referrals' => ['priority' => '0.7', 'changefreq' => 'weekly'],
        '/profile' => ['priority' => '0.6', 'changefreq' => 'monthly'],
        '/contracts' => ['priority' => '0.7', 'changefreq' => 'weekly'],
        '/upgrade' => ['priority' => '0.7', 'changefreq' => 'weekly'],
        '/event' => ['priority' => '0.8', 'changefreq' => 'daily'],
        '/raffle' => ['priority' => '0.8', 'changefreq' => 'daily'],
        '/deposit' => ['priority' => '0.6', 'changefreq' => 'monthly'],
        '/terms' => ['priority' => '0.3', 'changefreq' => 'yearly'],
        '/policy' => ['priority' => '0.3', 'changefreq' => 'yearly']
    ];
    
    foreach ($staticPages as $url => $config) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>https://snakebox.vip' . $url . '</loc>';
        $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
        $sitemap .= '<changefreq>' . $config['changefreq'] . '</changefreq>';
        $sitemap .= '<priority>' . $config['priority'] . '</priority>';
        $sitemap .= '</url>';
    }
    
    // Динамические страницы кейсов
    foreach ($cases as $case) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>https://snakebox.vip/case/' . $case->url . '</loc>';
        $sitemap .= '<lastmod>' . $case->updated_at->format('Y-m-d') . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.9</priority>';
        $sitemap .= '</url>';
    }
    
    $sitemap .= '</urlset>';
    
    return response($sitemap, 200)
        ->header('Content-Type', 'application/xml');
});
`;

// ========================================
// 6. GOOGLE SEARCH CONSOLE И YANDEX WEBMASTER
// ========================================

const searchConsoleSetup = `
<!-- Google Search Console -->
<meta name="google-site-verification" content="YOUR_GOOGLE_VERIFICATION_CODE">

<!-- Yandex Webmaster -->
<meta name="yandex-verification" content="YOUR_YANDEX_VERIFICATION_CODE">

<!-- Bing Webmaster -->
<meta name="msvalidate.01" content="YOUR_BING_VERIFICATION_CODE">
`;

// ========================================
// 7. АНАЛИТИКА И ОТСЛЕЖИВАНИЕ
// ========================================

const analyticsScript = `
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>

<!-- Yandex Metrica -->
<script type="text/javascript">
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(YANDEX_METRICA_ID, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/YANDEX_METRICA_ID" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
`;

// ========================================
// 8. ОПТИМИЗАЦИЯ СКОРОСТИ ЗАГРУЗКИ
// ========================================

const performanceOptimization = `
<!-- Preload критических ресурсов -->
<link rel="preload" href="/css/styles.min.css" as="style">
<link rel="preload" href="/fonts/Roboto/stylesheet.css" as="style">
<link rel="preload" href="/js/app.js" as="script">

<!-- Preconnect к внешним доменам -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- DNS prefetch -->
<link rel="dns-prefetch" href="//api.snakebox.vip">
<link rel="dns-prefetch" href="//cdn.snakebox.vip">

<!-- Resource hints -->
<link rel="prefetch" href="/bonus">
<link rel="prefetch" href="/referrals">
<link rel="prefetch" href="/profile">
`;

// ========================================
// 9. СТРУКТУРИРОВАННЫЕ ДАННЫЕ ДЛЯ КЕЙСОВ
// ========================================

const caseStructuredData = `
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Operation Bravo Case",
  "description": "Открой Operation Bravo Case на SNAKEBOX и получи шанс выиграть редкие скины CS2",
  "image": "https://snakebox.vip/images/cases/operation-bravo.jpg",
  "brand": {
    "@type": "Brand",
    "name": "Counter-Strike 2"
  },
  "category": "CS2 Case",
  "offers": {
    "@type": "Offer",
    "price": "2.50",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "SNAKEBOX"
    }
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "reviewCount": "1250"
  }
}
</script>
`;

// ========================================
// 10. МОНИТОРИНГ ИНДЕКСАЦИИ
// ========================================

const indexingMonitoring = `
// Добавьте в utils/seo.js функцию для отслеживания индексации
export function trackIndexing(pageName, url) {
  // Отправляем данные в аналитику
  if (typeof gtag !== 'undefined') {
    gtag('event', 'page_view', {
      page_title: document.title,
      page_location: url,
      page_name: pageName
    });
  }
  
  // Отправляем данные в Yandex Metrica
  if (typeof ym !== 'undefined') {
    ym(YANDEX_METRICA_ID, 'hit', url, {
      title: document.title
    });
  }
  
  // Логируем для отладки
  console.log('SEO tracking:', {
    page: pageName,
    url: url,
    title: document.title,
    description: document.querySelector('meta[name="description"]')?.content
  });
}
`;

export {
    sitemapContent,
    robotsContent,
    additionalMetaTags,
    prerenderScript,
    dynamicSitemapAPI,
    searchConsoleSetup,
    analyticsScript,
    performanceOptimization,
    caseStructuredData,
    indexingMonitoring,
};
