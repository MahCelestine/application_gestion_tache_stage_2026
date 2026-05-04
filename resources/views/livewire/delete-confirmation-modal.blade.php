<div>
    @if($isOpen)
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-[10000] flex items-center justify-center p-4">
            {{-- Fenêtre Blanche --}}
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">

                {{-- 1. La pastille rouge (Icone) --}}
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                {{-- 2. Le texte (SORTI de la pastille rouge) --}}
                <div class="mt-4 text-center">
                    <h3 class="text-xl font-bold text-gray-900">
                        Confirmer {{ $title }}
                    </h3>
                    <p class="mt-2 text-gray-500 text-sm">
                        {{ $message }}
                    </p>
                </div>

                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('isOpen', false)"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                        Annuler
                    </button>
                    <button wire:click="confirm"
                        class="flex-1 px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition">
                        {{ $label }}
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>