<div>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="mx-55" x-data="subtaskManager()">
        <form wire:submit.prevent="save" @submit="handleSubmit">
            @csrf
            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-2">Importer la ligne Evoliz</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>

                <div class="flex my-2">
                    <div class="basis-2/4 mx-5">
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Client *</label>
                            <div class="flex">
                                <div class="flex flex-col basis-1/2 mt-2">
                                    <label class="text-lg">Ajouter un nouveau client</label>
                                    <input type="text" wire:model="new_client_name" x-model="newClientName"
                                        placeholder="Nom du client"
                                        class="my-2 rounded-lg border-2 w-[85%] border-gray-300 text-gray-600 px-2 py-1 h-10"
                                        :class="clientError ? 'border-red-500' : 'border-gray-300'" />
                                </div>
                                <div class="flex flex-col basis-1/2 mt-2">
                                    <label class="text-lg">Client existant</label>
                                    <select wire:model="client_id" x-model="selectedClientId"
                                        class="my-2 rounded-lg border-2 w-[85%] border-gray-300 text-gray-600 px-2 py-1 h-10"
                                        :class="clientError ? 'border-red-500' : 'border-gray-300'">
                                        <option value="">Choisir un client...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <p x-show="clientError" class="text-red-500 font-semibold mt-2" x-cloak>
                                Veuillez soit créer un nouveau client, soit en choisir un existant.
                            </p>
                        </div>

                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Grande tâche *</label>
                            <textarea wire:model="label" placeholder="Intitulé de la grande tâche" required
                                class="my-2 rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"></textarea>
                        </div>

                        <div wire:ignore>
                            <label class="text-lg font-semibold">Assignation</label>
                            <select id="select-equipes" wire:model="equipes_ids" multiple
                                class="w-[95%] mt-4 text-gray-600 font-mono">
                                @foreach ($equipes as $equipe)
                                    <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="basis-1/4">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Délai *</label>
                            <input type="date" wire:model="due_date" x-model="parentDate" required
                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 text-gray-600 px-2 py-1" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Temps donné *</label>
                            <div>
                                <input type="number" wire:model="estimated_h" x-model="parentHours" min="0" required
                                    class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                    h</span>
                                <input type="number" wire:model="estimated_m" x-model="parentMinutes" min="0" max="59"
                                    required
                                    class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                    min</span>
                            </div>
                        </div>
                    </div>

                    <div class="basis-1/4">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">N° Devis *</label>
                            <input type="text" wire:model="quote_number" x-model="quoteNumber" @input="syncQuotes()"
                                placeholder="Le numéro de devis"
                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 text-gray-600 px-2 py-1"
                                required />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Facturation</label>
                            <input type="text" wire:model="billing_info" x-model="billingInfo"
                                placeholder="Le numéro de facturation"
                                class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                    </div>
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
                                        <input type="text" x-model="subtask.label" placeholder="Intitulé" required
                                            class="my-2 rounded-lg border-2 w-[95%] border-gray-300 px-2 py-1" />
                                    </div>
                                    <div wire:ignore class="flex flex-col">
                                        <label class="text-lg font-semibold">Assignation :</label>
                                        <select multiple x-init="initSubtaskSelect($el, index)"
                                            class="select-equipes-dynamic w-[95%] mt-4 text-gray-600 font-mono">
                                            @foreach ($equipes as $equipe)
                                                <option value="{{ $equipe->id }}">{{ $equipe->prenom }} {{ $equipe->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="basis-1/4">
                                    <div class="flex flex-col mb-2">
                                        <label class="text-lg font-semibold">Délai *</label>
                                        <input type="date" x-model="subtask.due_date" required
                                            class="my-2 rounded-lg border-2 w-[85%] border-gray-300 px-2 py-1"
                                            :class="isDateInvalid(subtask.due_date) ? 'border-red-500' : 'border-gray-300'" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-lg font-semibold">Temps donné *</label>
                                        <div>
                                            <input type="number" x-model.number="subtask.estimated_h"
                                                @input="syncTime()" min="0" required
                                                class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" /><span>
                                                h</span>
                                            <input type="number" x-model.number="subtask.estimated_m"
                                                @input="syncTime()" min="0" max="59" required
                                                class="my-2 rounded-lg border-2 w-[35%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 ml-2" /><span>
                                                min</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="basis-1/4">
                                    <div class="flex flex-col mb-2">
                                        <label class="text-lg font-semibold">N° Devis</label>
                                        <input type="text" x-model="subtask.quote_number" placeholder="N° Devis"
                                            class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-lg font-semibold">Facturation</label>
                                        <input type="text" x-model="subtask.billing_info" placeholder="Facturation"
                                            class="my-2 rounded-lg border-2 w-[85%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                                    </div>
                                </div>
                            </div>
                            <button type="button" @click="removeSubtask(index)"
                                class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg mx-5 transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">Supprimer</button>
                        </div>
                    </template>
                </div>
                <div class="mx-5 mb-2">
                    <button type="button" @click="addSubtask()" style="margin: 10px 0;"
                        class="bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">
                        + Ajouter une sous-tâche </button>
                </div>
            </div>
            <div class="flex flex-col py-4">
                <button type="submit" :disabled="!canSubmit"
                    :class="!canSubmit ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600'"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]"
                    @click="$wire.set('subtasks', subtasks)">
                    Valider et passer à la suivante
                </button>
            </div>
        </form>
    </div>

    <div wire:loading wire:target="save">
        <livewire:loading-overlay />
    </div>

    <script>
        function subtaskManager() {
            return {
                subtasks: [],
                quoteNumber: @entangle('quote_number'),
                parentDate: @entangle('due_date'),
                parentHours: @entangle('estimated_h'),
                parentMinutes: @entangle('estimated_m'),
                newClientName: @entangle('new_client_name') || '',
                selectedClientId: @entangle('client_id') || '',
                billingInfo: @entangle('billing_info') || '',
                isSaving: false,

                get clientError() {
                    const hasNew = this.newClientName && this.newClientName.trim().length > 0;
                    const hasSelected = this.selectedClientId && this.selectedClientId !== "";
                    return hasNew && hasSelected;
                },

                get canSubmit() {
                    const hasNew = this.newClientName && this.newClientName.trim().length > 0;
                    const hasSelected = this.selectedClientId && this.selectedClientId !== "";
                    return (hasNew || hasSelected) && !this.clientError;
                },

                get hasDateError() {
                    return this.subtasks.some(s => this.isDateInvalid(s.due_date));
                },

                async handleSubmit() {
                    if (this.isSaving) return;
                    this.isSaving = true;

                    const overlay = document.getElementById('loading-overlay');
                    if (overlay) overlay.style.display = 'flex';

                    await new Promise(resolve => setTimeout(resolve, 1000));

                    this.$wire.set('subtasks', this.subtasks);
                    this.$wire.save().then(() => {
                        this.isSaving = false;
                        this.subtasks = [];
                    });
                },

                init() {
                    this.setupMainSelect();
                    Livewire.on('next-devis', () => { this.setupMainSelect(); });
                },

                setupMainSelect() {
                    let el = document.getElementById("select-equipes");
                    if (el) {
                        if (el.tomselect) el.tomselect.destroy();
                        new TomSelect(el, {
                            plugins: ['remove_button'],
                            closeAfterSelect: true,
                            onChange: (value) => { @this.set('equipe_ids', value); }
                        });
                    }
                },

                initSubtaskSelect(el, index) {
                    this.$nextTick(() => {
                        if (el.tomselect) el.tomselect.destroy();
                        new TomSelect(el, {
                            plugins: ['remove_button'],
                            closeAfterSelect: true,
                            onChange: (value) => { this.subtasks[index].equipe_ids = value; }
                        });
                    });
                },

                addSubtask() {
                    this.subtasks.push({
                        label: '',
                        due_date: this.parentDate,
                        estimated_h: 0,
                        estimated_m: 0,
                        equipe_ids: [],
                        quote_number: this.quoteNumber,
                        billing_info: this.billingInfo
                    });
                },

                removeSubtask(index) {
                    this.subtasks.splice(index, 1);
                    this.syncTime();
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
                    this.subtasks.forEach(s => { s.quote_number = this.quoteNumber; });
                }
            }
        }
    </script>
</div>