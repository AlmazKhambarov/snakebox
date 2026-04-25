<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\SteamController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\CasesController;
use App\Http\Controllers\Api\LiveController;
use App\Http\Controllers\Api\UpgradeController;
use App\Http\Controllers\Api\ContractsController;
use App\Http\Controllers\Api\PromocodeController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProvablyFairController;
use App\Http\Controllers\Api\MarketOrSteam\MarketController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Payment\NirvanaController;
use App\Http\Controllers\Api\Payment\CryptoCloudController;
use App\Http\Controllers\Api\Payment\TBankController;
use App\Http\Controllers\Api\Payment\NirvanaUzsController;
use App\Http\Controllers\Api\Payment\PaymeController;

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\PromocodesController;
use App\Http\Controllers\Admin\ItemsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\WithdrawController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\ReferralController as AdminReferralController;
use App\Http\Controllers\GiveawayController;

Route::get('/sitemap.xml', function () {
    $cases = \App\Models\Boxes::all();
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
        // '/raffle' => ['priority' => '0.8', 'changefreq' => 'daily'],
        // '/deposit' => ['priority' => '0.6', 'changefreq' => 'monthly'],
        '/terms' => ['priority' => '0.3', 'changefreq' => 'yearly'],
        '/policy' => ['priority' => '0.3', 'changefreq' => 'yearly']
    ];

    foreach ($staticPages as $url => $config) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>https://snakebox.vip' . $url . '</loc>';
        $sitemap .= '<lastmod>' . date(format: 'Y-m-d') . '</lastmod>';
        $sitemap .= '</url>';
    }

    // Динамические страницы кейсов
    foreach ($cases as $case) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>https://snakebox.vip/case/' . $case->url . '</loc>';
        $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
        $sitemap .= '</url>';
    }

    $sitemap .= '</urlset>';

    return response($sitemap, 200)
        ->header('Content-Type', 'application/xml');
});


Route::prefix('/auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login');
        Route::get('/register', 'register');
    });

    Route::controller(SteamController::class)->group(function () {
        Route::get('/steam', 'redirectToSteam')->name('auth.steam');
        Route::get('/steam/handle', 'handleSteamCallback')->name('auth.steam.handle');
        Route::post('/steam/logout', 'logout');
    });

    Route::controller(SocialController::class)->group(function () {
        Route::get('{provider}/redirect', 'redirect');
        Route::get('{provider}/callback', 'callback');
    });
});


Route::controller(LiveController::class)->prefix('/main')->group(function () {
    Route::get('/', 'index');
    Route::get('/stats', 'stats');
});
Route::controller(UserController::class)->prefix('/user')->group(function () {
    Route::get('/', 'index')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::get('/get/admin', 'getAdmin')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::get('/items', 'getItems')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/trade-link', 'tradeLink')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::get('/other', 'getOtherProfile');

});

Route::controller(CasesController::class)->prefix('/case')->group(function () {
    Route::get('/get', 'index');
    Route::get('/one', 'one');
    Route::post('/open', 'open')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/sell/item', 'sellItem')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/sell/allItem', 'sellAllItems')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
});

Route::controller(ContractsController::class)->prefix('/contracts')->group(function () {
    Route::get('/user/items', 'getItems')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/create', 'create')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
});

Route::controller(PromocodeController::class)->prefix('/promocodes')->group(function () {
    Route::get('/daily-bonus', 'getDailyBonus');
    Route::post('/activate', 'activate')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
});

Route::controller(EventController::class)->prefix('/event')->group(function () {
    Route::get('/get', 'index');
});

Route::controller(ReferralController::class)->prefix('/referral')->group(function () {
    Route::get('/summary', 'getSummary')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::get('/referrals', 'getReferrals')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/transfer', 'transferToBalance')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/apply-code', 'applyReferralCode')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
});

Route::controller(BonusController::class)->prefix('/bonus')->group(function () {
    Route::post('/check-username', 'checkUsername')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/check-avatar', 'checkAvatar')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::get('/debug-avatars', 'debugAvatars')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::get('/test-avatars', 'testAvatars');
    Route::get('/current-avatar', 'getCurrentAvatar')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
});

