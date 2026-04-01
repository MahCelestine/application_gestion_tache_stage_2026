<x-layout>

    <form action="{{ route('gestions.subtask_update', $subtask->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="border-2 border-gray-300 rounded-xl shadow-md mb-8">

            <h2 class="text-2xl mx-6 my-6">Gestion Facturation sous-tâche : {{ $subtask->label }}</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="flex my-6">
                <div class=" basis-1/2 mx-10">
                    <label class="text-xl font-semibold">N° Devis *</label>
                    <input type="text" name="quote_number"
                        class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-12"
                        value="{{ $subtask->quote_number }}" required>
                </div>

                <div class="basis-1/2 mx-5">
                    <div>
                        <label class="text-xl font-semibold">N° Facture</label>
                        <input type="text" id="billing_input" name="billing_info"
                            class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-12"
                            value="{{ $subtask->billing_info }}">
                    </div>
                </div>
            </div>
        </div>
        <div id="payement_status" style="display: none;" class="border-2 border-gray-300 rounded-xl shadow-md mb-8">
            <h2 class="text-2xl mx-6 my-6">Etat</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="mt-4 mx-10">
                <div class="flex flex-col my-6">
                    <label class="text-xl font-semibold">Statut Paiement</label>
                    <select
                        class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1 h-12"
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
        onsubmit="return confirm('Remettre cette tâche en cours ? Cela permettra de basculer cette tache dans Accueil.');">
        @csrf
        @method('PATCH')
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-2 px-5 rounded-lg">
            Remettre "En cours"
        </button>
    </form>

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
    </script>
</x-layout>