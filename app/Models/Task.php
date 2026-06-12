<?php

namespace App\Models;

use App\Traits\HasContextLogic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackableTime;
use Illuminate\Support\Facades\DB;
use App\Models\DailyAssignment;

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
        'is_paid',
        'evoliz_quote_id',
        'evoliz_item_id',
    ];

    public $importantFieldsWereChanged = false;

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
                'evoliz_quote_id' => $data['evoliz_quote_id'] ?? null,
                'evoliz_item_id' => $data['evoliz_item_id'] ?? null,
            ]);

            Prospect::handleConversion($data['prospect_id'] ?? null);

            if (!empty($data['subtasks'])) {
                foreach ($data['subtasks'] as $subData) {
                    $subtask = $task->subtasks()->create([
                        'label' => $subData['label'],
                        'due_date' => $subData['due_date'],
                        'estimated_hours' => self::convertToHours($subData['estimated_h'], $subData['estimated_m']),
                        'actual_hours' => 0,
                        'status' => 'en cours',
                        'quote_number' => $subData['quote_number'] ?? $task->quote_number,
                        'billing_info' => $subData['billing_info'] ?? null,
                    ]);

                    if (!empty($subData['equipe_ids'])) {
                        $subtask->equipes()->sync($subData['equipe_ids']);

                        $prenoms = \DB::table('equipes')
                            ->whereIn('id', $subData['equipe_ids'])
                            ->pluck('prenom');

                        foreach ($prenoms as $prenom) {
                            DailyAssignment::incrementTaskCountForToday((string) $prenom, $subtask->id, 'subtask', 'created');
                        }
                    }
                }
                $task->subtasks()->first()->syncParentTask();
            }

            return $task;
        });
    }

    public function updateWithLogic(array $data, array $additionalData = [])
    {
        // FIX SÉCURITÉ : Si 'status' n'est pas fourni dans $data, on prend la valeur actuelle
        // Cela évite l'erreur "Undefined array key" si le formulaire ne l'envoie pas.
        $targetStatus = $data['status'] ?? $this->status;

        // Ta ligne d'origine, maintenant sécurisée avec $targetStatus
        $statusPassedToBatOk = ($targetStatus === 'BAT ok' && $this->status !== 'BAT ok');

        $this->fill([
            'label' => $data['label'],
            'due_date' => $data['due_date'],
            'quote_number' => $data['quote_number'] ?? null,
            'billing_info' => $data['billing_info'] ?? null,
            'client_id' => $data['client_id'],
        ]);

        $importantFieldChanged = $this->isDirty(['label', 'due_date', 'quote_number', 'billing_info', 'client_id']);

        $this->importantFieldsWereChanged = ($importantFieldChanged || $statusPassedToBatOk);

        $hasSubtasks = $this->subtasks()->exists();

        if (!$hasSubtasks) {
            $newEstimated = self::convertToHours($data['estimated_h'], $data['estimated_m']);
            $decimalToAdd = self::convertToHours($data['add_actual_h'], $data['add_actual_m']);
            $decimalToReduce = self::convertToHours($data['reduce_actual_h'], $data['reduce_actual_m']);

            $newActual = max(0, $this->actual_hours + $decimalToAdd - $decimalToReduce);

            // Utilisation de la variable sécurisée ici aussi
            if ($targetStatus == 'bloqué') {
                $this->reasons()->updateOrCreate(
                    ['is_finish' => false],
                    ['description' => $data['reason_description'] ?? '']
                );
            } elseif ($this->status === 'bloqué') {
                $this->reasons()->where('is_finish', false)->update(['is_finish' => true]);
            }

            // Ta ligne d'origine qui applique le statut
            $this->status = $targetStatus;

        } else {
            if (($this->subtasks()->sum('estimated_hours')) > 0) {
                $newEstimated = $this->subtasks()->sum('estimated_hours');
            } else {
                $newEstimated = isset($data['estimated_h']) ? self::convertToHours($data['estimated_h'], $data['estimated_m']) : $this->estimated_hours;
            }
            $newActual = $this->subtasks()->sum('actual_hours');
        }

        $this->estimated_hours = $newEstimated;
        $this->actual_hours = $newActual;

        // Sauvegarde globale de l'instance
        $saved = $this->save();

        // Enregistrement dans le résumé journalier si un champ critique a bougé
        if ($saved && $this->importantFieldsWereChanged) {
            foreach ($this->equipes as $equipe) {
                DailyAssignment::incrementTaskCountForToday(
                    $equipe->prenom,
                    $this->id,
                    'task',
                    'updated'
                );
            }
        }

        return $saved;
    }
    public function duplicateWithSubtasks()
    {
        return DB::transaction(function () {
            $newTask = $this->replicate();
            $newTask->due_date = $this->due_date ? $this->due_date->copy()->addMonth() : null;
            $newTask->status = 'en cours';
            $newTask->actual_hours = 0;
            $newTask->quote_number = "??";
            $newTask->save();

            foreach ($this->subtasks as $subtask) {
                $newSubtask = $subtask->replicate();
                $newSubtask->task_id = $newTask->id;
                $newSubtask->due_date = $subtask->due_date ? $subtask->due_date->copy()->addMonth() : null;
                $newSubtask->status = 'en cours';
                $newSubtask->actual_hours = 0;
                $newSubtask->quote_number = "??";
                $newSubtask->save();
            }

            return $newTask;
        });
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
        })->when(request()->routeIs('tasks.index') || request()->routeIs('tasks.index_cca'), function ($q) {
            $q->where('tasks.status', '!=', 'validé');
        })
            ->when($request->filter_status, function ($q) use ($request) {
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

        return $query->orderByRaw("FIELD(status, 'bloqué', 'attente BAT', 'BAT ok', 'en cours', 'validé')")
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
