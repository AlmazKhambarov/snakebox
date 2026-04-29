<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    // Types
    const TYPE_UC = 'uc';

    // Statuses (int column in DB)
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DECLINED = 2;

    protected $guarded = [];

    protected $casts = [
        'price' => 'integer',
        'amount' => 'integer',
        'status' => 'integer',
        'type' => 'string',
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
