<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boxes extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'url',
        'image',
        'price',
        'discount',
        'opened',
        'min_dep',
        'is_active',
        'is_visible',
        'total_stock',
        'in_stock',
        'type',
        'game',
        'profit',
        'sound_pack',
        // RTP поля
        'current_rtp',
        'target_rtp',
        'min_rtp',
        'max_rtp',
        'total_opened',
        'total_spent',
        'total_won',
        'last_rtp_update',
        // Auto disabled поля
        'auto_disabled',
        'auto_disabled_reason',
        'auto_disabled_at',
    ];

    protected $casts = [
        'current_rtp' => 'float',
        'target_rtp' => 'float',
        'min_rtp' => 'float',
        'max_rtp' => 'float',
        'total_opened' => 'integer',
        'total_spent' => 'integer',
        'total_won' => 'integer',
        'last_rtp_update' => 'datetime',
        'auto_disabled' => 'boolean',
        'auto_disabled_at' => 'datetime',
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function items()
    {
        return $this->hasMany(CaseItems::class, 'box_id');
    }
}
