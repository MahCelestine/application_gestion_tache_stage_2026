<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <div class="mx-55" x-data="subtaskManager()">
        <form action="{{ route('tasks.store') }}" method="POST" @submit="handleSubmit">
            @csrf
            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-2">Ajouter une nouvelle grande tâche</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex my-2">
                    <div class="basis-2/4 mx-5">
                        @if(isset($prospect))
                            <input type="hidden" name="prospect_id" value="{{ $prospect->id }}">
                        @endif
                        @php
                            $isCCA = request('context') === 'cca';
                        @endphp
                        <input type="hidden" name="redirect_to" value="{{ $isCCA ? 'tasks.cca' : 'tasks.index' }}">
                        <div class="flex flex-col mb-2">
                            @if($isCCA)
                                <input type="hidden" name="context" value="cca">
                                <input type="hidden" name="client_id" value="{{ $clientCCA->id ?? '' }}">
                                <div class="flex flex-col">
                                    <label class="text-lg font-semibold">Client</label>
                                    <h2 class="my-2 rounded-lg border-2 w-[95%] border-white font-semibold px-2 py-1">
                                        CCA</h2>
                                </div>
                            @else
                                <div class="flex flex-col">
                                    <label class="text-lg font-semibold">Client *</label>
                                    <div class="flex">
                                        <div class="flex flex-col basis-1/2 mt-2">
                                            <label class="text-lg">Ajouter un nouveau client</label>
                                            <input type="text" name="new_client_name" x-model="newClientName"
                                                placeholder="Nom du client"
                                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-10"
                                                :class="clientError ? 'border-red-500' : 'border-gray-300'" />
                                        </div>
                                        <div class="flex flex-col basis-1/2 mt-2">
                                            <label class="text-lg">Client existant</label>
                                            <select name="client_id" id="client_id_select" x-model="selectedClientId"
                                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-10"
                                                :class="clientError ? 'border-red-500' : 'border-gray-300'">
                                                <option value="" selected>Choisir un client...</option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}" {{ (isset($existingClient) && $existingClient->id == $client->id) ? 'selected' : '' }}>
                                                        {{ $client->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <p x-show="clientError" class="text-red-500 font-semibold mt-2 hidden" x-cloak>
                                        Veuillez soit créer un nouveau client, soit en choisir un existant, mais pas
                                        les deux.
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Grande tâche *</label>
                            <textarea placeholder="Intitulé de la grande tâche" name="label" type="text" required
                                class="my-2 rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /></textarea>
                        </div>
                        <div>
                            <label class="text-lg font-semibold">Assignation (plusieurs choix possible)</label>
                            <select id="select-equipes" name="equipe_ids[]" multiple
                                class="w-[95%] mt-4 text-gray-600 font-mono">
                                <option value="" disabled selected>Choisir un membre ...</option>
                                @foreach ($equipes as $equipe)
                                    <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="basis-1/4">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Délai *</label>
                            <input type="date" name="due_date" x-model="parentDate" required
                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />

                            <p id="date-error-msg" class="text-red-500 font-semibold mt-2 hidden">
                                Attention : Une ou plusieurs sous-tâches ont une date postérieure à celle de la
                                grande
                                tâche.
                            </p>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Temps donné *</label>
                            <div>
                                <input type="number" name="estimated_h" min="0" x-model="parentHours" required
                                    class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                    h</span>
                                <input type="number" name="estimated_m" min="0" max="59" x-model="parentMinutes"
                                    required
                                    class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                <span>min</span>
                            </div>
                        </div>
                    </div>
                    @if (!$isCCA)
                        <div class="basis-1/4">
                            <div class="flex flex-col mb-2">
                                <label class="text-lg font-semibold">N° Devis *</label>
                                <input type="text" name="quote_number" x-model="quoteNumber" @input="syncQuotes()"
                                    placeholder="Le numéro de devis"
                                    class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"
                                    required />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-lg font-semibold">Facturation</label>
                                <input type="text" name="billing_info" x-model="billingInfo"
                                    placeholder="Le numéro de facturation"
                                    class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="quote_number" value="INTERNE">
                        <input type="hidden" name="billing_info" value="OFFERT">
                    @endif
                </div>
            </div>
            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-2">Ajouter une sous-tâche</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div id="subtasks-container" class="my-2">
                    <template x-for="(subtask, index) in subtasks" :key="index">
                        <div class="subtask-row">
                            <div class="flex my-3">
                                <div class="flex basis-2/4 flex-col mx-5">
                                    <div class="flex flex-col mb-2">
                                        <label class="text-lg font-semibold">Sous-tâche *</label>
                                        <input type="text" :name="`subtasks[${index}][label]`" x-model="subtask.label"
                                            placeholder="Intitulé" required
                                            class="my-2 rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-lg font-semibold">Assignation :</label>
                                        <select :name="`subtasks[${index}][equipe_ids][]`" multiple
                                            x-init="initTomSelect($el)"
                                            class="select-equipes-dynamic w-[95%] mt-4 text-gray-600 font-mono">
                                            @foreach ($equipes as $equipe)
                                                <option value="{{ $equipe->id }}">{{ $equipe->prenom }}
                                                    {{ $equipe->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="basis-1/4">
                                    <div class="flex flex-col mb-2">
                                        <label class="text-lg font-semibold">Délai *</label>
                                        <input type="date" :name="`subtasks[${index}][due_date]`"
                                            x-model="subtask.due_date" required
                                            class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"
                                            :class="isDateInvalid(subtask.due_date) ? 'border-red-500' : 'border-gray-300'" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-lg font-semibold">Temps donné *</label>
                                        <div>
                                            <input type="number" :name="`subtasks[${index}][estimated_h]`"
                                                x-model.number="subtask.estimated_h" @input="syncTime()" min="0"
                                                required
                                                class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                                h</span>
                                            <input type="number" :name="`subtasks[${index}][estimated_m]`"
                                                x-model.number="subtask.estimated_m" @input="syncTime()" min="0"
                                                max="59" required
                                                class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                                min</span>
                                        </div>
                                    </div>
                                </div>
                                @if (!$isCCA)
                                    <div class="basis-1/4">
                                        <div class="flex flex-col mb-2">
                                            <label class="text-lg font-semibold">N° Devis</label>
                                            <input type="text" :name="`subtasks[${index}][quote_number]`"
                                                x-model="subtask.quote_number" placeholder="N° Devis"
                                                :value="subtask.quote_number"
                                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                        </div>
                                        <div class="flex flex-col">
                                            <label class="text-lg font-semibold">Facturation</label>
                                            <input type="text" :name="`subtasks[${index}][billing_info]`"
                                                :value="subtask.billing_info" placeholder="Facturation"
                                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" :name="`subtasks[${index}][quote_number]`" value="INTERNE">
                                    <input type="hidden" :name="`subtasks[${index}][billing_info]`" value="OFFERT">
                                @endif
                            </div>

                            <button type="button" @click="removeSubtask(index)"
                                class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg mx-5 transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Supprimer</button>
                        </div>
                    </template>
                </div>
                <div class="mx-5 mb-2">
                    <button type="button" @click="addSubtask()" style="margin: 10px 0;"
                        class="bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">
                        + Ajouter une sous-tâche
                    </button>
                </div>
            </div>
    </div>
    <div class="flex flex-col mx-55">
        <small class="text-base my-2 mx-6">* Champs obligatoires</small>
        <button type="submit" :disabled="!canSubmit"
            :class="!canSubmit ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600'"
            class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Valider</button>
    </div>
    </form>
    </div>

    <livewire:loading-overlay />

    <script>
        function subtaskManager() {
            return {
                subtasks: [],
                quoteNumber: "{{ isset($prospect) ? $prospect->quote_number : old('quote_number') }}",
                parentDate: '',
                parentHours: 0,
                parentMinutes: 0,
                newClientName: "{{ isset($prospect) && !$existingClient ? $prospect->nom : '' }}",
                selectedClientId: "{{ isset($existingClient) ? $existingClient->id : '' }}",

                get clientError() {
                    return this.newClientName.trim() !== "" && this.selectedClientId !== "";
                },
                get canSubmit() {
                    const oneFilled = this.newClientName.trim() !== "" || this.selectedClientId !== "";
                    return oneFilled && !this.clientError;
                },

                init() {
                    new TomSelect("#select-equipes", {
                        plugins: ['remove_button'],
                        create: false,
                    });
                },

                addSubtask() {
                    this.subtasks.push({
                        label: '',
                        due_date: '',
                        estimated_h: 0,
                        estimated_m: 0,
                        quote_number: this.quoteNumber,
                    });
                },

                removeSubtask(index) {
                    this.subtasks.splice(index, 1);
                    this.syncTime();
                },

                initTomSelect(element) {
                    this.$nextTick(() => {
                        if (!element.tomselect) {
                            new TomSelect(element, {
                                plugins: ['remove_button'],
                                create: false,
                            });
                        }
                    });
                },

                isDateInvalid(subDate) {
                    if (!this.parentDate || !subDate) return false;
                    return new Date(subDate) > new Date(this.parentDate);
                },

                syncTime() {
                    let totalMinutes = this.subtasks.reduce((total, s) => {
                        return total + (parseInt(s.estimated_h) * 60 || 0) + (parseInt(s.estimated_m) || 0);
                    }, 0);

                    let newH = Math.floor(totalMinutes / 60);
                    let newM = totalMinutes % 60;

                    if (newH > this.parentHours) this.parentHours = newH;
                    if (newM > this.parentMinutes && newH >= this.parentHours) this.parentMinutes = newM;
                },

                syncQuotes() {
                    this.subtasks.forEach(s => {
                        s.quote_number = this.quoteNumber;
                    });
                }
            }
        }

        document.querySelector('form').addEventListener('submit', function (e) {

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