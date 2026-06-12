<?php

namespace App\Models;

use App\Traits\HasContextLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackableTime;

class Subtask extends Model
{
    /** @use HasFactory<\Database\Factories\SubtaskFactory> */
    use HasFactory;
    use TrackableTime;
    use HasContextLogic;

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
    public $importantFieldsWereChanged = false;

    protected $casts = [
        'is_paid' => 'boolean',
        'due_date' => 'date',
    ];

    public function currentBlocking()
    {
        return $this->reasons
            ->where('is_finish', false)
            ->sortByDesc('created_at')
            ->first();
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

    public function syncParentTask()
    {
        $parent = $this->task;
        if (!$parent)
            return;

        $sumEstimated = $parent->subtasks()->sum('estimated_hours');
        $parent->actual_hours = $parent->subtasks()->sum('actual_hours');

        if ($sumEstimated > 0) {
            $parent->estimated_hours = $sumEstimated;
        }

        $maxSubtaskDate = $parent->subtasks()->max('due_date');
        if ($maxSubtaskDate > $parent->due_date) {
            $parent->due_date = $maxSubtaskDate;
        }

        $subtasks = $parent->subtasks;
        // Priorité: bloqué > attente BAT > en cours > validé
        if ($subtasks->where('status', 'bloqué')->count() > 0) {
            $parent->status = 'bloqué';
        } elseif ($subtasks->where('status', 'attente BAT')->count() > 0) {
            $parent->status = 'attente BAT';
        } elseif ($subtasks->where('status', 'BAT ok')->count() > 0) {
            $parent->status = 'BAT ok';
        } elseif ($subtasks->where('status', '!=', 'validé')->count() === 0) {
            $parent->status = 'validé';
        } else {
            $parent->status = 'en cours';
        }

        $parent->save();
    }

    public static function createWithLogic(array $data, bool $context = null)
    {

        $subtask = self::create([
            'task_id' => $data['task_id'],
            'label' => $data['label'],
            'due_date' => $data['due_date'],
            'estimated_hours' => self::convertToHours($data['estimated_h'], $data['estimated_m']),
            'actual_hours' => 0,
            'status' => 'en cours',
            'quote_number' => self::formatQuoteNumber($data['quote_number'] ?? null, $context),
            'billing_info' => self::formatBillingInfo($data['billing_info'] ?? null, $context),
        ]);

        $subtask->syncParentTask();

        return $subtask;
    }

    public function updateLogic(array $data, array $timeInputs, $isCCA = false)
    {
        $statusPassedToBatOk = ($data['status'] === 'BAT ok' && $this->status !== 'BAT ok');

        $newEstimated = self::convertToHours($timeInputs['estimated_h'], $timeInputs['estimated_m']);
        $decimalToAdd = self::convertToHours($timeInputs['add_actual_h'], $timeInputs['add_actual_m']);
        $decimalToReduce = self::convertToHours($timeInputs['reduce_actual_h'], $timeInputs['reduce_actual_m']);
        $newActualTotal = max(0, $this->actual_hours + $decimalToAdd - $decimalToReduce);

        $this->fill([
            'label' => $data['label'],
            'estimated_hours' => $newEstimated,
            'actual_hours' => $newActualTotal,
            'due_date' => $data['due_date'],
            'quote_number' => $isCCA ? 'INTERNE' : ($data['quote_number'] ?? null),
            'billing_info' => $isCCA ? 'OFFERT' : ($data['billing_info'] ?? null),
        ]);

        $importantFieldChanged = $this->isDirty(['label', 'estimated_hours', 'actual_hours', 'due_date', 'quote_number', 'billing_info']);
        $this->importantFieldsWereChanged = ($importantFieldChanged || $statusPassedToBatOk);

        if ($data['status'] == 'bloqué') {
            $this->reasons()->updateOrCreate(
                ['is_finish' => false],
                ['description' => $data['reason_description']]
            );
        } else {
            $this->reasons()->where('is_finish', false)->update(['is_finish' => true]);
        }

        $this->status = $data['status'];

        $this->save();

        $this->syncParentTask();

        return $this;
    }

    public function scopeFiltersSortSub($query, $search, $status, $sortOrder)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where('label', 'LIKE', "%{$search}%");
        })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })

            ->orderByRaw("FIELD(status, 'bloqué', 'attente BAT', 'BAT ok', 'en cours', 'validé') ASC")
            ->orderBy('due_date', 'asc')
            ->orderBy('label', $sortOrder ?: 'asc');
    }

    public function scopeFiltersPaidSub($query, $search, $filterPayment, $sortOrder)
    {
        return $query->where('status', 'validé')
            ->when($search, fn($q) => $q->where('label', 'LIKE', "%{$search}%"))
            ->when($filterPayment, function ($q) use ($filterPayment) {
                if ($filterPayment === 'paye')
                    $q->where('is_paid', true);
                elseif ($filterPayment === 'non_paye')
                    $q->where('is_paid', false)->whereNotNull('billing_info');
                elseif ($filterPayment === 'a_facturer')
                    $q->whereNull('billing_info');
            })
            ->when(
                $sortOrder,
                fn($q) => $q->orderBy('label', $sortOrder),
                fn($q) => $q->orderByRaw("CASE WHEN billing_info IS NULL THEN 1 WHEN is_paid = 0 THEN 2 ELSE 3 END")->orderBy('due_date', 'asc')
            );
    }

    public function scopeFiltersArchive($query, $search, $sortOrder)
    {
        return $query->where('is_paid', true)
            ->when($search, function ($q) use ($search) {
                $q->where('label', 'LIKE', "%{$search}%");
            })
            ->when(
                $sortOrder,
                fn($q) => $q->orderBy('label', $sortOrder),
                fn($q) => $q->orderBy('updated_at', 'desc')
            );
    }
}
