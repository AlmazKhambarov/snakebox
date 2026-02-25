<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusClaim extends Model
{
    protected $fillable = [
        'user_id',
        'bonus_type',
        'amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}











