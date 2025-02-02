<?php

namespace Elmsellem\Tests\Unit\Services;

use Elmsellem\DTOs\ExchangeRatesDTO;
use Elmsellem\Services\ExchangeRatesService;
use Elmsellem\Tests\ReflectionHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use RuntimeException;

class ExchangeRatesServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected ExchangeRatesService $service;
    protected Client $mockClient;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = Mockery::mock(Client::class);
        $this->service = Mockery::mock(ExchangeRatesService::class)->makePartial();

        ReflectionHelper::setProtectedProperty($this->service, 'client', $this->mockClient);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetExchangeRates(): void
    {
        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode([
            'success' => true,
            'base' => 'EUR',
            'rates' => ['USD' => 1.18, 'JPY' => 130.15],
            'timestamp' => 1627654321,
            'date' => '2022-07-30'
        ]));

        $this->mockClient->shouldReceive('get')->andReturn($responseMock);

        $rates = $this->service->getExchangeRates();

        $this->assertInstanceOf(ExchangeRatesDTO::class, $rates);
        $this->assertEquals('EUR', $rates->base);
        $this->assertArrayHasKey('USD', $rates->rates);
        $this->assertEquals(1.18, $rates->rates['USD']);
    }

    /**
     * @throws ReflectionException
     * @throws GuzzleException
     */
    public function testGetExchangeRatesWithCache(): void
    {
        $cachedRates = new ExchangeRatesDTO(
            1627654321,
            'EUR',
            '2022-07-30',
            ['USD' => 1.18, 'JPY' => 130.15],
        );

        ReflectionHelper::setProtectedProperty($this->service, 'cache', $cachedRates);
        $rates = $this->service->getExchangeRates();

        $this->assertSame($cachedRates, $rates);
    }

    /**
     * @throws ReflectionException
     * @throws GuzzleException
     */
    public function testGetExchangeRatesWithException(): void
    {
        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode([
            'success' => false,
        ]));

        $this->mockClient->shouldReceive('get')->andReturn($responseMock);
        ReflectionHelper::setProtectedProperty($this->service, 'cache', null);

        $this->expectException(RuntimeException::class);

        $this->service->getExchangeRates();
    }
}
