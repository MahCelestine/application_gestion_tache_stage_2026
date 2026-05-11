<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Equipe extends Model
{
    /** @use HasFactory<\Database\Factories\EquipeFactory> */
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'color',
    ];

    protected function complet(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->prenom} {$this->nom}",
        );
    }

    public function getFilamentName(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function subtasks()
    {
        return $this->belongsToMany(Subtask::class, 'equipe_subtask');
    }
}
