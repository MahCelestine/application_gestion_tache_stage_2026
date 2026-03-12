<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div>
            <label>Client *</label>
            <div>
                <label>Ajouter un nouveau client</label>
                <input type="text" name="new_client_name" placeholder="Nom du client" />
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
            <label>Delai *</label>
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
        <div id="subtasks-container"></div>

        <button type="button" id="add-subtask-btn" style="margin: 10px 0;">
            + Ajouter une sous-tâche
        </button>
        <template id="subtask-template">
            <div class="subtask-row">
            <div>
                <label>Sous tâche *</label>
                <input type="text" name="subtasks[INDEX][label]" placeholder="Intitulé" required />
            </div>
            <div>
                <label>Delai *</label>
                <input type="date" name="subtasks[INDEX][due_date]" required />
            </div>
            <div>
                <label>Temps donné *</label>
                <input type="number" name="subtasks[INDEX][estimated_hours]" placeholder="Le nombre d'heures"
                    required />
            </div>
            <div>
                <label>N° Devis</label>
                <input type="text" name="subtasks[INDEX][quote_number]" placeholder="N° Devis" />
            </div>
            <div>
                <label>Facturation</label>
                <input type="text" name="subtasks[INDEX][billing_info]" placeholder="Facturation" />
            </div>
            <label>Assignation :</label>
            <select name="subtasks[INDEX][equipe_ids][]" multiple class="select-equipes-dynamic">
                @foreach ($equipes as $equipe)
                    <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}</option>
                @endforeach
            </select>
            <button type="button" onclick="this.closest('.subtask-row').remove()">Supprimer</button>
            </div>
        </template>
        <small>Veuillez remplir tout les champs avec *</small>
        <button type="submit">Valider</button>
    </form>

    <script>
        new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
            create: false,
        });

        let subtaskIndex = 0;
        const container = document.getElementById('subtasks-container');
        const template = document.getElementById('subtask-template');
        const btn = document.getElementById('add-subtask-btn');

        btn.addEventListener('click' , function () {
            const clone = template.content.cloneNode(true) ;

            const id = subtaskIndex++;
            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', id);
            });

            container.appendChild(clone);

            const newSelect = container.lastElementChild.querySelector('.select-equipes-dynamic');
            if (newSelect) {
                new TomSelect(newSelect, {
                plugins: ['remove_button'],
                create: false
            });
            }
        });
    </script>
</x-layout>