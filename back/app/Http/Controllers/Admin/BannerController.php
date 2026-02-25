<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        return datatables(Banner::query()->orderBy('position', 'ASC'))->toJson();
    }

    public function get(Request $request)
    {
        $banner = Banner::find($request->id);
        if (!$banner) {
            return ['success' => false, 'message' => 'Баннер не найден'];
        }

        return [
            'success' => true,
            'banner' => $banner,
        ];
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'image.required' => 'Вы не указали картинку баннера',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Изображение должно быть в формате jpeg, png, jpg, gif или webp',
            'image.max' => 'Размер изображения не должен превышать 2MB',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        // Создаем директорию, если её нет
        $bannersPath = base_path('frontend/public/assets/banners');
        if (!file_exists($bannersPath)) {
            mkdir($bannersPath, 0755, true);
        }

        $randomId = Str::random(10);
        $image = $request->file('image');
        $imageName = $randomId . '_' . time() . '.' . $image->getClientOriginalExtension();
        $image->move($bannersPath, $imageName);

        $banner = Banner::create([
            'title' => $request->title,
            'text' => $request->text,
            'image' => '/assets/banners/' . $imageName,
            'link' => $request->link,
            'button_text' => $request->button_text,
            'position' => $request->position ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        Log::channel('admin_banners')->info('Banner created', [
            'banner_id' => $banner->id,
            'title' => $banner->title,
        ]);

        return [
            'success' => true,
            'message' => 'Баннер успешно создан',
            'banner_id' => $banner->id,
        ];
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:banners,id',
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'id.required' => 'ID баннера обязателен',
            'id.exists' => 'Баннер не найден',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Изображение должно быть в формате jpeg, png, jpg, gif или webp',
            'image.max' => 'Размер изображения не должен превышать 2MB',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        $banner = Banner::find($request->id);
        if (!$banner) {
            return ['success' => false, 'message' => 'Баннер не найден'];
        }

        $updateData = [
            'title' => $request->title,
            'text' => $request->text,
            'link' => $request->link,
            'button_text' => $request->button_text,
            'position' => $request->position ?? $banner->position,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $banner->is_active,
        ];

        // Если загружено новое изображение
        if ($request->hasFile('image')) {
            // Удаляем старое изображение
            if ($banner->image) {
                $oldImagePath = base_path('frontend/public' . $banner->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Сохраняем новое изображение
            $bannersPath = base_path('frontend/public/assets/banners');
            if (!file_exists($bannersPath)) {
                mkdir($bannersPath, 0755, true);
            }

            $randomId = Str::random(10);
            $image = $request->file('image');
            $imageName = $randomId . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move($bannersPath, $imageName);

            $updateData['image'] = '/assets/banners/' . $imageName;
        }

        $banner->update($updateData);

        Log::channel('admin_banners')->info('Banner updated', [
            'banner_id' => $banner->id,
        ]);

        return [
            'success' => true,
            'message' => 'Баннер успешно обновлен',
        ];
    }

    public function delete(Request $request)
    {
        $banner = Banner::find($request->id);
        if (!$banner) {
            return ['success' => false, 'message' => 'Баннер не найден'];
        }

        // Удаляем изображение
        if ($banner->image) {
            $imagePath = base_path('frontend/public' . $banner->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $banner->delete();

        Log::channel('admin_banners')->info('Banner deleted', [
            'banner_id' => $request->id,
        ]);

        return [
            'success' => true,
            'message' => 'Баннер успешно удален',
        ];
    }
}
