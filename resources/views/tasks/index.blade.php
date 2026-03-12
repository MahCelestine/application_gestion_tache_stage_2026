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
                <tr>
                    <td>{{ $task->client->nom }}</td>
                    <td>{{ $task->label }}</td>
                    <td></td>
                    <td>{{ $task->status }}</td>
                    <td>
                        @forelse ($task->equipes as $membre)
                            {{ $membre->prenom }} {{ $membre->nom }}
                        @empty
                            -
                        @endforelse
                    </td>
                    <td>{{ $task->due_date }}</td>
                    <td>{{ $task->estimated_hours }}</td>
                    <td>{{ $task->actual_hours }}</td>
                    <td>{{ $task->compteur_temps }}</td>
                    <td>{{ $task->quote_number ?? '-'}}</td>
                    <td>{{ $task->billing_info ?? '-'}}</td>
                    <td><a href="{{ route('tasks.edit', $task->id) }}">Modifier</a></td>
                </tr>

                @foreach ($task->subtasks as $subtask)
                    <tr>
                        <td colspan="2"></td>
                        <td>{{ $subtask->label }}</td>
                        <td>{{ $subtask->status }}</td>
                        <td>
                            @forelse ($subtask->equipes as $membre)
                                {{ $membre->prenom }}{{ $membre->nom }}
                            @empty
                                -
                            @endforelse
                        </td>
                        <td>{{ $subtask->due_date }}</td>
                        <td>{{ $subtask->estimated_hours }}</td>
                        <td>{{ $subtask->actual_hours }}</td>
                        <td>{{ $subtask->compteur_temps }}</td>
                        <td>{{ $subtask->quote_number ?? '-' }}</td>
                        <td>{{ $subtask->billing_info ?? '-' }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="2"></td>
                    <td colspan="9"><a href="{{ route('subtasks.create', ['task_id' => $task->id]) }}">+ Ajouter une sous tâche</a></td>
                </tr>
            @endforeach

        </tbody>
    </table>

</x-layout>