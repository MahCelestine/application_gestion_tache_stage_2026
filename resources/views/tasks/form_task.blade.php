<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div>
            <label>Client *</label>
            <div>
                <label>Ajouter un nouveau client</label>
                <input type="text" name="new_client_name" placeholder="Nom du client"  />
            </div>
            <div>
                <label>Client existant</label>
                <select name="client_id">
                    <option value="" selected>Choisir un client...</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label>Grande tâche *</label>
            <input placeholder="Intitulé de la grande tâche" name="label" type="text" required />
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
            <label>Delais *</label>
            <input type="date" name="due_date" required />
        </div>
        <div>
            <label>Temps donné *</label>
            <input type="number" name="estimated_hours" placeholder="Le nombre d'heure donnée pour cette tâche"
                required />
        </div>
        <div>
            <label>N° Devis</label>
            <input type="text" name="quote_number" placeholder="Le numéro de devis" />
        </div>
        <div>
            <label>Facturation</label>
            <input type="text" name="billing_info" placeholder="Le numéro de facturation" />
        </div>
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