<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $fillable = [
        'description',
        'is_finish',
        'raisonable_id',
        'raisonable_type',
        ];

    public function raisonable()
    {
        return $this->morphTo();
    }
}
