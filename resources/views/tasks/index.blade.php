<x-layout>
    <a href="{{ route('tasks.create') }}"> + Ajouter une grande tache</a>
    <div>
        <p>Filtrer par état :</p>
        @if ($filterStatus)
            <a href="{{ request()->fullUrlWithQuery(['filter_status' => null]) }}">Réinitialiser les filtres</a>
        @endif
        <a href="{{ request()->fullUrlWithQuery(['filter_status' => 'bloqué']) }}">Bloqués</a>
        <a href="{{ request()->fullUrlWithQuery(['filter_status' => 'en cours']) }}">En cours</a>
        <a href="{{ request()->fullUrlWithQuery(['filter_status' => 'validé']) }}">Validés</a>
    </div>
    <div>
        <form action="{{ route('tasks.index') }}" method="GET">
            @csrf
            @if($filterStatus) <input type="hidden" name="filter_status" value="{{ $filterStatus }}" />@endif
            @if($sortClient) <input type="hidden" name="sort_client" value="{{ $sortClient }}" />@endif
            @if($sortTask) <input type="hidden" name="sort_task" value="{{ $sortTask }}" />@endif

            <input type="text" name="search" value="{{ $search }}" placeholder="Client, tâche, sous-tâche ou une assignation..." />
            <button type="submit">Search</button>
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>
                    @php
                        $nextClientSort = match ($sortClient) {
                            'asc' => 'desc',
                            'desc' => '',
                            default => 'asc',
                        };

                        $arrowClient = match ($sortClient) {
                            'asc' => '(A-Z)',
                            'desc' => '(Z-A)',
                            default => '',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_client' => $nextClientSort]) }}">Client</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_client' => $nextClientSort]) }}">
                        {{ $arrowClient }}</a>
                </th>
                <th>
                    @php
                        $nextTaskSort = match ($sortTask) {
                            'asc' => 'desc',
                            'desc' => '',
                            default => 'asc',
                        };

                        $arrowTask = match ($sortTask) {
                            'asc' => '(A-Z)',
                            'desc' => '(Z-A)',
                            default => '',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_task' => $nextTaskSort]) }}">Grande tâche</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_task' => $nextTaskSort]) }}"> {{ $arrowTask }}</a>
                </th>
                <th>
                    @php
                        $nextSubtaskSort = match ($sortSubtask) {
                            'asc' => 'desc',
                            'desc' => '',
                            default => 'asc',
                        };

                        $arrowSubtask = match ($sortSubtask) {
                            'asc' => '(A-Z)',
                            'desc' => '(Z-A)',
                            default => '',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_subtask' => $nextSubtaskSort]) }}">Sous tâche</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_subtask' => $nextSubtaskSort]) }}">
                        {{ $arrowSubtask }}</a>
                </th>
                <th>Etat</th>
                <th>Assignation</th>
                <th>Delai</th>
                <th>Temps donné</th>
                <th>Temps réel</th>
                <th>Compteur temps</th>
                <th>Devis</th>
                <th>Facturation</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                @if ($task->status !== 'validé' || $filterStatus || $search)
                    <tr>
                        <td>{{ $task->client->nom }}</td>
                        <td>{{ $task->label }}</td>
                        <td></td>
                        <td class="cell-{{ Str::slug($task->status) }}">{{ $task->status }}
                            @if($task->status === 'bloqué' && $task->currentBlocking() && $task->subtasks->count() === 0)
                                <button type="button" onclick="toggleReason('reason-task-{{ $task->id }}')">↓</button>
                            @endif
                        </td>
                        <td>
                            @forelse ($task->equipes as $membre)
                                {{ $membre->prenom }} {{ $membre->nom }}
                            @empty
                                -
                            @endforelse
                        </td>
                        <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</td>
                        <td>{{ $task->estimated_hours }} H</td>
                        <td>{{ $task->actual_hours }} H</td>
                        <td>{{ $task->compteur_temps }}</td>
                        <td>{{ $task->quote_number ?? '-'}}</td>
                        <td>{{ $task->billing_info ?? '-'}}</td>
                        <td><a href="{{ route('tasks.edit', $task->id) }}">Modifier</a></td>
                    </tr>
                    @if($task->status === 'bloqué' && $blocking = $task->currentBlocking())
                        <tr id="reason-task-{{ $task->id }}" style="display: none;">
                            <td colspan="3"></td>
                            <td colspan="5">Raison du blocage : {{ $blocking->description }}</td>
                            <td>Signalé le {{ $blocking->created_at->format('d/m') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    @endif

                    @foreach ($task->subtasks as $subtask)
                        <tr>
                            <td></td>
                            <td><a href="{{ route('subtasks.edit', $subtask->id) }}">Modifier</a></td>
                            <td>{{ $subtask->label }}</td>
                            <td class="text-{{ Str::slug($subtask->status) }}">{{ $subtask->status }}
                                @if($subtask->status === 'bloqué')
                                    <button type="button" onclick="toggleReason('reason-sub-{{ $subtask->id }}')">↓</button>
                                @endif
                            </td>
                            <td>
                                @forelse ($subtask->equipes as $membre)
                                    {{ $membre->prenom }}{{ $membre->nom }}
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>{{ \Carbon\Carbon::parse($subtask->due_date)->format('d/m/Y') }}</td>
                            <td>{{ $subtask->estimated_hours }} H</td>
                            <td>{{ $subtask->actual_hours }} H</td>
                            <td>{{ $subtask->compteur_temps }}</td>
                            <td>{{ $subtask->quote_number ?? '-' }}</td>
                            <td>{{ $subtask->billing_info ?? '-' }}</td>
                        </tr>
                        @if($subtask->status === 'bloqué' && $blocking = $subtask->currentBlocking())
                            <tr id="reason-sub-{{ $subtask->id }}" style="display: none;">
                                <td colspan="3"></td>
                                <td colspan="5">Raison du blocage : {{ $blocking->description }}</td>
                                <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        @endif
                    @endforeach

                    <tr>
                        <td colspan="2"></td>
                        <td colspan="9"><a href="{{ route('subtasks.create', ['task_id' => $task->id]) }}">+ Ajouter une sous
                                tâche</a></td>
                    </tr>
                @endif
            @endforeach

        </tbody>
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

</x-layout>