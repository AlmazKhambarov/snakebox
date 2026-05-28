<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpgradeRtpStats extends Model
{
    use HasFactory;

    protected $table = 'upgrade_rtp_stats';

    protected $fillable = [
        'total_upgrades',
        'total_spent',
        'total_won',
        'target_rtp',
        'min_rtp',
        'max_rtp',
        'current_rtp',
        'last_rtp_update',
        'chance_boost',
    ];

    protected $casts = [
        'target_rtp' => 'decimal:2',
        'min_rtp' => 'decimal:2',
        'max_rtp' => 'decimal:2',
        'current_rtp' => 'decimal:2',
        'chance_boost' => 'decimal:2',
        'last_rtp_update' => 'datetime',
    ];

    /**
     * Получить или создать единственную запись статистики
     */
    public static function getStats(): self
    {
        $stats = self::first();
        
        if (!$stats) {
            $stats = self::create([
                'total_upgrades' => 0,
                'total_spent' => 0,
                'total_won' => 0,
                'target_rtp' => 92.0,
                'min_rtp' => 88.0,
                'max_rtp' => 96.0,
                'current_rtp' => 92.0,
            ]);
        }
        
        return $stats;
    }
}
