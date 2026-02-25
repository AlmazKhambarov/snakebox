<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\PaymentMethods;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Promocode;
use App\Models\PromocodeLog;
use App\Models\Settings;
use App\Models\User;
use App\Services\RedisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\PromocodeUse;
use App\Models\ReferralEarning;

class TBankController extends Controller
{

  protected RedisService $redisService;
  public function __construct(
    RedisService $redisService
  ) {
    $this->redisService = $redisService;
  }

   /**
   * Генерация подписи (токена) для TBank API
   * 
   * @param array $params Параметры запроса
   * @return string SHA-256 хеш подписи
   */

   private function generateToken(array $params): string
   {
     // Получаем пароль из env
     $password = env('TBANK_PASSWORD', 'Rux5kzpWHfQy&7K$'); // Дефолтный демо-пароль
     
     // Шаг 1: Фильтруем массив - берем только параметры корневого объекта
     // Исключаем вложенные массивы и объекты
     $filteredParams = [];
     foreach ($params as $key => $value) {
       if (!is_array($value) && !is_object($value)) {
         // Преобразуем boolean в строку для корректной генерации токена
         if (is_bool($value)) {
           $value = $value ? 'true' : 'false';
         }
         $filteredParams[$key] = $value;
       }
     }
     
     // Шаг 2: Добавляем Password в массив
     $filteredParams['Password'] = $password;
     
     // Шаг 3: Сортируем массив по ключам в алфавитном порядке
     ksort($filteredParams);
     
     // Шаг 4: Конкатенируем только значения в одну строку
     $concatenatedString = implode('', array_values($filteredParams));
     
     // Шаг 5: Применяем SHA-256 хеш-функцию
     $token = hash('sha256', $concatenatedString);
     
     return $token;
   }

  public function create(Request $request)
  {
    $user = $request->user();
    $terminalKey = '1762518328511DEMO';
    $amount = $request->amount;
    $orderID = time() . uniqid();
    $description = 'Пополнение баланса на ' . $request->amount . ' рублей';
    $customerKey = $user->id;
    $notificationURL = config('app.url') . '/api/payment/tbank/callback';
    $method = strval($request->payment_method);
    $system = $request->system;

    if (!$method) return ['success' => false, 'message' => 'Выберите способ оплаты'];

    $promo_input = $request->promocode;

    $limits = $this->getDepositLimits($method, $system);

    if ($amount < $limits['min_amount']) {
      return ['success' => false, 'message' => "Минимальная сумма депозита: " . ($limits['min_amount']) . " ₽."];
    }

    if ($amount > $limits['max_amount']) {
      return ['success' => false, 'message' => "Максимальная сумма депозита: " . ($limits['max_amount']) . " ₽."];
    }

    $log = null;
    $promocode = null;

    if (!is_null($promo_input)) {
      $promocode = Promocode::query()
        ->where('code', $promo_input)
        ->where('type', 'deposit_percent')
        ->where('is_active', true)
        ->where('uses_left', '>', 0)
        ->where('valid_from', '<', Carbon::now())
        ->where('valid_until', '>', Carbon::now())
        ->first();

      if (!$promocode) {
        return ['success' => false, 'message' => 'Промокод не найден. Истек срок действия, или неактивен!'];
      }

      $log = PromocodeUse::query()->where('promocode_id', $promocode->id)->where('user_id', $user->id)->first();
    }

    Log::channel('payment_tbank')->info($promocode);



    $params = [
      'TerminalKey' => $terminalKey,
      'Amount' => $amount * 100,
      'OrderId' => $orderID,
      'Description' => $description,
      'CustomerKey' => $customerKey,
      'NotificationURL' => $notificationURL,
    ];

    $token = $this->generateToken($params);
    
    $params['Token'] = $token;

    $client = new Client();
    $response = $client->post('https://securepay.tinkoff.ru/v2/Init', [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'json' => $params,
    ]);

    $responseData = json_decode($response->getBody()->getContents(), true);

    Log::channel('payment_tbank')->info('TBank response:', $responseData);

    
    $payment = Payment::query()->create([
      'user_id' => $user->id,
      'promocode_id' => $promocode ? $promocode->id : null,
      'system' => 'tbank',
      'method' => $method,
      'amount' => $amount,
      'status' => Payment::PENDING,
      'transaction_id' => $orderID,
      'metadata' => [
        'user_id' => $user->id,
        'username' => $user->username,
        'time' => Carbon::now()->format('Y-m-d H:i:s'),
        'ip' => $request->getClientIp(true),
      ],
    ]);

    return [
      'success' => true,
      'message' => 'Переносим на страницу оплаты...',
      'link' => $responseData['PaymentURL'] ?? null,
    ];
  }

