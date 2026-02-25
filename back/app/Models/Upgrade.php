<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upgrade extends Model
{
    use HasFactory;

    const WIN = 'WIN';
    const LOSE = 'LOSE';

    protected $table = 'upgrades';

    protected $fillable = [
        'user_id',
        'item_id',
        'win_id',
        'price',
        'price_win',
        'profit',
        'percent',
        'status',
        'base_chance',
        'game_chance',
        'random_float',
    ];

    public function usedItem(): BelongsTo
    {
        return $this->belongsTo(Items::class, 'item_id', 'id');
    }

    public function winItem(): BelongsTo
    {
        return $this->belongsTo(Items::class, 'win_id', 'id');
    }
}
