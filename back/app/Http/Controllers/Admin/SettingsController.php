<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function get()
    {
        return Settings::query()->first();
    }

    public function save(Request $request): array
    {
        if (!$request->site_name) return ['success' => false, 'message' => 'Введите название сайта'];
        if (!$request->domain) return ['success' => false, 'message' => 'Введите домен сайта'];


        Settings::query()->first()->update([
            'domain' => $request->domain,
            'site_name' => $request->site_name,
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,

            'vk_group' => $request->vk_group,
            'tg_group' => $request->tg_group,

            'market_key' => $request->market_key,
        ]);

        return [
            'success' => true,
            'message' => 'Настройки сохранены'
        ];
    }
}
