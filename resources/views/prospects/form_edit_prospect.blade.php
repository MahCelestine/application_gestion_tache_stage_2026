<x-layout>
    <div class="mx-40" x-data="{
        status: 'RDV à prendre',
        showRdvDate: false}">
        <form action="{{ route('prospects.update', $prospect->id) }}" method="POST" id="edit-prospect-form">
            @csrf
            @method('PUT')

            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-2">Modifier un prospect</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>

                <div class="flex my-6">
                    <div class="basis-2/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Nom *</label>
                            <input type="text" name="nom" placeholder="Nom du prospect" value="{{ $prospect->nom }}"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"
                                required />
                        </div>
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Source</label>
                            <input type="text" name="source" placeholder="Source du contact"
                                value="{{ $prospect->source }}"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-lg font-semibold">Relance</label>
                            <select name="is_followup"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                <option value="OUI" {{ $prospect->is_followup === 'OUI' ? 'selected' : '' }}>Oui</option>
                                <option value="NON" {{ $prospect->is_followup === 'NON' ? 'selected' : '' }}>Non</option>
                            </select>
                        </div>
                    </div>

                    <div class="basis-1/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">État *</label>
                            <select name="status" id="status-select" x-model="status"
                                x-effect="showRdvDate = (status === 'Date de RDV' || status === 'OK')"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                <option value="RDV à prendre" {{ $prospect->status == 'RDV à prendre' ? 'selected' : '' }}>RDV
                                    à prendre</option>
                                <option value="Date de RDV" {{ $prospect->status == 'Date de RDV' ? 'selected' : '' }}>
                                    Date de
                                    RDV</option>
                                <option value="OK" {{ $prospect->status == 'OK' ? 'selected' : '' }}>OK</option>
                            </select>
                        </div>
                        <div id="date_rdv" x-show="showRdvDate" x-cloak>
                            <label class="text-lg font-semibold">Date du RDV *</label>
                            <input type="date" name="rdv_date" id="rdv-input"
                                value="{{ $prospect->rdv_date ? $prospect->rdv_date->format('Y-m-d') : '' }}"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                            <span id="date-error" class="text-red-500 text-sm font-bold block mb-2"
                                style="display: none;">
                                Veuillez saisir une date obligatoire pour ce statut.
                            </span>
                        </div>
                    </div>

                    <div class="basis-1/4 mx-5">
                        <div class="flex flex-col mb-2">
                            <label class="text-lg font-semibold">Réponse</label>
                            <p id="response-warning" class="text-sm text-orange-600 font-medium mt-2"
                                style="display: none;">
                                L'état doit être "OK" pour modifier la réponse.
                            </p>
                            <select name="response_type" id="response-select"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value=""></option>
                                <option value="OUI" {{ $prospect->response_type == 'OUI' ? 'selected' : '' }}>Oui</option>
                                <option value="NON" {{ $prospect->response_type == 'NON' ? 'selected' : '' }}>Non</option>
                                <option value="DEVIS" {{ $prospect->response_type == 'DEVIS' ? 'selected' : '' }}>Devis
                                </option>
                            </select>
                        </div>
                        <div id="number_quote" style="display: none;">
                            <label class="text-lg font-semibold">N° Devis</label>
                            <input type="text" name="quote_number" value="{{ $prospect->quote_number }}"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-6">Informations complémentaires</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex flex-col mx-5 my-6">
                    <label class="text-lg font-semibold">Information</label>
                    <textarea name="note" placeholder="Information sur le prospect"
                        class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">@if($prospect->notes->isNotEmpty()){{ $prospect->notes->last()->description }}@endif</textarea>
                </div>
            </div>

            <div class="flex flex-col">
                <small class="text-base my-2 mx-6">* Champs obligatoires</small>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] shadow-md transition-all m-auto">
                    Valider
                </button>
            </div>
        </form>

        <div class="mx-6 mb-10">
            <form action="{{ route('prospects.destroy', $prospect->id) }}" method="POST"
                id="delete-form-{{ $prospect->id }}">
                @csrf
                @method('DELETE')
                <button type="button" class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg"
                    onclick="Livewire.dispatch('open-delete-modal', { title: 'le prospect', message: 'Êtes-vous sûr de vouloir supprimer ce prospect ?', label: 'Supprimer', formId: 'delete-form-{{ $prospect->id }}' })">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <livewire:loading-overlay />
    <livewire:delete-confirmation-modal />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status-select');
            const responseSelect = document.getElementById('response-select');
            const responseWarning = document.getElementById('response-warning');

            const rdvContainer = document.getElementById('date_rdv');
            const rdvInput = document.getElementById('rdv-input');
            const dateError = document.getElementById('date-error');
            const responseContainer = document.getElementById('number_quote');

            function toggleFields() {
                if (statusSelect.value === 'Date de RDV' || statusSelect.value === 'OK') {
                    rdvContainer.style.display = 'block';
                    rdvInput.required = true;
                } else {
                    rdvContainer.style.display = 'none';
                    rdvInput.required = false;
                    rdvInput.value = "";
                    dateError.style.display = 'none';
                    rdvInput.classList.remove('border-red-500');
                }

                if (statusSelect.value === 'OK') {
                    responseSelect.disabled = false;
                    responseWarning.style.display = 'none';
                } else {
                    responseSelect.disabled = true;
                    responseWarning.style.display = 'block';
                }

                if (responseSelect.value === 'DEVIS' && statusSelect.value === 'OK') {
                    responseContainer.style.display = 'block';
                } else {
                    responseContainer.style.display = 'none';
                }
            }

            statusSelect.addEventListener('change', toggleFields);
            responseSelect.addEventListener('change', toggleFields);

            rdvInput.addEventListener('invalid', function () {
                dateError.style.display = 'block';
            });

            rdvInput.addEventListener('input', function () {
                if (rdvInput.value !== "") {
                    dateError.style.display = 'none';
                }
            });

            toggleFields();
        });

        document.getElementById('edit-prospect-form').addEventListener('submit', function () {
            const overlay = document.getElementById('loading-overlay');
            const submitBtn = this.querySelector('button[type="submit"]');

            if (overlay) {
                overlay.style.display = 'flex';
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
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
    </script>
</x-layout>