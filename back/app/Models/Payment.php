<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    // Types
    const TYPE_UC = 'uc';

    // Statuses (int column in DB) — original names
    public const PENDING = 0;
    public const PAID = 1;
    public const CANCELLED = 2;
    public const FAILED = 3;

    // Aliases for new code
    const STATUS_PENDING = self::PENDING;
    const STATUS_APPROVED = self::PAID;
    const STATUS_DECLINED = self::CANCELLED;

    protected $guarded = [];

    protected $casts = [
        'price' => 'integer',
        'amount' => 'integer',
        'status' => 'integer',
        'type' => 'string',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promocode::class, 'promocode_id');
    }
}
?>
