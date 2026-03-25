@props ([
    'currentStatus' => request('filter_status'),
])

@php
    $status_possible = [
    'bloqué' => 'Bloqués',
    'en cours' => 'En cours',
    'validé' => 'Validés',
    ];
@endphp

<div class="flex w-[45%] items-center mb-2" >
        <p class="font-semibold text-lg">Filtrer par état :</p>
        <div class="flex justify-around w-[55%]">
            @if ($currentStatus)
                <a href="{{ request()->fullUrlWithQuery(['filter_status' => null]) }}"
                    class="hover:font-semibold">Réinitialiser les filtres</a>
            @endif
            @foreach ($status_possible as $value => $label )
            <a href="{{ request()->fullUrlWithQuery(['filter_status' => $value]) }}"
                @class([ 'hover:font-semibold', 
                'font-semibold' => $currentStatus === $value, 
                'font-normal' => $currentStatus !== $value, ])>
                {{ $label }}</a>
            @endforeach
        </div>
    </div>