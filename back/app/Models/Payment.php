<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{

    protected $casts = [
        'metadata' => 'array',
    ];

    public const PENDING = 0;
    public const PAID = 1;
    public const CANCELLED = 2;
    public const FAILED = 3;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promocode::class, 'promocode_id');
    }
}
