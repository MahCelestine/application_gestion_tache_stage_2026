<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAssignment extends Model
{
    protected $fillable = ['name', 'assigned_date'];

    public static function incrementTaskCountForToday(?string $name): void
    {
        $name = trim($name);

        if (empty($name)) {
            return;
        }

        $exists = self::where('name', $name)->exists();

        if (!$exists) {
            try {
                self::create([
                    'name' => $name,
                    'task_count' => 1
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                self::where('name', $name)->increment('task_count');
            }
        } else {
            self::where('name', $name)->increment('task_count');
        }
    }
}
