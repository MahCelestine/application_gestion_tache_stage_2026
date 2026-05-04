<div>
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg z-10 bg-white">
            <tr>
                <th
                    class="py-3 rounded-tl-3xl border-t-2 border-b-2 border-l-2 border-gray-300 w-[8%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Client')">Client
                        {{$sortClient === 'asc' ? '(A-Z)' : ($sortClient === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[11%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Task')">Grande tâches
                        {{$sortTask === 'asc' ? '(A-Z)' : ($sortTask === 'desc' ? '(Z-A)' : '') }}</button>

                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[17%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Subtask')">Sous-tâches
                        {{$sortSubtask === 'asc' ? '(A-Z)' : ($sortSubtask === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">État</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Assignation</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">Délai</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">Quota</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">Temps réel</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">Compteur</th>
                @if(!$isCca)
                    <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">Devis</th>
                    <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">Facturation</th>
                @endif
                <th
                    class="py-3 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                </th>
            </tr>
        </thead>
        @foreach ($tasks as $task)
            @if (!($task->status === 'validé' && $filterStatus === 'validé'))
                @if ($task->status !== 'validé' || $filterStatus || $search)
                            <tbody class="task-group-border shadow-md ml-2">
                                <tr>
                                    @if(!$isCca)
                                        <td colspan="12"></td>
                                    @else
                                        <td colspan="10"></td>
                                    @endif

                                </tr>
                                <tr>
                                    <td class="border-r-[15px] border-r-transparent">{{ $task->client->nom }}</td>
                                    <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600" colspan="2">
                                        {{ $task->label }}
                                    </td>
                                    <td class="text-center">
                                        <span class="{{ $task->status_cell_class }}">{{ $task->status }}
                                            @if($task->status === 'bloqué' && $task->currentBlocking() && $task->subtasks->count() === 0)
                                                <button type="button" onclick="toggleReason('reason-task-{{ $task->id }}')">↓</button>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="border-r-[15px] border-r-transparent">
                                        @forelse ($task->equipes as $membre)
                                            {{ $membre->prenom }}
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td style="{{ $task->is_urgent ? 'color: #dc3545; font-weight: bold; ' : '' }}" class="text-center">
                                        {{ $task->due_date->format('d/m/Y') }}
                                    </td>
                                    <td class="text-center">
                                        {{ $task->display_estimated }}
                                    </td>
                                    <td class="text-center">
                                        {{ $task->display_actual }}
                                    </td>
                                    <td class="text-center">{{ $task->compteur_temps }}</td>
                                    @if(!$isCca)
                                        <td class="text-center">{{ $task->quote_number ?? '-'}}</td>
                                        <td class="text-center">{{ $task->billing_info ?? '-'}}</td>
                                    @endif
                                    <td><a href="{{ route('tasks.edit', [$task->id, 'context' => $isCca ? 'cca' : 'default']) }}"
                                            class="text-blue-500 hover:text-blue-600 hover:font-semibold border rounded-2xl py-1 px-2 border-blue-500">Modifier</a>
                                    </td>
                                </tr>
                                @if($task->status === 'bloqué' && $blocking = $task->currentBlocking())
                                    <tr id="reason-task-{{ $task->id }}" style="display: none;" wire:ignore>
                                        @if(!$isCca)
                                            <td colspan="2"></td>
                                            <td colspan="6">Raison du blocage : {{ $blocking->description }}</td>
                                            <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                                            <td colspan="2"></td>
                                        @else
                                            <td colspan="2"></td>
                                            <td colspan="5">Raison du blocage : {{ $blocking->description }}</td>
                                            <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                                            <td colspan="1"></td>
                                        @endif
                                    </tr>
                                @endif
                                <tr>
                                    @if (!$isCca)
                                        <td></td>
                                        <td colspan="10" class="py-1">
                                            <div class="border-b-2 border-gray-300 w-full"></div>
                                        </td>
                                        <td></td>
                                    @else
                                        <td></td>
                                        <td colspan="8" class="py-1">
                                            <div class="border-b-2 border-gray-300 w-full"></div>
                                        </td>
                                        <td></td>
                                    @endif

                                </tr>
                                @foreach ($task->subtasks as $subtask)
                                                <tr>
                                                    <td></td>
                                                    <td><a href="{{ route('subtasks.edit', [$subtask->id, 'context' => $isCca ? 'cca' : 'default']) }}"
                                                            class="text-blue-500 hover:text-blue-600 hover:font-semibold border rounded-2xl py-1 px-2 border-blue-200">Modifier</a>
                                                    </td>
                                                    <td>{{ $subtask->label }}</td>
                                                    <td class="{{ $subtask->status_text_class }} text-center">{{ $subtask->status }}
                                                        @if($subtask->status === 'bloqué')
                                                            <button type="button" onclick="toggleReason('reason-sub-{{ $subtask->id }}')">↓</button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @forelse ($subtask->equipes as $membre)
                                                            {{ $membre->prenom }}
                                                        @empty
                                                            -
                                                        @endforelse
                                                    </td>
                                                    <td style="{{ $subtask->is_urgent ? 'color: #dc3545; font-weight: bold; ' : '' }}" class="text-center">
                                                        {{ $subtask->due_date->format('d/m/Y') }}
                                                    </td>
                                                    <td class="text-center">{{ $subtask->display_estimated }}</td>
                                                    <td class="text-center">{{ $subtask->display_actual }}</td>
                                                    <td class="text-center">{{ $subtask->compteur_temps }}</td>
                                                    @if(!$isCca)
                                                        <td class="text-center">{{ $subtask->quote_number ?? '-' }}</td>
                                                        <td class="text-center">{{ $subtask->billing_info ?? '-' }}</td>
                                                    @endif
                                                </tr>
                                                @if($subtask->status === 'bloqué' && $blocking = $subtask->currentBlocking())
                                                    <tr id="reason-sub-{{ $subtask->id }}" style="display: none;" wire:ignore>
                                                        <td colspan="2"></td>
                                                        <td colspan="6">Raison du blocage : {{ $blocking->description }}</td>
                                                        <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                @endif
                                    </div>
                                @endforeach
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="9" class="padding_ajout"><a
                                href="{{ route('subtasks.create', ['task_id' => $task->id, 'context' => $isCca ? 'cca' : 'default']) }}"
                                class="text-blue-500 hover:text-blue-600 hover:font-semibold">+ Ajouter une sous-tâche</a></td>
                    </tr>
                    </tbody>
                @endif
            @endif
        @endforeach
</table>

<script>
    function toggleReason(id) {
        const row = document.getElementById(id);
        if (row.style.display === "none") {
            row.style.display = "table-row";
        } else {
            row.style.display = "none";
        }
    }
</script>
</div>