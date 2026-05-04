<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <div class="mx-40" x-data="{
        status: '{{ $subtask->status }}',
        showCorrection: false,
    }">
        <form action="{{ route('subtasks.update', $subtask->id) }}" method="POST" id="edit-subtask-form">
            @csrf
            @php
                $isCCA = request('context') === 'cca';
            @endphp
            <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
            @method('PUT')
            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-2">Modifier une sous-tâche</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex my-6">
                    <div class="basis-2/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Sous-tâche</label>
                            <textarea name="label" required
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />{{ $subtask->label }}</textarea>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Assignation (plusieurs choix possible)</label>
                            <select id="select-equipes" name="equipe_ids[]" multiple
                                class="w-[95%]  mt-4 text-gray-600 font-mono">
                                @foreach ($equipes as $equipe)
                                    <option value="{{ $equipe->id }}" {{ $subtask->equipes->contains($equipe->id) ? 'selected' : '' }}>
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
                                value="{{ $subtask->due_date ? $subtask->due_date->format('Y-m-d') : '' }}" required
                                class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Temps donné *</label>
                            @php
                                $hours = floor($subtask->estimated_hours);
                                $minutes = round(($subtask->estimated_hours - $hours) * 60);
                            @endphp
                            <div>
                                <input type="number" name="estimated_h" value="{{ $hours }}" min="0" required
                                    class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                <span>h</span>
                                <input type="number" name="estimated_m" value="{{ $minutes }}" min="0" max="59" required
                                    class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                <span>min</span>
                            </div>
                        </div>
                    </div>

                    @if (!$isCCA)
                        <div class="basis-1/4 mx-5">
                            <div class="flex flex-col mb-2">
                                <label class="text-lg font-semibold">N° Devis</label>
                                <input type="text" name="quote_number" placeholder="Le numéro de devis"
                                    value="{{ $subtask->quote_number }}"
                                    class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-lg font-semibold">Facturation</label>
                                <input type="text" name="billing_info" placeholder="Le numéro de facturation"
                                    value="{{ $subtask->billing_info }}"
                                    class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="border-2 border-gray-300 rounded-xl shadow-md">
                <h2 class="text-xl mx-6 my-2">État de la sous tâche</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex my-6">
                    <div class="basis-2/4 mx-5">
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">État *</label>
                            <select name="status" x-model="status" id="status-select" required
                                class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                <option value="en cours" {{ $subtask->status == 'en cours' ? 'selected' : '' }}>En cours
                                </option>
                                <option value="attente BAT" {{ $subtask->status == 'attente BAT' ? 'selected' : '' }}>
                                    Attente BAT</option>
                                <option value="bloqué" {{ $subtask->status == 'bloqué' ? 'selected' : '' }}>Bloqué
                                </option>
                                <option value="validé" {{ $subtask->status == 'validé' ? 'selected' : '' }}>Validé
                                </option>
                            </select>
                        </div>
                        <div id="reason-container"
                            x-show="status === 'bloqué'" x-cloak>
                            <div class="flex flex-col mt-3">
                                <label class="text-lg font-semibold">Raison du blocage *</label>
                                <textarea name="reason_description" id="reason_description"
                                    placeholder="Expliquez le problème..." :required="status === 'bloqué'"
                                    class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">{{ $subtask->status == 'bloqué' && $subtask->currentBlocking() ? $subtask->currentBlocking()->description : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="basis-1/4">
                        <label class="text-lg font-semibold">Temps réel cumulé :
                            {{ $subtask->formatTime($subtask->actual_hours) }}</label>
                        <div class="flex flex-col mt-2">
                            <div>
                                <label class="text-lg font-semibold">Ajouter du temps</label>
                                <div>
                                    <input type="number" name="add_actual_h" value="" min="0"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                        h</span>
                                    <input type="number" name="add_actual_m" value="" min="0" max="59"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                        min</span>
                                </div>
                            </div>
                            <button type="button" @click="showCorrection = !showCorrection"
                                class="bg-gray-100 hover:bg-white border-1 border-gray-300 shadow-sm py-3 font-semibold rounded-lg w-[75%] mb-2">Corriger
                                le
                                temps
                                cumulé</button>
                            <div id="correction-actual-hour" x-show="showCorrection" x-cloak>
                                <label class="text-lg font-semibold">Déduire du temps</label>
                                <div>
                                    <input type="number" name="reduce_actual_h" value="" min="0" x-effect="if(!showCorrection) { $el.value = '' }"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
                                        h</span>
                                    <input type="number" name="reduce_actual_m" value="" min="0" max="59" x-effect="if(!showCorrection) { $el.value = '' }"
                                        class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"><span>
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

        <form action="{{ route('subtasks.destroy', $subtask->id) }}" method="POST" id="delete-form-{{ $subtask->id }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}" />
            <button type="button"
                onclick="Livewire.dispatch('open-delete-modal', { title: 'la suppression de la sous-tâche', message: 'Êtes-vous sûr de vouloir supprimer cette sous-tâche ?', label: 'Supprimer', formId: 'delete-form-{{ $subtask->id }}' })"
                class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg">Supprimer la
                sous-tâche</button>
        </form>

    </div>

    <livewire:loading-overlay />
    <livewire:delete-confirmation-modal />

    <script>
        var select = new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
            maxItems: null,
        });

        document.getElementById('edit-subtask-form').addEventListener('submit', function () {
            const overlay = document.getElementById('loading-overlay');
            const submitBtn = this.querySelector('button[type="submit"]');

            if (overlay) {
                overlay.style.display = 'flex';
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('cursor-not-allowed', 'opacity-50');
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
        });

        document.addEventListener('')
    </script>
</x-layout>