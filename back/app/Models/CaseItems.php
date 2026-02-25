<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseItems extends Model
{
    protected $fillable = [
        'box_id',
        'skin_id',
        'chance',
    ];

    public function item()
    {
        return $this->belongsTo(Items::class, 'skin_id');
    }

    public function case()
    {
        return $this->belongsTo(Boxes::class, 'box_id');
    }
}
