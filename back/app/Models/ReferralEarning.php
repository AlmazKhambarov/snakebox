<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralEarning extends Model
{
  protected $fillable = [
    'user_id',
    'referral_id',
    'amount',
    'deposit_amount',
    'percentage',
    'type',
    'description'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function referral()
  {
    return $this->belongsTo(User::class, 'referral_id');
  }
}
