@props ([
    'name',
    'label',
    'options' => [],
    'current' => request($name),
])

<div class="flex w-[45%] items-center mb-2" >
        <p class="font-semibold text-lg">{{ $label}}</p>
        <div class="flex justify-around w-[55%]">
            @foreach ($options as $value => $optionLabel )
            <a href="{{ request()->fullUrlWithQuery([$name => $value]) }}"
                @class([ 'hover:font-semibold', 
                'font-semibold' => $current === (string)$value, 
                'font-normal' => $current !== (string)$value, ])>
                {{ $optionLabel }}</a>
            @endforeach
            @if ($current)
                <a href="{{ request()->fullUrlWithQuery([$name => null]) }}"
                    class="hover:font-semibold">Réinitialiser</a>
            @endif
        </div>
    </div>