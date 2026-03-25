<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <form action="{{ route('tasks.store') }}" method="POST">
        <div class="border-2 border-gray-300 rounded-xl shadow-md mb-8">
            <h2 class="text-2xl mx-6 my-6">Ajouter une nouvelle grande tâche</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="flex my-6">
                <div class="basis-2/4 mx-5">
                    @csrf
                    @php
                        $isCCA = request('context') === 'cca';
                    @endphp
                    <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
                    <div class="flex flex-col mb-6">
                        @if($isCCA)
                            <input type="hidden" name="context" value="cca">
                            <input type="hidden" name="client_id" value="{{ $clientCCA->id ?? '' }}">
                            <div class="flex flex-col">
                                <label class="text-xl font-semibold">Client</label>
                                <h2 class="my-4 text-lg rounded-lg border-2 w-[90%] border-white font-semibold px-2 py-1">
                                    CCA</h2>
                            </div>
                        @else
                            <div class="flex flex-col">
                                <label class="text-xl font-semibold">Client *</label>
                                <div class="flex">
                                    <div class="flex flex-col basis-1/2 mt-2">
                                        <label class="text-xl">Ajouter un nouveau client</label>
                                        <input type="text" id="new_client_name" name="new_client_name"
                                            placeholder="Nom du client"
                                            class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-10" />
                                    </div>
                                    <div class="flex flex-col basis-1/2 mt-2">
                                        <label class="text-xl">Client existant</label>
                                        <select name="client_id" id="client_id_select"
                                            class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-10">
                                            <option value="" selected>Choisir un client...</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <p id="client-error-msg" class="text-red-500 font-semibold mt-2 hidden">
                                    Veuillez soit créer un nouveau client, soit en choisir un existant, mais pas
                                    les deux.
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Grande tâche *</label>
                        <input placeholder="Intitulé de la grande tâche" name="label" type="text" required
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
                <div class="basis-1/4">
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Délai *</label>
                        <input type="date" name="due_date" required
                            class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xl font-semibold">Temps donné *</label>
                        <div>
                            <input type="number" name="estimated_h" min="0" required
                                class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                h</span>
                            <input type="number" name="estimated_m" min="0" max="29" required
                                class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            <span>min</span>
                        </div>
                    </div>
                </div>
                @if (!$isCCA)
                    <div class="basis-1/4">
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
        <div class="border-2 border-gray-300 rounded-xl shadow-md mb-8">
            <h2 class="text-2xl mx-6 my-6">Ajouter une sous-tâche</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div id="subtasks-container" class="my-6"></div>
            <div class="mx-5 mb-6">
                <button type="button" id="add-subtask-btn" style="margin: 10px 0;"
                    class="bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 shadow-md/20">
                    + Ajouter une sous-tâche
                </button>
            </div>
            <template id="subtask-template">
                <div class="subtask-row">
                    <div class="flex my-6">
                        <div class="flex basis-2/4 flex-col mx-5">
                            <div class="flex flex-col mb-6">
                                <label class="text-xl font-semibold">Sous-tâche *</label>
                                <input type="text" name="subtasks[INDEX][label]" placeholder="Intitulé" required
                                    class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-xl font-semibold">Assignation :</label>
                                <select name="subtasks[INDEX][equipe_ids][]" multiple
                                    class="select-equipes-dynamic w-[90%] text-lg mt-4 text-gray-600 font-mono">
                                    @foreach ($equipes as $equipe)
                                        <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="basis-1/4">
                            <div class="flex flex-col mb-6">
                                <label class="text-xl font-semibold">Délai *</label>
                                <input type="date" name="subtasks[INDEX][due_date]" required
                                    class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-xl font-semibold">Temps donné *</label>
                                <div>
                                    <input type="number" name="subtasks[INDEX][estimated_h]" min="0" required
                                        class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                        h</span>
                                    <input type="number" name="subtasks[INDEX][estimated_m]" min="0" max="59" required
                                        class="my-4 text-lg rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                        min</span>
                                </div>
                            </div>
                        </div>
                        @if (!$isCCA)
                            <div class="basis-1/4">
                                <div class="flex flex-col mb-6">
                                    <label class="text-xl font-semibold">N° Devis</label>
                                    <input type="text" name="subtasks[INDEX][quote_number]" placeholder="N° Devis"
                                        class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-xl font-semibold">Facturation</label>
                                    <input type="text" name="subtasks[INDEX][billing_info]" placeholder="Facturation"
                                        class="my-4 text-lg rounded-lg border-2 w-[80%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                </div>
                            </div>
                        @endif
                    </div>

                    <button type="button" onclick="this.closest('.subtask-row').remove()"
                        class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg mx-5">Supprimer</button>
                </div>
            </template>
        </div>
        <div class="flex flex col">
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

        let subtaskIndex = 0;
        const container = document.getElementById('subtasks-container');
        const template = document.getElementById('subtask-template');
        const btn = document.getElementById('add-subtask-btn');

        btn.addEventListener('click', function () {
            const clone = template.content.cloneNode(true);

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

        const newClientInput = document.getElementById('new_client_name');
        const existingClientSelect = document.getElementById('client_id_select');
        const errorMsg = document.getElementById('client-error-msg');
        const submitBtn = document.querySelector('button[type="submit"]');

        function checkClientSelection() {
            const hasNew = newClientInput.value.trim() !== "";
            const hasExisting = existingClientSelect.value !== "";

            if (hasNew && hasExisting) {
                errorMsg.classList.remove('hidden');
                newClientInput.classList.add('border-red-500');
                existingClientSelect.classList.add('border-red-500');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                errorMsg.classList.add('hidden');
                newClientInput.classList.remove('border-red-500');
                existingClientSelect.classList.remove('border-red-500');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        newClientInput.addEventListener('input', checkClientSelection);
        existingClientSelect.addEventListener('change', checkClientSelection);
    </script>
</x-layout>