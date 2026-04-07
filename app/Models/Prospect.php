<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nom',
        'status',
        'rdv_date',
        'response_type',
        'quote_number',
        'is_followup',
        'source',
    ];

    protected $casts = [
        'rdv_date' => 'date',
    ];

    public function notes()
    {
        return $this->morphMany(Reason::class, 'raisonable');
    }

    public function lastNote () {
        return $this->notes()->latest()->first();
    }
}
