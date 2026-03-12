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

    public function getCompteurTempsAttribute() 
    {
        $diff= $this->estimated_hours - $this->actual_hours;

        if($diff > 0) {
            return "Gain : {$diff} H";
        }
        elseif ($diff == 0) {
            return "OK";
        }
        else {
            return "Perte:" . abs($diff) . "H";
        }
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
}
