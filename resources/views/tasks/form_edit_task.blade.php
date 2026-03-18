<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <a href="{{ route('tasks.index') }}"><</a>
            <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <label>Client</label>
                    <input type="hidden" name="client_id" value="{{ $task->client_id }}">
                    <h2>{{ $task->client->nom }}</h2>
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
                    @if ($task->subtasks->count() === 0)
                        <input type="date" name="due_date" value="{{ $task->due_date }}" required />
                    @else
                        <input type="hidden" name="due_date" value="{{ $task->due_date }}" required />
                        <p>Le delai est géré via les sous-tâches, il prend automatiquement le delai le plus tard.</p>
                    @endif
                </div>
                <div>
                    <label>Temps donné</label>
                    <input type="hidden" name="estimated_hours" value="{{ $task->estimated_hours }}" />
                    <p>{{ $task->estimated_hours }} H</p>
                </div>
                <div>
                    <label>Temps réel cumulé : {{ $task->actual_hours }} H</label>
                    @if ($task->subtasks->count() === 0)
                        <label>Ajouter du temps</label>
                        <input type="number" name="hours_to_add" value="0" step="0.5" />
                    @else
                        <p>Le temps est géré via les sous-tâches.</p>
                    @endif
                </div>
                <div>
                    <label>N° Devis</label>
                    <input type="text" name="quote_number" value="{{ $task->quote_number }}" />
                </div>
                <div>
                    <label>Facturation</label>
                    <input type="text" name="billing_info" value="{{ $task->billing_info }}" />
                </div>
                @if ($task->subtasks->count() === 0)
                    <div>
                        <label>État de la tâche</label>
                        <select name="status" id="status-select" onchange="toggleReasonField()" required>
                            <option value="en cours" {{ $task->status == 'en cours' ? 'selected' : '' }}>En cours</option>
                            <option value="bloqué" {{ $task->status == 'bloqué' ? 'selected' : '' }}>Bloqué</option>
                            <option value="validé" {{ $task->status == 'validé' ? 'selected' : '' }}>Validé</option>
                        </select>
                    </div>
                    <div id="reason-container" style="display: {{ $task->status == 'bloqué' ? 'block' : 'none' }}">
                        <label>Raison du blocage</label>
                        <textarea name="reason_description" id="reason_description"
                            placeholder="Expliquez le problème...">{{ $task->currentBlocking() ? $task->currentBlocking()->description : '' }}</textarea>
                    </div>
                @else
                    <input type="hidden" name="status" value="{{ $task->status }}" />
                    <div>
                        <label>État de la tâche</label>
                        <div class="status-display">
                            <span class="badge-status {{ $task->status }}">
                                {{ ucfirst($task->status) }}
                            </span>
                            <p>
                                L'état est calculé automatiquement selon l'avancement des sous-tâches.
                            </p>
                        </div>
                    </div>
                @endif
                @if ($errors->any())
                    <div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button type="submit">Enregistrer les modifications</button>
            </form>

            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" id="delete-form-{{ $task->id }}">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete({{ $task->id }})">Supprimer la tâche</button>
            </form>

            <script>
                new TomSelect("#select-equipes", {
                    plugins: ['remove_button'],
                });

                function toggleReasonField() {
                    const status = document.getElementById('status-select').value;
                    const container = document.getElementById('reason-container');
                    const textarea = document.getElementById('reason_description');

                    const hasSubtasks = {{ $task->subtasks->count() > 0 ? 'true' : 'false' }}

                    if (status === 'bloqué') {
                        container.style.display = 'block';
                        if (!hasSubtasks) {
                            textarea.setAttribute('required', 'required');
                        } else {
                            textarea.removeAttribute('required');
                        }

                    } else {
                        container.style.display = 'none';
                        textarea.removeAttribute('required');
                    }
                }

                document.addEventListener('DOMContentLoaded', toggleReasonField);

                function confirmDelete(taskId) {
                    if (confirm("Êtes-vous sûr de vouloir supprimer cette tâche ? Cela suprimera également toutes les sous-tâches associées.")) {
                        document.getElementById('delete-form-' + taskId).submit();
                    }
                }
            </script>
</x-layout>