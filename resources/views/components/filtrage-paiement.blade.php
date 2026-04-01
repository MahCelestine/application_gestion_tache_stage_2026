@props ([
    'currentPayment' => request('filter_payment'),
])

@php
    $status_possible = [
    'a_facturer' => 'À facturer',
    'non_paye' => 'Non payés',
    'paye' => 'Payés',
    ];
@endphp

<div class="flex w-[45%] items-center mb-2" >
        <p class="font-semibold text-lg">Filtrer par paiement :</p>
        <div class="flex justify-around w-[55%]">
            @if ($currentPayment)
                <a href="{{ request()->fullUrlWithQuery(['filter_payment' => null]) }}"
                    class="hover:font-semibold">Réinitialiser les filtres</a>
            @endif
            @foreach ($status_possible as $value => $label )
            <a href="{{ request()->fullUrlWithQuery(['filter_payment' => $value]) }}"
                @class([ 'hover:font-semibold', 
                'font-semibold' => $currentPayment === $value, 
                'font-normal' => $currentPayment !== $value, ])>
                {{ $label }}</a>
            @endforeach
        </div>
    </div>