<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <div class="mx-40">
        <form method="POST" action="{{ route('subtasks.store') }}">
            @csrf
            @php
                $isCCA = request('context') === 'cca';
            @endphp
            <input type="hidden" name="context" value="{{ $isCCA ? 'cca' : '' }}">
            <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
            <input type="hidden" name="task_id" value="{{ request('task_id') }}">
            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-2">Ajouter une sous-tâche</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex my-6">
                    <div class="basis-2/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Sous-tâche *</label>
                            <textarea type="text" name="label" placeholder="Intitulé" required
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /></textarea>
                        </div>
                        <div>
                            <label class="text-lg font-semibold">Assignation (plusieurs choix possible)</label>
                            <select id="select-equipes" name="equipe_ids[]" multiple
                                class="w-[95%]  mt-4 text-gray-600 font-mono">
                                <option value="" disabled selected>Choisir un membre ...</option>
                                @foreach ($equipes as $equipe)
                                    <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="basis-1/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Délai *</label>
                            <input type="date" name="due_date" required
                                class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Temps Donné *</label>
                            <div>
                                <input type="number" name="estimated_h" placeholder="0" min="0" required
                                    class="my-2  rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                <span>h</span>
                                <input type="number" name="estimated_m" placeholder="0" min="0" max="59" required
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
                                    value="{{ $parentTask->quote_number ?? '' }}"
                                    class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-lg font-semibold">Facturation</label>
                                <input type="text" name="billing_info" placeholder="Le numéro de facturation"
                                    class="my-2  rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="quote_number" value="INTERNE">
                        <input type="hidden" name="billing_info" value="OFFERT">
                    @endif
                </div>
            </div>
            <div class="flex flex-col">
                <small class="text-base my-2 mx-6">* Champs obligatoires</small>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Valider</button>
            </div>
        </form>
    </div>

    <livewire:loading-overlay />

    <script>
        new TomSelect("#select-equipes", {
            plugins: ['remove_button'],
            create: false,
        });

        document.querySelector('form').addEventListener('submit', function () {
            const overlay = document.getElementById('loading-overlay');
            const submitBtn = this.querySelector('button[type="submit"]');

            if (overlay) {
                overlay.style.display = 'flex';
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Enregistrement...';
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>
</x-layout>