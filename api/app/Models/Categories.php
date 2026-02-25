<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = [
        'name',
        'type',
        'position',
        'is_active',
    ];

    public function cases()
    {
        return $this->hasMany(Boxes::class, 'category_id');
    }
}
