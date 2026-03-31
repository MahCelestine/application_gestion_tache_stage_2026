<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'label',
        'status',
        'due_date',
        'quote_number',
        'billing_info',
        'estimated_hours',
        'actual_hours',
        'is_paid'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
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

    protected static function booted()
    {
        static::creating(function ($task) {
            if (request()->routeIs('cca.*')) {
                $task->is_paid = true;
            }
        });
    }

    public function currentBlocking()
    {
        return $this->reasons()->where('is_finish', false)->latest()->first();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function equipes()
    {
        return $this->belongsToMany(Equipe::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    public function reasons()
    {
        return $this->morphMany(Reason::class, 'raisonable');
    }
}
