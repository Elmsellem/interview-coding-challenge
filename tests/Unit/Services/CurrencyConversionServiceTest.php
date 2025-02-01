<?php

namespace Elmsellem\Tests\Unit\Services;


use Elmsellem\DTOs\ExchangeRatesDTO;
use Elmsellem\Enums\Currency;
use Elmsellem\Services\{CurrencyConversionService, ExchangeRatesService};
use Elmsellem\Tests\ReflectionHelper;
use GuzzleHttp\Exception\GuzzleException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\{Attributes\After, Attributes\Before, Attributes\DataProvider, TestCase};
use ReflectionException;

class CurrencyConversionServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private int $currentBcScale;

    #[Before]
    protected function saveBcScale(): void
    {
        $this->currentBcScale = bcscale();
    }

    #[After]
    protected function revertBcScale(): void
    {
        bcscale($this->currentBcScale);
    }

    /**
     * @throws GuzzleException
     * @throws ReflectionException
     */
    #[DataProvider('convertProvider')]
    public function testConvert(
        Currency         $fromCurrency,
        Currency         $toCurrency,
        string           $amount,
        ExchangeRatesDTO $rates,
        string           $expected,
    )
    {
        bcscale(10);
        $mockExchangeRatesService = Mockery::mock(ExchangeRatesService::class);
        $service = Mockery::mock(CurrencyConversionService::class)->makePartial();
        ReflectionHelper::setProtectedProperty($service, 'exchangeRatesService', $mockExchangeRatesService);

        $mockExchangeRatesService->shouldReceive('getExchangeRates')->andReturn($rates);

        $this->assertEquals($expected, $service->convert($fromCurrency, $toCurrency, $amount));
    }

    public static function convertProvider(): array
    {
        return [
            'same currency' => [
                Currency::from('EUR'),
                Currency::from('EUR'),
                '100',
                ExchangeRatesDTO::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.18, 'JPY' => 130.15],
                    'timestamp' => 1627654321,
                    'date' => '2022-07-30',
                ]),
                '100',
            ],
            'EUR to USD' => [
                Currency::from('EUR'),
                Currency::from('USD'),
                '100',
                ExchangeRatesDTO::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.18, 'JPY' => 130.15],
                    'timestamp' => 1627654321,
                    'date' => '2022-07-30',
                ]),
                '118.0000000000',
            ],
            'USD to EUR' => [
                Currency::from('USD'),
                Currency::from('EUR'),
                '100',
                ExchangeRatesDTO::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.18, 'JPY' => 130.15],
                    'timestamp' => 1627654321,
                    'date' => '2022-07-30',
                ]),
                '84.7457627100',
            ],
            'USD to JPY' => [
                Currency::from('USD'),
                Currency::from('JPY'),
                '100',
                ExchangeRatesDTO::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.18, 'JPY' => 130.15],
                    'timestamp' => 1627654321,
                    'date' => '2022-07-30',
                ]),
                '11029.6610167065',
            ],
            'JPY to EUR' => [
                Currency::from('JPY'),
                Currency::from('EUR'),
                '100',
                ExchangeRatesDTO::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.18, 'JPY' => 130.15],
                    'timestamp' => 1627654321,
                    'date' => '2022-07-30',
                ]),
                '0.7683442100',
            ],
        ];
    }
}
