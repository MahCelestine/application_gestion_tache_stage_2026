<x-layout>
    <form action="{{ route('prospects.store') }}" method="POST">
        @csrf
        <div class="border-2 border-gray-300 rounded-xl shadow-md mb-8">
            <h2 class="text-2xl mx-6 my-6">Ajouter un nouveau prospect</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="flex my-6">
                <div class="basis-1/2 mx-5">
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Nom *</label>
                        <input type="text" name="nom" placeholder="Nom du prospect"
                            class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"
                            required />
                    </div>
                    <div class="flex flex-col mb-6">
                        <label class="text-xl font-semibold">Source</label>
                        <input type="text" name="source" placeholder="Source"
                            class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                    </div>
                </div>
                <div class="basis-1/2 mx-5">
                    <div class="flex flex-col">
                        <div class="flex flex-col mb-6">
                            <label class="text-xl font-semibold">État *</label>
                            <select name="status" id="status-select"
                                class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1">
                                <option value="RDV à prendre" selected>RDV à prendre</option>
                                <option value="Date de RDV">Date de RDV</option>
                                <option value="OK">OK</option>
                            </select>
                        </div>
                        <div id="date_rdv" style="display: none;" class="flex flex-col">
                            <label class="text-xl font-semibold">Date du RDV</label>
                            <input type="date" name="rdv_date"
                                class="my-4 text-lg rounded-lg border-2 w-[90%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="border-2 border-gray-300 rounded-xl shadow-md mb-8">
            <h2 class="text-2xl mx-6 my-6">Informations complémentaires</h2>
            <span class="block border-b-2 border-gray-300 w-[95%] m-auto"></span>
            <div class="flex flex-col mx-5 my-6">
                <label class="text-xl font-semibold">Information</label>
                <textarea name="note" placeholder="Information sur le prospect"
                    class="my-4 text-lg rounded-lg border-2 w-[95%] border-gray-300 focus:border-gray-400 focus:outline-gray-400 text-gray-600 px-2 py-1"></textarea>
            </div>
        </div>
        <div class="flex flex-col">
            <small class="text-base my-2 mx-6">* Champs obligatoires</small>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white py-4 font-semibold rounded-lg w-[20%] m-auto">Valider</button>
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status-select');

            const rdvContainer = document.getElementById('date_rdv');

            function toggleFields() {
                if (statusSelect.value === 'Date de RDV' || statusSelect.value === 'OK') {
                    rdvContainer.style.display = 'block';
                } else {
                    rdvContainer.style.display = 'none';
                }
            }

            statusSelect.addEventListener('change', toggleFields);

            toggleFields();
        })

    </script>
</x-layout>