<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label>Client</label>
            <input type="hidden" name="client_id" value="{{ $task->client_id }}">za
            <h1>{{ $task->client->nom }}</h1>
        </div>
        <div>
            <label>Grande Tâche *</label>
            <input type="text" name="label" value="{{ $task->label }}" required />
        </div>
        <div>
            <label>Assignation</label>
            <select id="select-equipes" name="equipe_ids[]" multiple>
                @foreach ($equipes as $equipe)
                    <option value="{{ $equipe->id }}" {{ $task->equipes->contains($equipe->id) ? 'selected' : '' }}>
                        {{ $equipe->prenom }} {{ $equipe->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Délai *</label>
            <input type="date" name="due_date" value="{{ $task->due_date }}" required />
        </div>
        <div>
            <label>Temps donné *</label>
            <input type="number" name="estimated_hours" value="{{ $task->estimated_hours }}" required />
        </div>
        <div>
            <label>Temps réel cumulé : {{ $task->actual_hours }} H</label>
            <label>Ajouter du temps</label>
            <input type="number" name="hours_to_add" value="0" step="0.5" />
        </div>
        <div>
            <label>N° Devis</label>
            <input type="text" name="quote_number" value="{{ $task->quote_number }}" />
        </div>
        <div>
            <label>Facturation</label>
            <input type="text" name="billing_info" value="{{ $task->billing_info }}" />
        </div>
        <button type="submit">Enregistrer les modifications</button>
    </form>

    <script>
        new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
        });
    </script>
</x-layout>