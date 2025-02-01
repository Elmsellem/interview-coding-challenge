<?php

namespace Elmsellem\Services;

use Elmsellem\Enums\Currency;

interface CurrencyConversionInterface
{
    public function convert(Currency $fromCurrency, Currency $toCurrency, string $amount): string;
}
