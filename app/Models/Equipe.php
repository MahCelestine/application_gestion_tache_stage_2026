<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    /** @use HasFactory<\Database\Factories\EquipeFactory> */
    use HasFactory;

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function subtacks()
    {
        return $this->belongsToMany(Subtask::class, 'equipe_subtask');
    }
}
