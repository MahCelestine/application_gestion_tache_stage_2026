<x-layout>
    <div class="mx-40" x-data="{
        billingInfo: '{{ $subtask->billing_info }}',
        showPayement: '{{ $subtask->billing_info }}'.trim() !== ''
    }">
        <form action="{{ route('gestions.subtask_update', $subtask->id) }}" method="POST" id="gestion-task-form">
            @csrf
            @method('PUT')
            <div class="border-2 border-gray-300 rounded-xl shadow-md mb-2">

                <h2 class="text-xl mx-6 my-2">Gestion Facturation sous-tâche : {{ $subtask->label }}</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="flex my-6">
                    <div class=" basis-1/2 mx-10">
                        <label class="text-lg font-semibold">N° Devis *</label>
                        <input type="text" name="quote_number"
                            class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-12"
                            value="{{ $subtask->quote_number }}" required>
                    </div>

                    <div class="basis-1/2 mx-5">
                        <div>
                            <label class="text-lg font-semibold">N° Facture</label>
                            <input type="text" id="billing_input" name="billing_info" x-model="billingInfo" x-effect="showPayement = billingInfo.trim() !== ''"
                                class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-12"
                                value="{{ $subtask->billing_info }}">
                        </div>
                    </div>
                </div>
            </div>
            <div id="payement_status" x-show="showPayement" x-cloak class="border-2 border-gray-300 rounded-xl shadow-md mb-2">
                <h2 class="text-xl mx-6 my-2">Etat</h2>
                <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
                <div class="mt-4 mx-10">
                    <div class="flex flex-col my-6">
                        <label class="text-lg font-semibold">Statut Paiement</label>
                        <select
                            class="my-2  rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-12"
                            name="is_paid">
                            <option value="0" {{ !$subtask->is_paid ? 'selected' : '' }}>Non Payé</option>
                            <option value="1" {{ $subtask->is_paid ? 'selected' : '' }}>Payé</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <small class="text-base my-2 mx-6">* Champs obligatoires</small>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto">Enregistrer
                    les modifications</button>
            </div>
        </form>

        <form action="{{ route('gestions.subtask_reset', $subtask->id) }}" method="POST"
            id="reset-subtask-{{ $subtask->id }}">
            @csrf
            @method('PATCH')
            <button type="button"
                onclick="Livewire.dispatch('open-delete-modal', { title: 'Remettre en cours', message: 'Voulez-vous vraiment remettre cette sous-tâche en cours ?', label: 'Remettre en cours', formId: 'reset-subtask-{{ $subtask->id }}' })"
                class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg">
                Remettre "En cours"
            </button>
        </form>
    </div>

    <livewire:loading-overlay />
    <livewire:delete-confirmation-modal />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const billingInput = document.getElementById('billing_input');
            const payementContainer = document.getElementById('payement_status');

            function togglePayementStatus() {
                if (billingInput.value.trim() !== "") {
                    payementContainer.style.display = 'block';
                } else {
                    payementContainer.style.display = 'none';
                }
            }

            togglePayementStatus();

            billingInput.addEventListener('input', togglePayementStatus);
        });

        document.getElementById('gestion-task-form').addEventListener('submit', function () {
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
    </script>
</x-layout>