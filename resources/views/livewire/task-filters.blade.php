<div class="flex w-[65%] items-center mb-2" >
        <p class="font-semibold text-lg">{{ $label}}</p>
        <div class="flex justify-around w-[55%]">
            @foreach ($options as $value => $optionLabel )
            <button type="button" wire:click="setFilter('{{ $value }}')"
                @class([ 
                'hover:font-semibold', 
                'font-semibold' => $current === (string)$value, 
                'font-normal' => $current !== (string)$value, ])>
                {{ $optionLabel }}</button>
            @endforeach
            @if ($current)
                <button wire:click="setFilter('')"
                    class="hover:font-semibold">Réinitialiser</button>
            @endif
        </div>
    </div>