<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Giveaway extends Model
{
    use HasFactory;

    protected $fillable = [
        'drop_id',
        'started_at',
        'finished_at',
        'min_deposit',
        'type',
        'status',
        'winner_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'min_deposit' => 'integer',
    ];

    /**
     * Связь с предметом
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Items::class, 'drop_id');
    }

    /**
     * Связь с победителем
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Связь с участниками
     */
    public function participants(): HasMany
    {
        return $this->hasMany(GiveawayParticipant::class);
    }

    /**
     * Проверка, активен ли розыгрыш
     */
    public function isActive(): bool
    {
        return $this->status === 'IN PROCESS' && 
               $this->finished_at > now();
    }

    /**
     * Проверка, закончился ли розыгрыш
     */
    public function isFinished(): bool
    {
        return $this->finished_at <= now() && 
               $this->status === 'IN PROCESS';
    }

    /**
     * Проверка участия пользователя
     */
    public function hasParticipant(int $userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Получить случайного победителя
     */
    public function selectWinner(): ?User
    {
        $participant = $this->participants()
            ->inRandomOrder()
            ->first();

        if ($participant) {
            $this->update([
                'winner_id' => $participant->user_id,
                'status' => 'FINISHED'
            ]);

            return $participant->user;
        }

        $this->update(['status' => 'FAILED']);
        return null;
    }

    /**
     * Получить конфигурацию типа розыгрыша
     */
    public static function getTypeConfig(string $type): array
    {
        $configs = [
            'hourly' => [
                'min_price' => 20000, // 200₽ * 100
                'max_price' => 50000, // 500₽ * 100
                'min_deposit' => 50,
                'duration' => 60, // минут
                'rarity' => 'restricted', // Mil-Spec Grade %
            ],
            'daily' => [
                'min_price' => 150000, // 1500₽ * 100
                'max_price' => 400000, // 4000₽ * 100
                'min_deposit' => 250,
                'duration' => 1440, // минут (24 часа)
                'rarity' => 'classified', // Restricted %
            ],
            'weekly' => [
                'min_price' => 500000, // 5000₽ * 100
                'max_price' => 1000000, // 10000₽ * 100
                'min_deposit' => 1000,
                'duration' => 10080, // минут (7 дней)
                'rarity' => 'covert', // Restricted % (более дорогие варианты)
            ],
        ];

        return $configs[$type] ?? [];
    }

    /**
     * Создать новый розыгрыш
     */
    public static function createGiveaway(string $type): ?self
    {
        $config = self::getTypeConfig($type);
        
        if (empty($config)) {
            return null;
        }

        // Получаем случайный предмет по параметрам (цена + редкость)
        $item = Items::whereBetween('steam_price', [$config['min_price'], $config['max_price']])
            ->where('rarity', 'like', ucfirst($config['rarity']) . ' %')
            ->inRandomOrder()
            ->first();

        if (!$item) {
            // Если не нашли с учетом rarity, берем только по цене
            $item = Items::whereBetween('steam_price', [$config['min_price'], $config['max_price']])
                ->inRandomOrder()
                ->first();
        }

        if (!$item) {
            return null;
        }

        $startedAt = now();
        $finishedAt = now()->addMinutes($config['duration']);

        return self::create([
            'drop_id' => $item->id,
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
            'min_deposit' => $config['min_deposit'],
            'type' => $type,
            'status' => 'IN PROCESS',
        ]);
    }
}

