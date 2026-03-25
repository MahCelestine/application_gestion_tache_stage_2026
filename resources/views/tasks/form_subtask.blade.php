<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <form method="POST" action="{{ route('subtasks.store') }}">
        @csrf
        @php
            $isCCA = request('context') === 'cca';
        @endphp
        <input type="hidden" name="context" value="{{ $isCCA ? 'cca' : '' }}">
        <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
        <input type="hidden" name="task_id" value="{{ request('task_id') }}">
        <div class="border-2 border-gray-300 rounded-xl shadow-md mb-8">
            <h2 class="text-2xl mx-6 my-6">Ajouter une sous-tâche</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="flex my-6">
                <div class="basis-2/4 mx-5">
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Sous-tâche *</label>
                        <input type="text" name="label" placeholder="Intitulé" required
                            class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                    </div>
                    <div>
                        <label class="text-xl font-semibold">Assignation (plusieurs choix possible)</label>
                        <select id="select-equipes" name="equipe_ids[]" multiple
                            class="w-[90%] text-lg mt-4 text-gray-600 font-mono">
                            <option value="" disabled selected>Choisir un membre ...</option>
                            @foreach ($equipes as $equipe)
                                <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="basis-1/4 mx-5">
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Délai *</label>
                        <input type="date" name="due_date" required
                            class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xl font-semibold">Temps Donné *</label>
                        <div>
                            <input type="number" name="estimated_h" placeholder="0" min="0" required
                                class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            <span>h</span>
                            <input type="number" name="estimated_m" placeholder="0" min="0" max="59" required
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
        <div class="flex flex-col">
            <small class="text-base my-2 mx-6">* Champs obligatoires</small>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto">Valider</button>
        </div>
    </form>

    <script>
        new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
            create: false,
        });
    </script>
</x-layout>