Route::controller(ProvablyFairController::class)->prefix('/provably')->group(function () {
    Route::get('/', 'index')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/update_client_seed', 'updateClientSeed')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/rotate_server_seed', 'rotateServerSeed')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
});

Route::controller(MarketController::class)->prefix('/market')->group(function () {
    Route::post('/parseItems', 'updateItemImagesAndRarity');
    Route::post('/items', 'marketItems');
    Route::post('/withdraw', 'withdraw')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/checkItems', 'checkItems');
});

Route::controller(UpgradeController::class)->prefix('/upgrade')->group(function () {
    Route::get('/items', 'getItems');
    Route::get('/user/items', 'userItems')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
    Route::post('/create', 'create')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
});

Route::controller(MainController::class)->prefix('/settings')->group(function () {
    Route::get('/', 'index');
});

Route::controller(\App\Http\Controllers\Api\BannerController::class)->prefix('/banners')->group(function () {
    Route::get('/', 'index');
});

Route::controller(PaymentController::class)->prefix('/payment')->group(function () {
    Route::get('/methods', 'getMethods');
    Route::post('/check-promo', 'checkPromocode');
    Route::controller(NirvanaController::class)->prefix('/nirvana')->group(function () {
        Route::post('/create', 'create')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
        Route::get('/callback', 'callback');
    });
    Route::controller(CryptoCloudController::class)->prefix('/cryptocloud')->group(function () {
        Route::post('/create', 'create')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
        Route::post('/callback', 'callback');
    });
    Route::controller(TBankController::class)->prefix('/tbank')->group(function () {
        Route::post('/create', 'create')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
        Route::get('/callback', 'callback');
    });
    Route::controller(NirvanaUzsController::class)->prefix('/nirvana-uzs')->group(function () {
        Route::post('/create', 'create')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
        Route::get('/callback', 'callback');
    });
    Route::controller(PaymeController::class)->prefix('/payme')->group(function () {
        Route::post('/create', 'create')->middleware(['auth:sanctum', \App\Http\Middleware\CheckUserBanned::class]);
        Route::post('/callback', 'callback');
    });
});

