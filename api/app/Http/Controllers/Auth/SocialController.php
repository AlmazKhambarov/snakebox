<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserIpHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class SocialController extends Controller
{
  protected $providers = ['google', 'discord', 'facebook', 'twitter'];

  public function redirect($provider)
  {
    if (!in_array($provider, $this->providers)) {
      return response()->json(['error' => 'Unsupported provider'], 422);
    }

    return Socialite::driver($provider)->stateless()->redirect();
  }

  public function callback($provider, Request $request)
  {
    if (!in_array($provider, $this->providers)) {
      return response()->json(['error' => 'Unsupported provider'], 422);
    }

    $socialUser = Socialite::driver($provider)->stateless()->user();
    $ip = $request->getClientIp(true);
    $isNewUser = false;

    $user = User::firstOrCreate(
      [
        'email' => $socialUser->getEmail(),
      ],
      [
        'username' => $socialUser->getNickname() ?? $socialUser->getName() ?? Str::random(8),
        'email' => $socialUser->getEmail(),
        'avatar' => $socialUser->getAvatar(),
        'password' => Hash::make(Str::random(16)),
        'reg_ip' => $ip,
        'social' => $provider,
      ]
    );

    if ($user->wasRecentlyCreated) {
      $isNewUser = true;
      // Записываем IP в историю при регистрации
      UserIpHistory::create([
        'user_id' => $user->id,
        'ip_address' => $ip,
        'type' => 'registration',
        'description' => 'Регистрация через ' . ucfirst($provider),
      ]);
    } else {
      // Обновляем last_ip при входе
      $user->update(['last_ip' => $ip]);
      // Записываем IP в историю при входе
      UserIpHistory::create([
        'user_id' => $user->id,
        'ip_address' => $ip,
        'type' => 'login',
        'description' => 'Вход через ' . ucfirst($provider),
      ]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;
    
    // Записываем IP при создании токена
    $accessToken = $user->tokens()->latest()->first();
    if ($accessToken) {
      UserIpHistory::create([
        'user_id' => $user->id,
        'ip_address' => $ip,
        'type' => 'token_created',
        'description' => 'Создание токена через ' . ucfirst($provider),
        'token_id' => (string)$accessToken->id,
      ]);
    }

    return redirect(config('app.frontend_url') . '/auth/callback?token=' . $token);
  }
}
