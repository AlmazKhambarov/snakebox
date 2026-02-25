<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use App\Models\Boxes;

Route::get('/', function () {
    return view('welcome');
});

// SEO: Генерация sitemap.xml
Route::get('/sitemap.xml', function () {
    $appUrl = rtrim(config('app.url', URL::to('/')), '/');

    $cases = Boxes::query()->select(['url', 'updated_at'])->get();

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
        '/policy' => ['priority' => '0.3', 'changefreq' => 'yearly'],
    ];

    $xml = [];
    $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($staticPages as $path => $config) {
        $xml[] = '<url>';
        $xml[] = '<loc>' . htmlspecialchars($appUrl . $path, ENT_XML1) . '</loc>';
        $xml[] = '<lastmod>' . date('Y-m-d') . '</lastmod>';
        $xml[] = '<changefreq>' . $config['changefreq'] . '</changefreq>';
        $xml[] = '<priority>' . $config['priority'] . '</priority>';
        $xml[] = '</url>';
    }

    foreach ($cases as $case) {
        $xml[] = '<url>';
        $xml[] = '<loc>' . htmlspecialchars($appUrl . '/case/' . $case->url, ENT_XML1) . '</loc>';
        $xml[] = '<lastmod>' . ($case->updated_at ? $case->updated_at->format('Y-m-d') : date('Y-m-d')) . '</lastmod>';
        $xml[] = '<changefreq>weekly</changefreq>';
        $xml[] = '<priority>0.9</priority>';
        $xml[] = '</url>';
    }

    $xml[] = '</urlset>';

    return response(implode("", $xml), 200)->header('Content-Type', 'application/xml');
});
