<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocodeUse extends Model
{
  use HasFactory;

  protected $fillable = [
    'promocode_id',
    'user_id',
    'bonus_amount',
    'metadata'
  ];

  protected $casts = [
    'metadata' => 'array',
  ];

  public function promocode()
  {
    return $this->belongsTo(Promocode::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
