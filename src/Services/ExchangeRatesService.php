<?php

namespace Elmsellem\Services;

use Elmsellem\DTOs\ExchangeRatesDTO;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRatesService extends AbstractHttpService
{
    protected static ExchangeRatesDTO|null $cache = null;

    public function __construct(int $retryCount = 3, int $retryDelay = 1000)
    {
        $url = 'https://api.exchangeratesapi.io/latest?access_key=' . env('EXCHANGE_RATES_API_KEY');
        parent::__construct($url, $retryCount, $retryDelay);
    }

    /**
     * @throws GuzzleException
     */
    public function getExchangeRates(): ExchangeRatesDTO
    {
        if (self::$cache) {
            return self::$cache;
        }

        $response = $this->client->get('');

        return self::$cache = ExchangeRatesDTO::fromArray($this->toDecodedJson($response));
    }
}
