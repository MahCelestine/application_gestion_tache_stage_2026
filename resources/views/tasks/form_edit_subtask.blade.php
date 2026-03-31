<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <form action="{{ route('subtasks.update', $subtask->id) }}" method="POST">
        @csrf
        @php
            $isCCA = request('context') === 'cca';
        @endphp
        <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
        @method('PUT')
        <div class="border-2 border-gray-300 rounded-xl shadow-md mb-8">
            <h2 class="text-2xl mx-6 my-6">Modifier une sous-tâche</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="flex my-6">
                <div class="basis-2/4 mx-5">
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Sous-tâche</label>
                        <input type="text" name="label" value="{{ $subtask->label }}" required
                            class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xl font-semibold">Assignation (plusieurs choix possible)</label>
                        <select id="select-equipes" name="equipe_ids[]" multiple
                            class="w-[90%] text-lg mt-4 text-gray-600 font-mono">
                            @foreach ($equipes as $equipe)
                                <option value="{{ $equipe->id }}" {{ $subtask->equipes->contains($equipe->id) ? 'selected' : '' }}>
                                    {{ $equipe->prenom }} {{ $equipe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="basis-1/4 mx-5">
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Délai *</label>
                        <input type="date" name="due_date" value="{{ $subtask->due_date }}" required
                            class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xl font-semibold">Temps donné *</label>
                        @php
                            $hours = floor($subtask->estimated_hours);
                            $minutes = round(($subtask->estimated_hours - $hours) * 60);
                        @endphp
                        <div>
                            <input type="number" name="estimated_h" value="{{ $hours }}" min="0" required
                                class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            <span>h</span>
                            <input type="number" name="estimated_m" value="{{ $minutes }}" min="0" max="59" required
                                class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            <span>min</span>
                        </div>
                    </div>
                </div>

                @if (!$isCCA)
                    <div class="basis-1/4 mx-5">
                        <div class="flex flex-col mb-6">
                            <label class="text-xl font-semibold">N° Devis</label>
                            <input type="text" name="quote_number" placeholder="Le numéro de devis"
                                class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xl font-semibold">Facturation</label>
                            <input type="text" name="billing_info" placeholder="Le numéro de facturation"
                                class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="border-2 border-gray-300 rounded-xl shadow-md">
            <h2 class="text-2xl mx-6 my-6">État de la sous tâche</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="flex my-6">
                <div class="basis-2/4 mx-5">
                    <div class="flex flex-col">
                        <label class="text-xl font-semibold">État *</label>
                        <select name="status" id="status-select" onchange="toggleReasonField()" required
                            class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                            <option value="en cours" {{ $subtask->status == 'en cours' ? 'selected' : '' }}>En cours
                            </option>
                            <option value="bloqué" {{ $subtask->status == 'bloqué' ? 'selected' : '' }}>Bloqué</option>
                            <option value="validé" {{ $subtask->status == 'validé' ? 'selected' : '' }}>Validé</option>
                        </select>
                    </div>
                    <div id="reason-container" style="display: {{ $subtask->status == 'bloqué' ? 'block' : 'none' }}">
                        <div class="flex flex-col mt-6">
                            <label class="text-xl font-semibold">Raison du blocage *</label>
                            <textarea name="reason_description" id="reason_description"
                                placeholder="Expliquez le problème..."
                                class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">{{ $subtask->status == 'bloqué' && $subtask->currentBlocking() ? $subtask->currentBlocking()->description : '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="basis-1/4">
                    <label class="text-xl font-semibold">Temps réel cumulé :
                        {{ $subtask->formatDuration($subtask->actual_hours) }}</label>
                    <div class="flex flex-col mt-2">
                        <div>
                            <label class="text-xl">Ajouter du temps</label>
                            <div>
                                <input type="number" name="add_actual_h" value="" min="0"
                                    class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                    h</span>
                                <input type="number" name="add_actual_m" value="" min="0" max="59"
                                    class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                    min</span>
                            </div>
                        </div>
                        <button type="button" onclick="toggleCorrection('correction-actual-hour')"
                            class="bg-gray-100 hover:bg-white border-1 border-gray-300 shadow-sm py-3 font-semibold rounded-lg w-[75%] mb-2">Corriger
                            le
                            temps
                            cumulé</button>
                        <div id="correction-actual-hour" style="display: none;">
                            <label class="text-xl">Déduire du temps</label>
                            <div>
                                <input type="number" name="reduce_actual_h" value="" min="0"
                                    class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                    h</span>
                                <input type="number" name="reduce_actual_m" value="" min="0" max="59"
                                    class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                    min</span>
                            </div>
                            <small class="text-base my-2">Les valeurs seront déduites du temps total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <small class="text-base my-2 mx-6">* Champs obligatoires</small>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto">Enregistrer
                les
                modifications</button>
        </div>
    </form>

    <form action="{{ route('subtasks.destroy', $subtask->id) }}" method="POST"
        id="delete-subtask-form-{{ $subtask->id }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
        <button type="button" onclick="confirmDeleteSubtask({{ $subtask->id }})"
            class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg">Supprimer la
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

        function toggleCorrection(id) {
            const container = document.getElementById(id);
            if (container) {
                if (container.style.display === "none") {
                    container.style.display = "block";
                } else {
                    container.querySelector('input[name="reduce_actual_h"]').value = "";
                    container.querySelector('input[name="reduce_actual_m"]').value = "";
                    container.style.display = "none"
                }
            }
        }

        document.addEventListener('')

        function confirmDeleteSubtask(id) {
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