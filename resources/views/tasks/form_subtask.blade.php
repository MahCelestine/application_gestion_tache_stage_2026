<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <a href="{{ route('tasks.index') }}">vsevd</a>
    <form method="POST" action="{{ route('subtasks.store') }}">
        @csrf
        @php
            $isCCA = request('context') === 'cca';
        @endphp
        <input type="hidden" name="context" value="{{ $isCCA ? 'cca' : '' }}">
        <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
        <input type="hidden" name="task_id" value="{{ request('task_id') }}">
        <div>
            <label>Sous tâche *</label>
            <input type="text" name="label" placeholder="Intitulé" required />
        </div>
        <div>
            <label>Assignation (plusieurs choix possible)</label>
            <select id="select-equipes" name="equipe_ids[]" multiple>
                <option value="" disabled selected>Choisir un membre ...</option>
                @foreach ($equipes as $equipe)
                    <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Delai *</label>
            <input type="date" name="due_date" required />
        </div>
        <div>
            <label>Temps Donné *</label>
            <input type="number" name="estimated_h" placeholder="0" min="0" required /> <span>h</span>
            <input type="number" name="estimated_m" placeholder="0" min="0" max="59" required />
        </div>
        @if (!$isCCA)
            <div>
                <label>N° Devis</label>
                <input type="text" name="quote_number" placeholder="Le numéro de devis" />
            </div>
            <div>
                <label>Facturation</label>
                <input type="text" name="billing_info" placeholder="Le numéro de facturation" />
            </div>
        @endif
        <small>Veuillez remplir tout les champs avec *</small>
        <button type="submit">Valider</button>
    </form>

    <script>
        new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
            create: false,
        });
    </script>
</x-layout>