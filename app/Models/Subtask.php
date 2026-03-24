<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    /** @use HasFactory<\Database\Factories\SubtaskFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'label',
        'status',
        'due_date',
        'quote_number',
        'billing_info',
        'estimated_hours',
        'actual_hours',
        'is_paid'
    ];

    public function formatDuration($decimalHours)
    {
        // On force en float pour être sûr, et on gère le cas vide/null/zero
        $decimalHours = (float) $decimalHours;

        if ($decimalHours <= 0) {
            return "0h00";
        }

        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);

        return $hours . "h" . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    }
    public function getCompteurTempsAttribute()
    {
        $diff = $this->estimated_hours - $this->actual_hours;

        if ($diff == 0)
            return "OK";

        $prefix = $diff > 0 ? "Gain : " : "Perte : ";
        return $prefix . $this->formatDuration(abs($diff));
    }

    public function currentBlocking()
    {
        return $this->reasons()->where('is_finish', false)->latest()->first();
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'equipe_subtask');
    }

    public function reasons()
    {
        return $this->morphMany(Reason::class, 'raisonable');
    }
}
