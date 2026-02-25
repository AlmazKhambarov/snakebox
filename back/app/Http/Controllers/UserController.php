<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lives;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\EventScores;

class UserController extends Controller
{
    public function getAdmin(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return [
                'success' => false,
                'message' => 'У вас нет доступа к этой странице',
                'status' => 403,
            ];
        }

        Log::channel('user')->info($user->role);
        return [
            'success' => true,
            'user' => $user,
            'result' => true,
            'status' => 200
        ];
    }
    public function index(Request $request)
    {

        $id = $request->user()->id;
        $name = $request->user()->username;
        $steamId = $request->user()->steam_id;
        $role = $request->user()->role;
        $avatar = $request->user()->avatar;
        $isBanned = $request->user()->is_banned;
        $balance = $request->user()->balance;
        $tradeLink = $request->user()->trade_url;
        $social = $request->user()->social;
        $total_bet = $request->user()->total_bet;
        $event_points = EventScores::where('user_id', $id)
        ->where('reward_received', false)
        ->sum('points');
        $is_vip = $request->user()->is_vip;

        $provablyController = new ProvablyFairController();
        $provablyData = $provablyController->index($request)->getData();

        $livesCount = Lives::where('user_id', $id)->count();

        $topDrop = Lives::where('user_id', $id)
            ->orderBy('price', 'desc') // сортировка по цене
            ->with('item')             // подтягиваем связь с предметом
            ->first();

        // Любимый кейс (самый часто открываемый кейс)
        $favoriteBox = Lives::select('box_id')
            ->where('user_id', $id)
            ->groupBy('box_id')
            ->orderByRaw('COUNT(*) DESC')
            ->with('box') // подключаем связь с кейсом
            ->first();



        return [
            'user' => [
                'id' => $id,
                'username' => $name,
                'steamId' => $steamId,
                'role' => $role,
                'avatar' => $avatar,
                'isBanned' => $isBanned,
                'balance' => $balance,
                'tradeLink' => $tradeLink,
                'social' => $social,
                'total_bet' => $total_bet,
                'lives_count' => $livesCount,
                'top_drop' => $topDrop ? $topDrop->item : null,
                'favorite_box' => $favoriteBox ? $favoriteBox->box : null,
                'event_points' => $event_points,
                'is_vip' => $is_vip
            ],
            // 'provably' => $provablyData,
            'result' => true,
            'status' => 200,
        ];
    }

    public function getOtherProfile(Request $request)
    {
        $id = $request->id;
        Log::channel('user')->info($id);
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $topDrop = Lives::where('user_id', $user->id)
            ->orderBy('price', 'desc')
            ->with('item')
            ->first();

        $favoriteBox = Lives::select('box_id')
            ->where('user_id', $user->id)
            ->where('box_id', '!=', null)
            ->groupBy('box_id')
            ->orderByRaw('COUNT(*) DESC')
            ->with('box')
            ->first();

        $query = Lives::query()
            ->select(['id', 'user_id', 'skin_id', 'price', 'status', 'created_at'])
            ->with(['item:id,weapon,skin_name,rarity,quality,image,steam_price'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        $items = $query->paginate(12);

        return [
            'user' => $user,
            'topDrop' => $topDrop,
            'favoriteBox' => $favoriteBox,
            'items' => $items,
            'success' => true,
            'status' => 200,
            'hasMorePages' => $items->hasMorePages(),
        ];
    }
    public function getItems(Request $request): array
    {
        $user = $request->user();

        $min = $request->min;
        $max = $request->max;
        $title = strval($request->market_name);
        $status = $request->status;
        $sort = $request->sort;

        $query = Lives::query()
            ->select(['id', 'user_id', 'skin_id', 'price', 'status', 'created_at'])
            ->with(['item:id,weapon,skin_name,rarity,quality,image,steam_price'])
            ->where('user_id', $user->id);

        // Фильтр по статусу
        if ($status && in_array($status, ['STOCK', 'SELL', 'WITHDRAWN'])) {
            $query->where('status', $status);
        }

        // Фильтр по цене
        if ($min) {
            $query->where('price', '>=', $min);
        }

        if ($max) {
            $query->where('price', '<=', $max);
        }

        // Фильтр по названию
        if ($title) {
            $query->whereHas('item', function ($q) use ($title) {
                $q->where('weapon', 'like', "%{$title}%")
                    ->orWhere('skin_name', 'like', "%{$title}%");
            });
        }

        // Сортировка
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $items = $query->paginate(12);

        return [
            'success' => true,
            'items' => $items,
            'hasMorePages' => $items->hasMorePages(),
        ];
    }

    public function tradeLink(Request $request)
    {
        $user = $request->user();

        $link = $request->link;

        if (strlen($link) > 255) {
            return [
                'success' => false,
                'message' => 'Слишком длинная ссылка'
            ];
        }

        if (!$this->parseSteamTradeUrl($link)) {
            return [
                'success' => false,
                'message' => 'Ссылка указана неверно!'
            ];
        }

        if (!$this->_parseTradeLink($link)) {
            return [
                'success' => false,
                'message' => 'Ссылка указана неверно!'
            ];
        }

        $user->update([
            'trade_url' => $link
        ]);

        return [
            'success' => true,
            'message' => 'Трейд ссылка сохранена',
            'link' => $link
        ];
    }

    private function _parseTradeLink($tradeLink)
    {
        $query_str = parse_url($tradeLink, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        return isset($query_params['token']) ? $query_params['token'] : false;
    }

    private function parseSteamTradeUrl($tradeLink): bool
    {
        return str_contains($tradeLink, 'https://steamcommunity.com/tradeoffer/new/');
    }
}
