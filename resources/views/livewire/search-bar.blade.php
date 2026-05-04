<div class="relative flex items-center w-full">
    <div class="w-[90%] relative">
        <input 
            wire:model.live.debounce.300ms="search"
            type="text" 
            placeholder="{{ $placeholder }}"
            class="border-2 border-gray-300 py-2 px-6 rounded-4xl w-full font-mono text-lg text-gray-600 focus:border-gray-400 focus:outline-gray-400 shadow-md" 
        />
        
        @if($search)
            <button 
                wire:click="$set('search', '')" 
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
            >
                <i class="bx bx-x text-2xl"></i>
            </button>
        @endif
    </div>
    
    <div class="ml-3">
        <i class="bx bx-search text-3xl text-gray-700"></i>
    </div>
</div>