  public function callback(Request $request)
  {
    $externalId = $request->query('externalId');

    $client = new Client();
    $response = $client->get('https://f.nirvanapay.pro/api/v2/order', [
      'headers' => [
        'ApiPublic'  => '5156439e-4e0e-4301-8f69-b43ac1426869',
        'ApiPrivate' => '65c55599-5381-408d-bc4b-e535138729e6',
      ],
      'query' => [
        'externalId' => $externalId,
      ],
    ]);

    $body = json_decode($response->getBody()->getContents(), true);
    Log::channel('payment_tbank')->debug('Nirvana order response:', $body);

    if ($body['data']['status'] !== 'SUCCESS') {
      die('Payment not SUCCESS');
    }

    $payment = Payment::query()->where('transaction_id', $body['data']['externalID'])->first();
    if (!$payment) die('Payment not found');
    if ($payment->status == Payment::PAID) die('payment is out of date');

    $user = User::query()->find($payment->user_id);
    if (!$user) return 'User not found';

    $amount = $payment->amount * 100;

    // === Промокод ===
    if ($payment->promocode_id) {
      $promocode = Promocode::query()->find($payment->promocode_id);
      if ($promocode) {
        $bonus = ($amount * $promocode->value) / 100;
        $amount += $bonus;
        $promocode->decrement('uses_left');

        PromocodeUse::query()->create([
          'user_id' => $payment->user_id,
          'promocode_id' => $payment->promocode_id,
          'bonus_amount' => $bonus
        ]);
      }
    }

    $event_points = $payment->amount * 0.1;
    $user->increment('balance', $amount);
    $user->increment('event_points', $event_points);
    $user->increment('total_deposited', $payment->amount * 100);

    $payment->status = Payment::PAID;
    $payment->save();

    $hasOtherDeposits = Payment::query()
      ->where('user_id', $user->id)
      ->where('status', Payment::PAID)
      ->where('id', '!=', $payment->id)
      ->exists();

    // === РЕФЕРАЛКА ===
    if ($user->referrer_id !== null) {
      $referrer = User::query()->find($user->referrer_id);
      if ($referrer) {
        // Проверяем, есть ли индивидуальный процент
        if ($referrer->custom_referral_percentage !== null) {
          $value = $referrer->custom_referral_percentage;
        } else {
          // Используем стандартный процент по уровню
          $referrerLevel = $referrer->referral_level;
          $value = match ($referrerLevel) {
            1 => 0.5,
            2 => 1,
            3 => 1.5,
            4 => 2,
            5 => 2.5,
            default => 0,
          };
        }

        // === ПЕРВЫЙ ДЕПОЗИТ ===
        if (!$hasOtherDeposits && $payment->amount >= 1000) {
          $fixedBonus = 2500; // 25 рублей
          $percentBonus = ($payment->amount * 100) * ($value / 100);
          $totalBonus = $fixedBonus + $percentBonus;

          $referrer->update([
            'balance' => $referrer->balance + $fixedBonus,

            'referral_balance' => $referrer->referral_balance + $percentBonus,
            'total_earned' => $referrer->total_earned + $totalBonus,
          ]);

          // === Фиксируем начисление ===
          ReferralEarning::query()->create([
            'user_id' => $referrer->id,
            'referral_id' => $user->id,
            'amount' => $totalBonus,
            'deposit_amount' => $payment->amount * 100,
            'percentage' => $value,
            'type' => 'nirvana',
            'description' => 'Бонус 25₽ и ' . $value . '% за первый депозит',
            'created_at' => now(),
            'updated_at' => now(),
          ]);

          Log::channel('payment_tbank')->info("Рефереру ID {$referrer->id} начислено 25₽ + {$value}% за первый депозит пользователя ID {$user->id}");
        }

        // === СЛЕДУЮЩИЕ ДЕПОЗИТЫ ===
        else {
          $percentBonus = ($payment->amount * 100) * ($value / 100);

          $referrer->update([
            'balance' => $referrer->balance + $percentBonus,
            'referral_balance' => $referrer->referral_balance + $percentBonus,
            'total_earned' => $referrer->total_earned + $percentBonus,
          ]);

          // === Фиксируем начисление ===
          ReferralEarning::query()->create([
            'user_id' => $referrer->id,
            'referral_id' => $user->id,
            'amount' => $percentBonus,
            'deposit_amount' => $payment->amount * 100,
            'percentage' => $value,
            'type' => 'nirvana',
            'description' => $value . '% от депозита',
            'created_at' => now(),
            'updated_at' => now(),
          ]);

          Log::channel('payment_tbank')->info("Рефереру ID {$referrer->id} начислено {$value}% от депозита пользователя ID {$user->id}");
        }

        $this->redisService->updateUserBalance($referrer->id, $referrer->balance, $referrer->event_points);
      }
    }

    $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);
    return 'OK';
  }


  private function getDepositLimits($method, $system)
  {
    $paymentMethod = PaymentMethods::where('method', $method)
      ->where('system', $system)
      ->first();

    if ($paymentMethod) {
      return [
        'min_amount' => $paymentMethod->min_amount,
        'max_amount' => $paymentMethod->max_amount
      ];
    }

    return [
      'min_amount' => 100000, // 10 руб
      'max_amount' => 10000000 // 10 000 руб
    ];
  }
}
