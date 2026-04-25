<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $fillable = [
        'title',
        'image',
        'rarity',
        'quality',
        'skin_name',
        'weapon',
        'steam_price',
        'steam_price_before',
        'game',
    ];
}
