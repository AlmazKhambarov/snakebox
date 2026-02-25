<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserIpHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:20|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $ip = request()->getClientIp(true);
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => 'https://ui-avatars.com/api/?name=' . $request->username,
            'reg_ip' => $ip,
            'social' => 'site',
        ]);

        // Записываем IP в историю
        UserIpHistory::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'type' => 'registration',
            'description' => 'Регистрация',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Записываем IP при создании токена
        $accessToken = $user->tokens()->latest()->first();
        if ($accessToken) {
            UserIpHistory::create([
                'user_id' => $user->id,
                'ip_address' => $ip,
                'type' => 'token_created',
                'description' => 'Создание токена при регистрации',
                'token_id' => (string)$accessToken->id,
            ]);
        }

        return [
            'token' => $token,
        ];
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'You have not specified E-Mail.',
            'password.required' => 'You have not specified a password',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Incorrect Password or E-Mail entered'], 401);
        }

        $user = Auth::user();
        $ip = request()->getClientIp(true);

        $user->update([
            'last_ip' => $ip,
        ]);

        // Записываем IP в историю
        UserIpHistory::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'type' => 'login',
            'description' => 'Вход в систему',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Записываем IP при создании токена
        $accessToken = $user->tokens()->latest()->first();
        if ($accessToken) {
            UserIpHistory::create([
                'user_id' => $user->id,
                'ip_address' => $ip,
                'type' => 'token_created',
                'description' => 'Создание токена при входе',
                'token_id' => (string)$accessToken->id,
            ]);
        }

        return [
            'token' => $token,
        ];
    }
}
