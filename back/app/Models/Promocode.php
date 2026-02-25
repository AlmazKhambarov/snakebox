<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
  use HasFactory;

  protected $fillable = [
    'code',
    'type',
    'value',
    'skin_id',
    'case_id',
    'uses_left',
    'max_uses',
    'valid_from',
    'valid_until',
    'is_active'
  ];

  protected $casts = [
    'valid_from' => 'datetime',
    'valid_until' => 'datetime',
    'is_active' => 'boolean',
  ];

  public function uses()
  {
    return $this->hasMany(PromocodeUse::class);
  }

  public function isExpired()
  {
    if ($this->valid_from && now()->lt($this->valid_from)) {
      return true;
    }

    if ($this->valid_until && now()->gt($this->valid_until)) {
      return true;
    }

    return $this->uses_left <= 0;
  }

  public function isUsedByUser($userId)
  {
    return $this->uses()->where('user_id', $userId)->exists();
  }

  public function canBeUsedByUser($userId)
  {
    return !$this->isExpired() &&
      $this->is_active &&
      !$this->isUsedByUser($userId) &&
      $this->uses_left > 0;
  }
}
