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

    public function get(Request $request)
    {
        $query = Items::query();
        
        if ($request->filled('game')) {
            $query->where('game', $request->game);
        }

        return datatables($query)->toJson();
    }

    public function createItem(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'icon_url' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric',
            'game' => 'nullable|string|in:cs,pubg',
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
            'game' => $request->game ?? 'cs',
        ]);

        return ['success' => true, 'message' => 'Предмет успешно создан!'];
    }

    public function updateItem(Request $request): array
    {
        $item = Items::find($request->id);
        if (!$item) {
            return ['success' => false, 'message' => 'Предмет не найден'];
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'icon_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'game' => 'nullable|string|in:cs,pubg',
        ], [
            'name.required' => 'Укажите название предмета',
            'price.required' => 'Укажите цену предмета',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        $updateData = [
            'title' => $request->name,
            'steam_price' => $request->price,
            'rarity' => $request->rarity ?? $item->rarity,
            'game' => $request->game ?? $item->game,
        ];

        if ($request->hasFile('icon_url')) {
            // Delete old image
            if ($item->image) {
                $parts = explode('/storage/', $item->image);
                $oldPath = end($parts);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $randomId = Str::random(10);
            $image = $request->file('icon_url');
            $imageName = $randomId . '_' . time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('items', $image, $imageName);
            $updateData['image'] = config('app.url') . '/storage/items/' . $imageName;
        }

        $item->update($updateData);

        return ['success' => true, 'message' => 'Предмет успешно обновлен!'];
    }

    public function deleteItem(Request $request): array
    {
        $item = Items::find($request->id);
        if (!$item) {
            return ['success' => false, 'message' => 'Предмет не найден'];
        }

        // Check if item is used in any cases
        $usedInCases = \App\Models\CaseItems::where('skin_id', $item->id)->count();
        if ($usedInCases > 0) {
            return ['success' => false, 'message' => 'Предмет используется в ' . $usedInCases . ' кейсах. Сначала удалите его из кейсов.'];
        }

        // Delete image
        if ($item->image) {
            $parts = explode('/storage/', $item->image);
            $oldPath = end($parts);
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $item->delete();

        return ['success' => true, 'message' => 'Предмет успешно удален!'];
    }
}
