<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAssignment extends Model
{
    protected $fillable = ['name', 'task_count'];

    protected $casts = [
        'created_tasks' => 'array',
        'created_subtasks' => 'array',
        'updated_tasks' => 'array',
        'updated_subtasks' => 'array',
    ];

    public static function incrementTaskCountForToday(?string $name, int $id, string $type, string $action): void
    {
        $name = trim($name);

        if (empty($name)) {
            return;
        }

        $assignment = self::firstOrNew(['name' => $name]);

        if (!$assignment->exists) {
            $assignment->task_count = 0;
            $assignment->created_tasks = [];
            $assignment->created_subtasks = [];
            $assignment->updated_tasks = [];
            $assignment->updated_subtasks = [];
        }

        $assignment->task_count += 1;

        $column = $action . '_' . ($type === 'task' ? 'tasks' : 'subtasks');

        $currentIds = $assignment->$column ?? [];
        if (!in_array($id, $currentIds)) {
            $currentIds[] = $id;
        }
        $assignment->$column = $currentIds;

        $assignment->save();
    }
}
