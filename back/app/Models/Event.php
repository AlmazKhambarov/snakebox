<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Event extends Model
{
  use HasFactory;

  protected $table = 'event';

  protected $fillable = [
    'name',
    'start_date',
    'end_date',
    'is_active',
    'rewards'
  ];

  protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'rewards' => 'array',
    'is_active' => 'boolean'
  ];

  public function scores()
  {
    return $this->hasMany(EventScores::class, 'event_id');
  }

  public function prizes()
  {
    return $this->hasMany(EventPrize::class, 'event_id');
  }

  public function getPrizeForPosition(int $position)
  {
    return $this->prizes()->where('position', $position)->first();
  }

  public static function getCurrentEvent()
  {

    $allEvents = self::all();
    Log::info($allEvents);
    $event = self::where('is_active', true)
      ->where('start_date', '<=', now())
      ->where('end_date', '>=', now())
      ->first();

    return $event;
  }
  public function isActive()
  {
    return $this->is_active &&
      now()->between($this->start_date, $this->end_date);
  }

  public function getTimeRemaining()
  {
    if (!$this->isActive()) {
      return 0;
    }

    return $this->end_date->diffInSeconds(now());
  }
}
