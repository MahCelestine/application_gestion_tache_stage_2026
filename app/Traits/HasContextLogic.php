<?php

namespace App\Traits;

trait HasContextLogic
{

    public static function formatQuoteNumber(?string $quoteNumber, ?string $context): ?string
    {
        return ($context === 'cca') ? 'INTERNE' : $quoteNumber;
    }

    public static function formatBillingInfo(?string $billingInfo, ?string $context): ?string
    {
        if ($context === 'cca') {
            return 'OFFERT';
        }

        return $billingInfo;
    }
}