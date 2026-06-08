<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <div class="mx-40" x-data="{
            status: '{{ $task->status }}',
            hasSubtasks: {{ $task->subtasks->count() > 0 ? 'true' : 'false' }},
            showCorrection: false,
        }">
        <form id="edit-task-form" action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @php
                $isCCA = request('context') === 'cca';
            @endphp
            <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
            @method('PUT')
            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class=" mx-6 my-2 text-xl">Modifier une grande tâche</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex my-6">
                    <div class="basis-2/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Client</label>
                            <input type="hidden" name="client_id" value="{{ $task->client_id }}">
                            <h2 class="my-2  rounded-lg border-2 w-[95%] border-white font-semibold px-2 py-1">
                                {{ $task->client->nom }}
                            </h2>
                        </div>
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Grande Tâche *</label>
                            <textarea type="text" name="label" required
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />{{ $task->label }}</textarea>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Assignation</label>
                            <select id="select-equipes" name="equipe_ids[]" multiple
                                class="w-[95%]  mt-4 text-gray-600 font-mono">
                                @foreach ($equipes as $equipe)
                                    <option value="{{ $equipe->id }}" {{ $task->equipes->contains($equipe->id) ? 'selected' : '' }}>
                                        {{ $equipe->prenom }} {{ $equipe->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="basis-1/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Délai *</label>
                            <input type="date" name="due_date"
                                value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" required
                                class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Temps donné *</label>
                            @php
                                $totalHours = (float) ($task->estimated_hours ?? 0);
                                $hours = floor($task->estimated_hours);
                                $minutes = (int) round(($totalHours - $hours) * 60);
                            @endphp
                            @if ($task->subtasks->count() === 0)
                                <div>
                                    <input type="number" name="estimated_h" value="{{ $hours }}" min="0" required
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                    <span>h</span>
                                    <input type="number" name="estimated_m" value="{{ $minutes }}" min="0" max="59" required
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                    <span>min</span>
                                </div>
                            @else
                                <p class="my-2  rounded-lg border-2 w-[85%] border-white px-2 py-1">
                                    {{ $task->formatTime($task->estimated_hours) }} (Calculé via sous-tâches)
                                </p>
                                <input type="hidden" name="estimated_h" value="{{ $hours }}">
                                <input type="hidden" name="estimated_m" value="{{ $minutes }}">
                            @endif
                        </div>
                    </div>
                    @if (!$isCCA)
                        <div class="basis-1/4 mx-5">
                            <div class="flex flex-col mb-2">
                                <label class="text-lg font-semibold">N° Devis *</label>
                                <input type="text" name="quote_number" placeholder="Le numéro de devis"
                                    value="{{ $task->quote_number }}"
                                    class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-lg font-semibold">Facturation</label>
                                <input type="text" name="billing_info" placeholder="Le numéro de facturation"
                                    value="{{ $task->billing_info }}"
                                    class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="border-2 border-gray-300 rounded-xl shadow-md">
                <h2 class=" mx-6 my-2">État de la tâche</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex my-6">
                    @if ($task->subtasks->count() === 0)
                        <div class="basis-1/2 mx-5">
                            <div class="flex flex-col">
                                <label class="text-lg font-semibold">État *</label>
                                <select name="status" id="status-select" x-model="status" required
                                    class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                    <option value="en cours" {{ $task->status == 'en cours' ? 'selected' : '' }}>En cours
                                    </option>
                                    <option value="attente BAT" {{ $task->status == 'attente BAT' ? 'selected' : '' }}>Attente
                                        BAT</option>
                                    <option value="bloqué" {{ $task->status == 'bloqué' ? 'selected' : '' }}>Bloqué</option>
                                    <option value="BAT ok" {{ $task->status == 'BAT ok' ? 'selected' : '' }}>BAT ok</option>
                                    <option value="validé" {{ $task->status == 'validé' ? 'selected' : '' }}>Validé</option>
                                </select>
                            </div>
                            <div id="reason-container" x-show="status === 'bloqué'" x-cloak>
                                <div class="flex flex-col mb-2">
                                    <label class="text-lg font-semibold">Raison du blocage *</label>
                                    <textarea name="reason_description" id="reason_description"
                                        placeholder="Expliquez le problème..."
                                        :required="status === 'bloqué' && !hasSubtasks"
                                        class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">{{ $task->currentBlocking() ? $task->currentBlocking()->description : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="status" value="{{ $task->status }}" />
                        <div class="basis-1/2 mx-5">
                            <label class="text-lg font-semibold">État</label>
                            <div class="my-2">
                                <span class=" rounded-lg border-2 w-[35%] border-white px-2 py-1">
                                    {{ ucfirst($task->status) }}
                                </span>
                                <p class="my-2  rounded-lg border-2 w-[85%] border-white px-2 py-1">
                                    L'état est calculé automatiquement selon l'avancement des sous-tâches.
                                </p>
                            </div>
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <label class="text-lg font-semibold">Temps réel cumulé :
                            {{ $task->formatTime($task->actual_hours) }}</label>
                        @if ($task->subtasks->count() === 0)
                            <div class="flex flex-col mt-2">
                                <label class="text-lg font-semibold">Ajouter du temps</label>
                                <div>
                                    <input type="number" name="add_actual_h" placeholder="0" min="0"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                    <span>h</span>
                                    <input type="number" name="add_actual_m" placeholder="0" min="0" max="59"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                    <span>min</span>
                                </div>
                            </div>
                            <button type="button" @click="showCorrection = !showCorrection"
                                class="bg-gray-100 hover:bg-white border-1 border-gray-300 shadow-sm py-3 font-semibold rounded-lg w-[75%] mb-2 transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Corriger
                                le
                                temps
                                cumulé</button>
                            <div id="correction-actual-hour" x-show="showCorrection" x-cloak>
                                <label class="text-lg font-semibold">Déduire du temps</label>
                                <div>
                                    <input type="number" name="reduce_actual_h" value="" min="0"
                                        x-effect="if(!showCorrection) { $el.value = '' }"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                        h</span>
                                    <input type="number" name="reduce_actual_m" value="" min="0" max="59"
                                        x-effect="if(!showCorrection) { $el.value = '' }"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                        min</span>
                                </div>
                                <small class="text-base my-2">Les valeurs seront déduites du temps total</small>
                            </div>
                        @else
                            <p class="my-2  rounded-lg border-2 w-[85%] border-white px-2 py-1">Le temps est géré via
                                les
                                sous-tâches.</p>
                        @endif
                    </div>
                </div>
            </div>
            @if ($errors->any())
                <div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="flex flex-col">
                <small class="text-base my-2 mx-6">* Champs obligatoires</small>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Enregistrer
                    les
                    modifications</button>
            </div>
        </form>
        <div class="flex">
        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" id="delete-form-{{ $task->id }}" class="mr-4">
            @csrf
            @method('DELETE')
            <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
            <button type="button"
                onclick="Livewire.dispatch('open-delete-modal', { title: 'la suppression de la grande tâche', message: 'Êtes-vous sûr de vouloir supprimer cette tâche ? Cela suprimera également toutes les sous-tâches associées.', label: 'Supprimer', formId: 'delete-form-{{ $task->id }}' })"
                class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Supprimer
                la tâche</button>
        </form>

        <form action="{{ route('tasks.duplicate', $task->id) }}" method="POST" id="duplicate-form-{{ $task->id }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
            <button type="button"
                onclick="Livewire.dispatch('open-delete-modal', { title: 'la dupplication de la grande tâche et ses sous-tâches', message: 'Êtes-vous sûr de vouloir dupliquer cette tâche ? Cela dupliquera également toutes les sous-tâches associées.', label: 'Dupliquer', formId: 'duplicate-form-{{ $task->id }}' })"
                class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-5 rounded-lg transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Dupliquer
                la tâche</button>
        </form>
        </div>
    </div>

    <livewire:loading-overlay />

    <livewire:delete-confirmation-modal />

    <script>
        new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
        });

        document.getElementById('edit-task-form').addEventListener('submit', function () {
            const overlay = document.getElementById('loading-overlay');
            const submitBtn = this.querySelector('button[type="submit"]');

            if (overlay) {
                overlay.style.display = 'flex';
            }

            if (submitBtn) {
                submitBtn.disabled = true;
            }
        });

        window.addEventListener('do-submit-delete', event => {
            const form = document.getElementById(event.detail.formId);
            if (form) {
                const overlay = document.getElementById('loading-overlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                }
                form.submit();
            }
        })

        window.addEventListener('do-submit-duplicate', event => {
            const form = document.getElementById(event.detail.formId);
            if (form) {
                const overlay = document.getElementById('loading-overlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                }
                form.submit();
            }
        })

        document.addEventListener('DOMContentLoaded', toggleReasonField);
    </script>
</x-layout>