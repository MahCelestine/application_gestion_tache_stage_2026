<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <a href="{{ route('tasks.index') }}">ggvg</a>
    <form action="{{ route('subtasks.update', $subtask->id) }}" method="POST">
        @csrf
        @php
            $isCCA = request('context') === 'cca';
        @endphp
        <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
        @method('PUT')
        <div>
            <label>Tâche</label>
            <input type="text" name="label" value="{{ $subtask->label }}" required />
        </div>
        <div>
            <label>Assignation</label>
            <select id="select-equipes" name="equipe_ids[]" multiple>
                @foreach ($equipes as $equipe)
                    <option value="{{ $equipe->id }}" {{ $subtask->equipes->contains($equipe->id) ? 'selected' : '' }}>
                        {{ $equipe->prenom }} {{ $equipe->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Délai *</label>
            <input type="date" name="due_date" value="{{ $subtask->due_date }}" required />
        </div>
        <div>
            <label>Temps donné</label>
            @php
                $hours = floor($subtask->estimated_hours);
                $minutes = round(($subtask->estimated_hours - $hours) * 60);
            @endphp
            <input type="number" name="estimated_h" value="{{ $hours }}" min="0" required /> <span>h</span>
            <input type="number" name="estimated_m" value="{{ $minutes }}" min="0" max="59" required /> <span>min</span>
        </div>
        <div>
            <label>Temps réel cumulé : {{ $subtask->formatDuration($subtask->actual_hours) }} h</label>
            <label>Ajouter du temps</label>
            <input type="number" name="add_actual_h" value="" min="0"><span>h</span>
            <input type="number" name="add_actual_m" value="" min="0" max="59"><span>min</span>
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
        <div>
            <label>État de la tâche</label>
            <select name="status" id="status-select" onchange="toggleReasonField()" required>
                <option value="en cours" {{ $subtask->status == 'en cours' ? 'selected' : '' }}>En cours</option>
                <option value="bloqué" {{ $subtask->status == 'bloqué' ? 'selected' : '' }}>Bloqué</option>
                <option value="validé" {{ $subtask->status == 'validé' ? 'selected' : '' }}>Validé</option>
            </select>
        </div>
        <div id="reason-container" style="display: {{ $subtask->status == 'bloqué' ? 'block' : 'none' }}">
            <label>Raison du blocage *</label>
            <textarea name="reason_description" id="reason_description"
                placeholder="Expliquez le problème...">{{ $subtask->status == 'bloqué' && $subtask->currentBlocking() ? $subtask->currentBlocking()->description : '' }}</textarea>
        </div>

        <button type="submit">Enregistrer les modifications</button>
    </form>

    <form action="{{ route('subtasks.destroy', $subtask->id) }}" method="POST"
        id="delete-subtask-form-{{ $subtask->id }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="redirect_to" value="tasks.cca" />
        <button type="button" onclick="confirmDeleteSubtask({{ $subtask->id }})">Supprimer la
            sous-tâche</button>
    </form>

    <script>
        var select = new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
            maxItems: null,
        });

        function toggleReasonField() {
            const status = document.getElementById('status-select').value;
            const container = document.getElementById('reason-container');
            const textarea = document.getElementById('reason_description');

            if (status === 'bloqué') {
                container.style.display = 'block';
                textarea.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                textarea.removeAttribute('required');
            }
        }

        document.addEventListener('DOMContentLoaded', toggleReasonField);

        function confirmDeleteSubtask(id) {
            // Ajoute un console.log pour vérifier que la fonction est bien appelée
            console.log("Tentative de suppression de la sous-tâche ID:", id);

            if (confirm("Êtes-vous sûr de vouloir supprimer cette sous-tâche ?")) {
                const form = document.getElementById('delete-subtask-form-' + id);

                if (form) {
                    console.log("Formulaire trouvé, envoi en cours...");
                    form.submit();
                } else {
                    alert("Erreur : Formulaire introuvable 'delete-subtask-form'" + id + "'introuvable dans le DOM.");
                }
            }
        }
    </script>
</x-layout>