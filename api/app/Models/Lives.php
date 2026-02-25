<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lives extends Model
{

    const OPENED = 'STOCK';
    const SELL = 'SELL';
    const SENDING = 'SENDING';
    const WAIT = 'WAIT';
    const ORDER_READY = 'ORDER_READY';
    const TRADE_LOCK = 'TRADE_LOCK';
    const WITHDRAWN = 'WITHDRAWN';

    const CONTRACTED = 'CONTRACTED';
    const CASE_TYPE = 'BOX';

    protected $fillable = [
        'user_id',
        'skin_id',
        'box_id',
        'from_where',
        'price',
        'trade_id',
        'market_id',
        'custom_id',
        'time',
        'settlement',
        'send_until',
        'status',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Items::class, 'skin_id', 'id');
    }


    public function box(): BelongsTo
    {
        return $this->belongsTo(Boxes::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
