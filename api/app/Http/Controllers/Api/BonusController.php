<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BonusClaim;
use App\Models\Payment;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;

use App\Services\RedisService;

class BonusController extends Controller
{

  protected RedisService $redisService;
  public function __construct(
    RedisService $redisService
  ) {
    $this->redisService = $redisService;
  }

  public function checkUsername(Request $request)
  {
    $user = $request->user();

    $todayBonus = BonusClaim::where('user_id', $user->id)
      ->where('bonus_type', 'username')
      ->whereDate('created_at', Carbon::today())
      ->first();

    if ($todayBonus) {
      return [
        'success' => false,
        'message' => 'Вы уже получали этот бонус сегодня. Попробуйте завтра.'
      ];
    }

    $weekAgo = Carbon::now()->subDays(7);

    $deposit = Payment::query()
      ->where('user_id', $user->id)
      ->where('status', Payment::PAID)
      ->where('created_at', '>=', $weekAgo)
      ->sum('amount') >= 150;

    if (!$deposit) {
      return [
        'success' => false,
        'message' => 'Необходимо пополнить баланс минимум на 150 рублей за неделю.'
      ];
    }


      $apiKey = env('STEAM_CLIENT_SECRET');
      $response = Http::get("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/", [
        'key' => $apiKey,
        'steamids' => $user->steam_id,
      ]);

      $playerData = $response->json()['response']['players'][0] ?? null;

      if (!$playerData) {
        return [
          'success' => false,
          'message' => 'Не удалось получить данные из Steam.'
        ];
      }

      $settings = Settings::query()->first();
      $domain = $settings->domain;

      if (stripos($playerData['personaname'], $domain) === false) {
          return response()->json([
              'success' => false,
              'message' => 'Ваш ник в Steam не содержит домен ' . $domain
          ]);
      }

      $bonusAmount = 3500;

      BonusClaim::create([
        'user_id' => $user->id,
        'bonus_type' => 'username',
        'amount' => $bonusAmount
      ]);

      $user->update([
        'balance' => $user->balance + $bonusAmount
      ]);

      $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);

      return [
        'success' => true,
        'message' => 'Бонус начислен! +' . $bonusAmount / 100 . ' рублей',
        'bonus_amount' => $bonusAmount
      ];
    }

    public function checkAvatar(Request $request)
    {
        $user = $request->user();
    
        // уже получал сегодня
        $todayBonus = BonusClaim::where('user_id', $user->id)
            ->where('bonus_type', 'avatar')
            ->whereDate('created_at', Carbon::today())
            ->first();
    
        if ($todayBonus) {
            return [
                'success' => false,
                'message' => 'Вы уже получали этот бонус сегодня. Попробуйте завтра.'
            ];
        }
    
        // проверка депозита за неделю
        $weekAgo = Carbon::now()->subDays(7);
        $hasDeposit = Payment::query()
            ->where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->where('created_at', '>=', $weekAgo)
            ->sum('amount') >= 150;
    
        if (!$hasDeposit) {
            return [
                'success' => false,
                'message' => 'Необходимо пополнить баланс минимум на 150 рублей за неделю.'
            ];
        }
    
        $bonusAmount = 3500;
        $user->increment('balance', $bonusAmount);
    
        $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);
    
        BonusClaim::create([
            'user_id' => $user->id,
            'bonus_type' => 'avatar',
            'amount' => $bonusAmount,
        ]);
    
        return [
            'success' => true,
            'message' => "Бонус начислен! +" . $bonusAmount / 100 . " рублей",
            'bonus_amount' => $bonusAmount
        ];
    }

}
