<?php

namespace App\Models;

use App\Traits\HasContextLogic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackableTime;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use TrackableTime;
    use HasContextLogic;
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
        'due_date' => 'date',
    ];

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

    public static function createWithLogicAndSubtask(array $data, string $context = null)
    {
        return DB::transaction(function () use ($data, $context) {
            $clientId = Client::resolveId($data);

            $task = self::create([
                'client_id' => $clientId,
                'label' => $data['label'],
                'estimated_hours' => self::convertToHours($data['estimated_h'], $data['estimated_m']),
                'due_date' => $data['due_date'],
                'quote_number' => self::formatQuoteNumber($data['quote_number'] ?? null, $context),
                'billing_info' => self::formatBillingInfo($data['billing_info'] ?? null, $context),
                'status' => 'en cours',
            ]);

            Prospect::handleConversion($data['prospect_id'] ?? null);

            if (!empty($data['subtasks'])) {
                foreach ($data['subtasks'] as $subData) {
                    $task->subtasks()->create([
                        'label' => $subData['label'],
                        'due_date' => $subData['due_date'],
                        'estimated_hours' => self::convertToHours($subData['estimated_h'], $subData['estimated_m']),
                        'actual_hours' => 0,
                        'status' => 'en cours',
                        'quote_number' => $subData['quote_number'] ?? $task->quote_number,
                        'billing_info' => $subData['billing_info'] ?? null,
                    ]);
                }
                $task->subtasks()->first()->syncParentTask();
            }

            return $task;
        });
    }

    public function updateWithLogic(array $data, array $additionalData)
    {
        $hasSubtasks = $this->subtasks()->exists();

        if (!$hasSubtasks) {
            $newEstimated = self::convertToHours($data['estimated_h'], $data['estimated_m']);
            $hoursToAdd = self::convertToHours($additionalData['add_actual_h'] ?? 0, $additionalData['add_actual_m']);
            $newActual = $this->actual_hours + $hoursToAdd;

            if ($data['status'] == 'bloqué') {
                $this->reasons()->updateOrCreate(
                    ['is_finish' => false],
                    ['description' => $data['reason_description']]
                );
            } elseif ($this->status === 'bloqué') {
                $this->reasons()->where('is_finish', false)->update(['is_finish' => true]);
            }

            $this->status = $data['status'];

        } else {
            $newEstimated = $this->subtasks()->sum('estimated_hours');
            $newActual = $this->subtasks()->sum('actual_hours');
        }

        return $this->update([
            'label' => $data['label'],
            'estimated_hours' => $newEstimated,
            'actual_hours' => $newActual,
            'due_date' => $data['due_date'],
            'quote_number' => isset($data['quote_number']) ? $data['quote_number'] : null,
            'billing_info' => isset($data['billing_info']) ? $data['billing_info'] : null,
            'client_id' => $data['client_id'],
        ]);
    }

    public function scopeFiltersSearch(Builder $query, $request)
    {
        return $query->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($inner) use ($search) {
                $inner->where('tasks.label', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', fn($c) => $c->where('nom', 'LIKE', "%{$search}%"))
                    ->orWhereHas('equipes', fn($e) => $e->where('prenom', 'LIKE', "%{$search}%"))
                    ->orWhereHas('subtasks', function ($sq) use ($search) {
                        $sq->where('label', 'LIKE', "%{$search}%")
                            ->orWhereHas('equipes', fn($eq) => $eq->where('prenom', 'LIKE', "%{$search}%"));
                    });
            });
        })->when($request->filter_status, function ($q) use ($request) {
            $status = $request->filter_status;
            $q->where(function ($statusGroup) use ($status) {
                $statusGroup->where('tasks.status', $status)
                    ->orWhereHas('subtasks', fn($sq) => $sq->where('status', $status));
            });

        });
    }

    public function scopeFiltersStatus($query, $status)
    {
        return $query->when($status, function ($q) use ($status) {
            return $q->where('status', $status);
        });
    }

    public function scopeOrderBySort(Builder $query, $request)
    {
        if ($request->sort_client) {
            return $query->join('clients', 'tasks.client_id', '=', 'clients.id')
                ->select('tasks.*')
                ->orderBy('clients.nom', $request->sort_client);
        }

        if ($request->sort_task) {
            return $query->orderBy('label', $request->sort_task);
        }

        return $query->orderByRaw("FIELD(status, 'bloqué', 'attente BAT', 'en cours', 'validé')")
            ->orderBy('due_date', 'asc');
    }

    public function scopeClientCCA(Builder $query, bool $isCCA = false)
    {
        return $query->whereHas('client', function ($q) use ($isCCA) {
            $q->where('nom', $isCCA ? '=' : '!=', 'CCA');
        });
    }

    public function scopePriorityPaid($query)
    {
        return $query->orderByRaw("CASE
        WHEN billing_info IS NULL THEN 1
        WHEN is_paid = 0 THEN 2
        ELSE 3
        END")->orderBy('due_date', 'asc');
    }

    public function scopeFiltersPaid($query, $filter)
    {
        return $query->when($filter, function ($q) use ($filter) {
            $q->where(function ($subQuery) use ($filter) {
                if ($filter === 'a_facturer') {
                    $subQuery->where(function ($taskQ) {
                        $taskQ->where('status', 'validé')
                            ->whereNull('billing_info');
                    })->orWhereHas('subtasks', function ($subtaskQ) {
                        $subtaskQ->where('status', 'validé')
                            ->whereNull('billing_info');
                    });
                } elseif ($filter === 'non_paye') {
                    $subQuery->where(function ($taskQ) {
                        $taskQ->whereNotNull('billing_info')
                            ->where('is_paid', false);
                    })->orWhereHas('subtasks', function ($subtaskQ) {
                        $subtaskQ->whereNotNull('billing_info')
                            ->where('is_paid', false);
                    });
                } elseif ($filter === 'paye') {
                    $subQuery->where('is_paid', true)
                        ->orWhereHas('subtasks', fn($sq) => $sq->where('is_paid', true));
                }
            });
        });
    }

    public function scopeArchived($query)
    {
        return $query->where(function ($q) {
            $q->where('is_paid', true)
                ->orWhereHas('subtasks', fn($sq) => $sq->where('is_paid', true));
        });
    }
}
