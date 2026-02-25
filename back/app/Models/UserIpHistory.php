<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIpHistory extends Model
{
    use HasFactory;

    protected $table = 'user_ip_history';

    protected $fillable = [
        'user_id',
        'ip_address',
        'type',
        'description',
        'token_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