Route::controller(GiveawayController::class)->prefix('/giveaway')->group(function () {
    Route::get('/', 'index');
    Route::get('/winners', 'winners');
    Route::get('/{id}', 'show');
    Route::post('/{id}/join', 'join')->middleware('auth:sanctum');
});
Route::post('/admin/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'login']);
Route::post('/admin/password/change', [\App\Http\Controllers\Admin\AdminAuthController::class, 'changePassword'])->middleware('auth:sanctum');

Route::prefix('/admin')->middleware('auth:sanctum')->group(function () {
    Route::controller(IndexController::class)->prefix('/index')->group(function () {
        Route::get('/', 'get');
    });

    Route::controller(CategoriesController::class)->prefix('/categories')->group(function () {
        Route::get('/', 'get');
        Route::post('/create', 'create');
        Route::get('/get', 'category');
        Route::post('/delete', 'delete');
        Route::post('/save', 'save');
    });

    Route::controller(\App\Http\Controllers\Admin\CasesController::class)->prefix('/cases')->group(function () {
        Route::get('/', 'get');
        Route::get('/case', 'case');
        Route::post('/create', 'create');
        Route::post('/save', 'save');
        Route::post('/delete', 'delete');

        Route::get('/items', 'items');
        Route::get('/items/item', 'getItem');
        Route::post('/items/save', 'saveItem');
        Route::post('/items/create', 'createItem');
        Route::post('/items/delete', 'deleteItem');
        Route::post('/items/chance', 'calcChance');
        Route::post('/items/generate', 'generateCaseItems');

        Route::get('/items/all', 'itemsAll');
        Route::get('/categories', 'categories');
        Route::get('/items/list', 'itemsAllForCase');

        // RTP управление
        Route::get('/rtp', 'getRTP');
        Route::post('/rtp/update', 'updateRTP');
        Route::post('/rtp/reset', 'resetRTP');
        Route::post('/enable', 'enableCase');
        Route::get('/statistics', 'statistics');
    });

    Route::controller(PromocodesController::class)->prefix('/promocodes')->group(function () {
        Route::get('/', 'get');
        Route::post('/create', 'create');
        Route::post('/delete', 'delete');
    });

    Route::controller(ItemsController::class)->prefix('/items')->group(function () {
        Route::get('/', 'get');
        Route::post('/create', 'createItem');
        Route::post('/update', 'updateItem');
        Route::post('/delete', 'deleteItem');

        Route::controller(MarketController::class)->prefix('/market')->group(function () {
            Route::post('/prices', 'updatePrices');
        });
    });
    Route::controller(UsersController::class)->prefix('/users')->group(function () {
        Route::get('/', 'get');
        Route::get('/user', 'user');
        Route::post('/save', 'save');
        
        // Управление инвентарем
        Route::get('/inventory', 'getInventory');
        Route::post('/inventory/sell', 'sellItem');
        Route::post('/inventory/delete', 'deleteItem');
        Route::post('/inventory/sell-all', 'sellAllItems');
        Route::post('/inventory/delete-all', 'deleteAllItems');
        Route::post('/inventory/change-status', 'changeItemStatus');
        
        // Сессии и активность
        Route::get('/sessions', 'getSessions');
        Route::post('/sessions/revoke', 'revokeSession');
        Route::post('/sessions/revoke-all', 'revokeAllSessions');
        Route::get('/ip-history', 'getIpHistory');
        Route::get('/activity-history', 'getActivityHistory');
        
        // Блокировка пользователя
        Route::post('/ban', 'banUser');
        Route::post('/unban', 'unbanUser');
        
        // Блокировка скинов
        Route::post('/block-skins', 'blockSkins');
        Route::post('/unblock-skins', 'unblockSkins');
    });
    Route::controller(SettingsController::class)->prefix('/settings')->group(function () {
        Route::get('/', 'get');
        Route::post('/save', 'save');
    });
    Route::controller(PaymentsController::class)->prefix('/payments')->group(function () {
        Route::get('/', 'index');
        Route::get('/getMethods', 'getMethods');
        Route::post('/delete', 'delete');
        Route::post('/deleteMethods', 'deleteMethods');
        Route::post('/save', 'save');
        Route::post('/create', 'create');
        Route::get('/methods', 'methods');
    });
    Route::controller(WithdrawController::class)->prefix('/withdraws')->group(function () {
        Route::get('/', 'index');
    });
    Route::controller(\App\Http\Controllers\Admin\GiveawayController::class)->prefix('/giveaways')->group(function () {
        Route::get('/', 'index');
        Route::get('/get', 'get');
        Route::post('/create', 'create');
        Route::post('/update', 'update');
        Route::post('/delete', 'delete');
        Route::post('/select-winner', 'selectWinner');
        Route::get('/participants', 'participants');
        Route::get('/items', 'items');
    });

    Route::controller(AdminReferralController::class)->prefix('/referrals')->group(function () {
        Route::get('/', 'index');
        Route::get('/get', 'get');
        Route::post('/update', 'update');
        Route::get('/statistics', 'statistics');
        Route::post('/generate-code', 'generateReferralCode');
        Route::post('/add-balance', 'addBalance');
    });

    Route::controller(\App\Http\Controllers\Admin\EventController::class)->prefix('/events')->group(function () {
        Route::get('/', 'index');
        Route::get('/get', 'get');
        Route::post('/create', 'create');
        Route::post('/update', 'update');
        Route::post('/delete', 'delete');
        Route::post('/prize/update', 'updatePrize');
        Route::get('/items', 'getItems');
    });

    Route::controller(\App\Http\Controllers\Admin\BannerController::class)->prefix('/banners')->group(function () {
        Route::get('/', 'index');
        Route::get('/get', 'get');
        Route::post('/create', 'create');
        Route::post('/update', 'update');
        Route::post('/delete', 'delete');
    });
});
