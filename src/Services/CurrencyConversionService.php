<?php

namespace Elmsellem\Services;

use Elmsellem\Enums\Currency;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyConversionService implements CurrencyConversionInterface
{
    protected ExchangeRatesService $exchangeRatesService;

    public function __construct()
    {
        $this->exchangeRatesService = new ExchangeRatesService();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function convert(Currency $fromCurrency, Currency $toCurrency, string $amount): string
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        /** Convert the amount to the requested currency if it is in the base currency. */
        $rates = $this->exchangeRatesService->getExchangeRates();
        if ($fromCurrency === Currency::from($rates->base)) {
            return Math::multiply($amount, $rates->rates[$toCurrency->value]);
        }

        /** Convert the amount to the base currency and return it if the requested currency is the base currency. */
        $fromCurrencyToBase = Math::multiply($amount, Math::divide(1, $rates->rates[$fromCurrency->value]));
        if ($toCurrency === Currency::from($rates->base)) {
            return $fromCurrencyToBase;
        }

        /** Convert the amount to the requested currency. */
        return Math::multiply($fromCurrencyToBase, $rates->rates[$toCurrency->value]);
    }
}
