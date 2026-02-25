<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserIpHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteamController extends Controller
{
    private $steamOpenIdUrl = 'https://steamcommunity.com/openid/login';

    public function redirectToSteam()
    {
        // Более надежное получение реферального кода
        $referralCode = request()->cookie('referral_code');

        Log::channel('auth_steam')->info("Redirect to Steam - referral code from cookie: " . $referralCode);

        $returnTo = route('auth.steam.handle');

        if ($referralCode && $this->isValidReferralCode($referralCode)) {
            $returnTo .= '?ref=' . urlencode($referralCode);
            Log::channel('auth_steam')->info("Added referral code to return URL: " . $returnTo);
        }

        $params = [
            'openid.ns'         => 'http://specs.openid.net/auth/2.0',
            'openid.mode'       => 'checkid_setup',
            'openid.return_to'  => $returnTo,
            'openid.realm'      => config('app.url'),
            'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        $steamUrl = $this->steamOpenIdUrl . '?' . http_build_query($params);
        Log::channel('auth_steam')->info("Final Steam URL: " . $steamUrl);

        return redirect($steamUrl);
    }

    public function handleSteamCallback(Request $request)
    {
        // Проверяем openid_mode (может быть как openid_mode, так и openid.mode)
        $openIdMode = $request->input('openid_mode') ?? ($_GET['openid.mode'] ?? $_POST['openid.mode'] ?? null);
        
        if ($openIdMode !== 'id_res') {
            Log::channel('auth_steam')->warning('Invalid openid_mode', ['mode' => $openIdMode]);
            return redirect('/');
        }

        // Получаем параметры напрямую из $_GET и $_POST, так как Laravel преобразует точки в подчеркивания
        $claimedId = $_GET['openid.claimed_id'] ?? $_POST['openid.claimed_id'] ?? $request->input('openid_claimed_id') ?? null;
        $identity = $_GET['openid.identity'] ?? $_POST['openid.identity'] ?? $request->input('openid_identity') ?? null;

        // Проверяем подпись OpenID ответа от Steam
        if (!$this->validateOpenIdSignature($request)) {
            Log::channel('auth_steam')->error('OpenID signature validation failed', [
                'ip' => $request->ip(),
                'claimed_id' => $claimedId,
            ]);
            return redirect('/');
        }

        // Проверяем, что ответ действительно от Steam
        
        if (!$this->isValidSteamOpenId($claimedId, $identity)) {
            Log::channel('auth_steam')->error('Invalid Steam OpenID', [
                'claimed_id' => $claimedId,
                'identity' => $identity,
                'ip' => $request->ip(),
            ]);
            return redirect('/');
        }

        // Извлекаем SteamID из claimed_id
        preg_match('/\d+$/', $claimedId, $matches);
        $steamId = $matches[0] ?? null;

        if (!$steamId) {
            Log::channel('auth_steam')->error('Could not extract SteamID', [
                'claimed_id' => $claimedId,
            ]);
            return redirect('/');
        }

        $apiKey = env('STEAM_CLIENT_SECRET');

        $response = Http::get("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/", [
            'key' => $apiKey,
            'steamids' => $steamId,
        ]);

        $playerData = $response->json()['response']['players'][0] ?? null;

        Log::channel('auth_steam')->info('request', ['player' => $playerData]);


        if (!$playerData) {
            return redirect('/');
        }

        $user = User::query()->where('steam_id', $steamId)->first();

        $ip = $request->ip();
        if ($user) {
            $user->update([
                'username' => $playerData['personaname'],
                'avatar' => $playerData['avatarfull'],
                'last_ip' => $ip,
            ]);
            
            // Записываем IP в историю
            UserIpHistory::create([
                'user_id' => $user->id,
                'ip_address' => $ip,
                'type' => 'login',
                'description' => 'Вход через Steam',
            ]);
        } else {
            $ip = $request->ip();
            $userData = [
                'username' => $playerData['personaname'],
                'steam_id' => $steamId,
                'avatar' => $playerData['avatarfull'],
                'reg_ip' => $ip,
                'social' => 'steam',
                'role' => 'client'
            ];

            // Получаем реферальный код из параметра URL
            $referralCode = $request->input('ref');

            // Если нет в URL, пробуем из куки
            if (!$referralCode) {
                $referralCode = $request->cookie('referral_code');
            }

            Log::channel('auth_steam')->info("Referral code found: " . $referralCode);

            $referrer = $this->getReferrerFromCode($referralCode);

            if ($referrer) {
                $userData['referrer_id'] = $referrer->id;
                Log::channel('auth_steam')->info("Referrer found: " . $referrer->id);
            }

            $user = User::query()->create($userData);

            // Записываем IP в историю
            UserIpHistory::create([
                'user_id' => $user->id,
                'ip_address' => $ip,
                'type' => 'registration',
                'description' => 'Регистрация через Steam',
            ]);

            // Обновляем счетчик рефералов
            if ($referrer) {
                $referrer->increment('referrals_count');
                Log::channel('auth_steam')->info("Updated referrals count for user: " . $referrer->id);

                // Очищаем куку после использования
                // cookie()->queue(cookie()->forget('referral_code'));
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Записываем IP при создании токена
        $accessToken = $user->tokens()->latest()->first();
        if ($accessToken) {
            UserIpHistory::create([
                'user_id' => $user->id,
                'ip_address' => $ip ?? $request->ip(),
                'type' => 'token_created',
                'description' => 'Создание токена через Steam',
                'token_id' => (string)$accessToken->id,
            ]);
        }
        $url = config('app.frontend_url') . '/auth/callback?token=' . $token;

        return redirect($url);
    }

    /**
     * Получаем пользователя-пригласителя по коду
     */
    private function getReferrerFromCode($referralCode)
    {
        if (!$referralCode || !$this->isValidReferralCode($referralCode)) {
            Log::channel('auth_steam')->warning("Invalid referral code: " . $referralCode);
            return null;
        }

        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer) {
            Log::channel('auth_steam')->warning("Referrer not found for code: " . $referralCode);
            return null;
        }

        return $referrer;
    }

    /**
     * Валидация реферального кода
     */
    private function isValidReferralCode($code)
    {
        return is_string($code) && strlen($code) >= 3 && strlen($code) <= 20;
    }

    /**
     * Проверка подписи OpenID ответа от Steam
     */
    private function validateOpenIdSignature(Request $request): bool
    {
        try {
            // Получаем все параметры OpenID из запроса
            // Steam отправляет параметры через GET, но Laravel преобразует точки в подчеркивания
            // Поэтому получаем напрямую из $_GET и $_POST
            $openIdParams = [];
            
            // Сначала пробуем из $_GET (Steam обычно отправляет через GET)
            foreach ($_GET ?? [] as $key => $value) {
                if (strpos($key, 'openid.') === 0) {
                    $openIdParams[$key] = $value;
                }
            }
            
            // Затем из $_POST (на случай, если Steam отправит через POST)
            foreach ($_POST ?? [] as $key => $value) {
                if (strpos($key, 'openid.') === 0) {
                    $openIdParams[$key] = $value;
                }
            }
            
            // Если параметры не найдены, пробуем получить из request->all() и преобразовать
            if (empty($openIdParams)) {
                $allParams = $request->all();
                foreach ($allParams as $key => $value) {
                    // Laravel преобразует openid.claimed_id в openid_claimed_id
                    if (strpos($key, 'openid_') === 0) {
                        $openIdKey = str_replace('openid_', 'openid.', $key);
                        $openIdParams[$openIdKey] = $value;
                    } elseif (strpos($key, 'openid.') === 0) {
                        $openIdParams[$key] = $value;
                    }
                }
            }
            
            // Убеждаемся, что у нас есть все необходимые параметры OpenID
            if (empty($openIdParams['openid.claimed_id']) || empty($openIdParams['openid.identity']) || empty($openIdParams['openid.sig'])) {
                // Пробуем еще раз получить из query string напрямую
                $queryString = $request->server->get('QUERY_STRING');
                if ($queryString) {
                    parse_str($queryString, $queryParams);
                    foreach ($queryParams as $key => $value) {
                        if (strpos($key, 'openid.') === 0) {
                            $openIdParams[$key] = $value;
                        }
                    }
                }
                
                // Если все еще нет параметров, логируем детали
                if (empty($openIdParams['openid.claimed_id']) || empty($openIdParams['openid.identity']) || empty($openIdParams['openid.sig'])) {
                    Log::channel('auth_steam')->error('Missing required OpenID parameters', [
                        'available_params' => array_keys($openIdParams),
                        'get_params' => array_keys($_GET ?? []),
                        'post_params' => array_keys($_POST ?? []),
                        'request_all' => array_keys($request->all()),
                        'query_string' => $queryString,
                        'request_uri' => $request->getRequestUri(),
                    ]);
                    return false;
                }
            }
            
            // Добавляем mode для проверки подписи (заменяем существующий)
            $openIdParams['openid.mode'] = 'check_authentication';
            
            // Удаляем параметры, которые не нужны для проверки
            unset($openIdParams['ref']);
            
            // Отправляем POST запрос на Steam OpenID сервер для проверки подписи
            $response = Http::asForm()->post($this->steamOpenIdUrl, $openIdParams);
            
            if (!$response->successful()) {
                Log::channel('auth_steam')->error('Steam OpenID verification request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }
            
            $responseBody = $response->body();
            
            // Проверяем, что ответ содержит is_valid:true
            if (strpos($responseBody, 'is_valid:true') === false) {
                Log::channel('auth_steam')->error('OpenID signature validation failed', [
                    'response' => $responseBody,
                ]);
                return false;
            }
            
            // Дополнительно проверяем, что return_to соответствует нашему
            $expectedReturnTo = route('auth.steam.handle');
            $actualReturnTo = $openIdParams['openid.return_to'] ?? null;
            
            if ($actualReturnTo) {
                // Убираем параметры из return_to для сравнения
                $expectedBase = parse_url($expectedReturnTo, PHP_URL_PATH);
                $actualBase = parse_url($actualReturnTo, PHP_URL_PATH);
                
                if ($expectedBase !== $actualBase) {
                    Log::channel('auth_steam')->error('Return URL mismatch', [
                        'expected' => $expectedBase,
                        'actual' => $actualBase,
                    ]);
                    return false;
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::channel('auth_steam')->error('Exception during OpenID validation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Проверка, что OpenID ответ действительно от Steam
     */
    private function isValidSteamOpenId(?string $claimedId, ?string $identity): bool
    {
        if (!$claimedId || !$identity) {
            return false;
        }
        
        // Steam OpenID должен начинаться с https://steamcommunity.com/openid/id/
        $steamOpenIdPrefix = 'https://steamcommunity.com/openid/id/';
        
        if (strpos($claimedId, $steamOpenIdPrefix) !== 0) {
            return false;
        }
        
        if (strpos($identity, $steamOpenIdPrefix) !== 0) {
            return false;
        }
        
        // Проверяем, что claimed_id и identity совпадают (для Steam они должны быть одинаковыми)
        if ($claimedId !== $identity) {
            return false;
        }
        
        return true;
    }
}
