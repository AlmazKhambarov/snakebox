<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Items;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemsController extends Controller
{
    protected MarketService $marketService;
    public function __construct(
        MarketService $marketService
    ) {
        $this->marketService = $marketService;
    }

    public function get()
    {
        return datatables(Items::query())->toJson();
    }

    public function createItem(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'icon_url' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric',
        ], [
            'name.required' => 'Укажите название предмета',
            'icon_url.required' => 'Загрузите изображение предмета',
            'icon_url.image' => 'Файл должен быть изображением',
            'price.required' => 'Укажите цену предмета',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        $randomId = Str::random(10);
        $image = $request->file('icon_url');
        $imageName = $randomId . '_' . time() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('items', $image, $imageName);

        Items::query()->create([
            'title' => $request->name,
            'image' => config('app.url') . '/storage/items/' . $imageName,
            'steam_price' => $request->price,
            'rarity' => $request->rarity ?? 'common',
        ]);

        return ['success' => true, 'message' => 'Предмет успешно создан!'];
    }
}
