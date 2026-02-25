<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPrize extends Model
{
    use HasFactory;

    protected $table = 'event_prizes';

    protected $fillable = [
        'event_id',
        'position',
        'item_id',
        'min_price',
        'max_price',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Items::class, 'item_id');
    }

    /**
     * Получить случайный скин в диапазоне цен
     */
    public function getRandomItem()
    {
        if ($this->item_id) {
            return $this->item;
        }

        if ($this->min_price && $this->max_price) {
            return Items::where('steam_price', '>=', $this->min_price)
                ->where('steam_price', '<=', $this->max_price)
                ->whereNotNull('image')
                ->inRandomOrder()
                ->first();
        }

        return null;
    }
}
