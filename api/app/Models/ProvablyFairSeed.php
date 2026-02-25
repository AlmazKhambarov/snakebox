<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvablyFairSeed extends Model
{
  protected $fillable = [
    'user_id',
    'server_seed',
    'server_seed_hashed',
    'client_seed',
    'nonce',
    'active',
    'revealed_at',
  ];

  protected $casts = [
    'revealed_at' => 'datetime',
    'active' => 'boolean',
  ];
}
