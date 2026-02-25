<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\ReferralEarning;
use App\Models\ReferralBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LiveService;
use App\Services\RedisService;

class ReferralController extends Controller
{

  protected LiveService $liveService;
  protected RedisService $redisService;
  public function __construct(
    LiveService $liveService,
    RedisService $redisService
  ) {
    $this->liveService = $liveService;
    $this->redisService = $redisService;
  }



  private $levelPercentages = [
    1 => 0.5,
    2 => 1.0,
    3 => 1.5,
    4 => 2.0,
    5 => 2.5
  ];

  private $levelRequirements = [
    1 => 0,
    2 => 5000000,
    3 => 10000000,
    4 => 50000000,
    5 => 100000000
  ];

  /**
   * Получить сводку реферальной системы
   */
  public function getSummary(Request $request)
  {
    $user = $request->user();

    // Обновляем уровень пользователя (если нет индивидуального процента)
    $this->updateUserLevel($user);

    // Определяем процент: индивидуальный или по уровню
    $percentage = $user->custom_referral_percentage !== null 
      ? $user->custom_referral_percentage 
      : $this->levelPercentages[$user->referral_level];

    return response()->json([
      'success' => true,
      'summary' => [
        'referral_code' => $user->referral_code,
        'referral_link' => config('app.frontend_url') . '/invite/' . $user->referral_code,
        'current_level' => $user->referral_level,
        'level_percentage' => $percentage,
        'has_custom_percentage' => $user->custom_referral_percentage !== null,
        'custom_percentage' => $user->custom_referral_percentage,
        'next_level_requirement' => $user->custom_referral_percentage !== null ? null : $this->getNextLevelRequirement($user),
        'total_earned' => $user->total_earned,
        'referral_balance' => $user->referral_balance,
        'referrals_count' => $user->referrals_count,
        'total_deposited' => $user->total_deposited,
        'bonus_per_referral' => 25
      ]
    ]);
  }

  /**
   * Получить список рефералов
   */
  public function getReferrals(Request $request)
  {
    $user = $request->user();
    $search = $request->get('search');

    $referrals = User::where('referrer_id', $user->id)
      ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%");
      })
      ->select(['id', 'username', 'created_at', 'total_deposited', 'avatar'])
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    $referrals->getCollection()->transform(function ($referral) use ($user) {
      $earned = DB::table('referral_earnings')
        ->where('user_id', $user->id)
        ->where('referral_id', $referral->id)
        ->sum('amount');

      $referral->earned_from_user = $earned;
      return $referral;
    });

    return response()->json([
      'success' => true,
      'referrals' => $referrals,
    ]);
  }


  public function transferToBalance(Request $request)
  {
    $user = $request->user();

    $refBalance = $user->referral_balance;

    if ($refBalance === 0) {
      return [
        'success' => false,
        'message' => 'Нечего переводить'
      ];
    }

    $user->update([
      'referral_balance' => 0,
      'balance' => $user->balance +  $refBalance
    ]);

    $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);

    return [
      'success' => true,
      'message' => 'Средства успешно переведены на баланс!'
    ];
  }

  private function updateUserLevel(User $user)
  {
    // Если у пользователя установлен индивидуальный процент, не обновляем уровень автоматически
    if ($user->custom_referral_percentage !== null) {
      return;
    }

    $currentLevel = $user->referral_level;
    $totalDeposited = $user->total_deposited;

    foreach ($this->levelRequirements as $level => $requirement) {
      if ($totalDeposited >= $requirement) {
        $user->referral_level = $level;
      }
    }

    if ($user->isDirty('referral_level')) {
      $user->save();
    }
  }

  private function getNextLevelRequirement(User $user)
  {
    $currentLevel = $user->referral_level;

    if ($currentLevel >= 5) {
      return null;
    }

    return $this->levelRequirements[$currentLevel + 1];
  }
}
