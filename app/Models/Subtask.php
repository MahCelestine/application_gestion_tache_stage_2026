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

    public function getCompteurTempsAttribute()
    {
        $diff = $this->estimated_hours - $this->actual_hours;

        if ($diff > 0) {
            return "Gain : {$diff} H";
        } elseif ($diff == 0) {
            return "OK";
        } else {
            return "Perte:" . abs($diff) . "H";
        }
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function equipes() 
    {
        return $this->belongsToMany(Equipe::class, 'equipe_subtask');
    }
}
