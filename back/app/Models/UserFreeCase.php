<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFreeCase extends Model
{
  use HasFactory;

  protected $table = 'user_free_cases';

  protected $fillable = [
    'user_id',
    'case_id',
    'promocode_id',
    'is_used',
    'used_at'
  ];

  protected $casts = [
    'is_used' => 'boolean',
    'used_at' => 'datetime'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function case()
  {
    return $this->belongsTo(Boxes::class, 'case_id'); // указываем связь с Box моделью
  }

  public function promocode()
  {
    return $this->belongsTo(Promocode::class);
  }

  public function markAsUsed()
  {
    $this->update([
      'is_used' => true,
      'used_at' => now()
    ]);
  }

  public function scopeAvailable($query)
  {
    return $query->where('is_used', false);
  }

  public function scopeUsed($query)
  {
    return $query->where('is_used', true);
  }
}
