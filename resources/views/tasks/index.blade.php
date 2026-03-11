<x-layout>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Grande tâche</th>
                <th>Sous tâche</th>
                <th>Etat</th>
                <th>Assignation</th>
                <th>Delais</th>
                <th>Temps donné</th>
                <th>Temps réel</th>
                <th>Compteur temps</th>
                <th>Devis</th>
                <th>Facturation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            @foreach ($tasks as $task)
                <td>{{ $task->client->nom }}</td>
                <td>{{ $task->label }}</td>
                <td></td>
                <td>{{ $task->status }}</td>
                <td></td>
                <td>{{ $task->due_date }}</td>
                <td>{{ $task->estimated_hours }}</td>
                <td>{{ $task->actual_hours }}</td>
                <td>{{ $task->compteur_temps }}</td>
                <td>{{ $task->quote_number }}</td>
                <td>{{ $task->billing_info }}</td>
            @endforeach
            </tr>
        </tbody>
    </table>

</x-layout>