<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;



class CategoriesController extends Controller
{
  public function get()
  {
    return datatables(Categories::query())->toJson();
  }

  public function create(Request $request): array
  {
    $name = $request->name;
    $type = $request->type;
    $position = $request->position;
    $is_active = $request->is_active;

    if (!$name) return ['success' => false, 'message' => 'You did not specify a category name'];
    if (!$type) return ['success' => false, 'message' => 'You have not specified the category type.'];
    if ($position < 0) return ['success' => false, 'message' => 'You have not specified a category position.'];
    if ($is_active < 0) return ['success' => false, 'message' => 'You did not indicate whether the category is hidden'];

    $exist = Categories::query()->where('position', $position)->first();
    if ($exist) return ['success' => false, 'message' => 'Category with this position already exists'];


    Categories::query()->create([
      'name' => $name,
      'position' => $position,
      'type' => $type,
      'is_active' => $is_active
    ]);

    return ['success' => true, 'message' => 'Category created'];
  }

  public function category(Request $request): array
  {
    $id = $request->id;

    $category = Categories::query()->find($id);
    if (!$category) return ['success' => false, 'message' => 'Category not found'];

    return [
      'success' => true,
      'category' => $category
    ];
  }

  public function delete(Request $request): array
  {
    $id = $request->id;

    $category = Categories::query()->find($id);
    if (!$category) return ['success' => false, 'message' => 'Category not found'];

    $category->cases()->update([
      'category_id' => null
    ]);

    $category->delete();

    return [
      'success' => true,
      'message' => 'Category deleted'
    ];
  }

  public function save(Request $request): array
  {
    $id = $request->id;

    $name = $request->name;
    $position = $request->position;
    $type = $request->type;
    $is_active = $request->is_active;

    if (!$name) return ['success' => false, 'message' => 'You did not specify a category name'];
    if (!$type) return ['success' => false, 'message' => 'You have not specified the category type.'];
    if ($position < 0) return ['success' => false, 'message' => 'You have not specified a category position.'];
    if ($is_active < 0) return ['success' => false, 'message' => 'You did not indicate whether the category is hidden'];

    $category = Categories::query()->find($id);
    if (!$category) return ['success' => false, 'message' => 'Category not found'];

    // $exist = Categories::query()->where('name', $name)->orWhere('position', $position)->where('id', '<>', $id)->first();
    // if ($exist) return ['success' => false, 'message' => 'Такое название или позиция уже используются'];

    $category->update([
      'name' => $name,
      'position' => $position,
      'type' => $type,
      'is_active' => $is_active
    ]);

    return ['success' => true, 'message' => 'Category updated'];
  }
}
