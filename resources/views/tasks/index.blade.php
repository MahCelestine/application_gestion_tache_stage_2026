<x-layout>
    <a href="{{ route('tasks.create') }}"> + Ajouter une grande tache</a>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Grande tâche</th>
                <th>Sous tâche</th>
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
                @if ($task->status !== 'validé')
                    <tr>
                        <td>{{ $task->client->nom }}</td>
                        <td>{{ $task->label }}</td>
                        <td></td>
                        <td>{{ $task->status }}
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
                        <td>{{ $task->due_date }}</td>
                        <td>{{ $task->estimated_hours }}</td>
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
                            <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    @endif

                    @foreach ($task->subtasks as $subtask)
                        <tr>
                            <td></td>
                            <td><a href="{{ route('subtasks.edit', $subtask->id) }}">Modifier</a></td>
                            <td>{{ $subtask->label }}</td>
                            <td>{{ $subtask->status }}
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
                            <td>{{ $subtask->due_date }}</td>
                            <td>{{ $subtask->estimated_hours }}</td>
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