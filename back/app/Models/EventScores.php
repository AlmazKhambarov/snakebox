<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventScores extends Model
{
  use HasFactory;

  protected $table = 'event_scores';

  protected $fillable = [
    'user_id',
    'event_id',
    'points',
    'position',
    'reward_received'
  ];

  protected $casts = [
    'reward_received' => 'boolean'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function event()
  {
    return $this->belongsTo(Event::class, 'event_id');
  }

  public function addPoints(int $points)
  {
    $this->increment('points', $points);
    $this->updatePosition();
  }

  public  function updatePosition()
  {
    $position = self::where('event_id', $this->event_id)
      ->where('points', '>', $this->points)
      ->count() + 1;

    $this->update(['position' => $position]);
  }

  public function getRewardAmount()
  {
    $rewards = $this->event->rewards ?? [];
    return $rewards[$this->position] ?? 0;
  }

  public function prize()
  {
    return $this->hasOne(EventPrize::class, 'event_id', 'event_id')
      ->where('position', $this->position);
  }

  public function getPrizeItem()
  {
    $prize = $this->event->getPrizeForPosition($this->position);
    if (!$prize || !$prize->item_id) {
      return null;
    }

    return $prize->item;
  }
}
