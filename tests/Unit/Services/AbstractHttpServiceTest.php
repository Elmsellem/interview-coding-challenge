<?php

namespace Elmsellem\Tests\Unit\Services;


use Elmsellem\Services\AbstractHttpService;
use Elmsellem\Tests\ReflectionHelper;
use GuzzleHttp\Client;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use ReflectionException;

class AbstractHttpServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @throws ReflectionException
     */
    public function testCreateClient()
    {
        $mockHttpService = Mockery::mock(AbstractHttpService::class)->makePartial();
        ReflectionHelper::setProtectedProperty($mockHttpService, 'baseUri', 'localhost');
        $mockHttpService->shouldAllowMockingProtectedMethods();

        $client = $mockHttpService->createClient();
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @throws ReflectionException
     */
    #[TestWith([408, 0, true])]
    #[TestWith([429, 0, true])]
    #[TestWith([500, 0, true])]
    #[TestWith([502, 0, true])]
    #[TestWith([503, 0, true])]
    #[TestWith([504, 0, true])]
    #[TestWith([504, 3, false])]
    #[TestWith([200, 0, false])]
    #[TestWith([201, 0, false])]
    #[TestWith([400, 0, false])]
    #[TestWith([404, 0, false])]
    #[TestWith([401, 0, false])]
    #[TestWith([403, 0, false])]
    #[TestWith([422, 0, false])]
    public function testGetRetryDecider(int $statusCode, int $retryNumber, bool $expected)
    {
        $mockHttpService = Mockery::mock(AbstractHttpService::class)->makePartial();
        ReflectionHelper::setProtectedProperty($mockHttpService, 'retryCount', 3);
        $mockHttpService->shouldAllowMockingProtectedMethods();

        $retryDecider = $mockHttpService->getRetryDecider();
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn($statusCode);

        $this->assertEquals(
            $expected,
            $retryDecider($retryNumber, Mockery::mock(RequestInterface::class), $response),
        );
    }

    public function testToJson()
    {
        $mockHttpService = Mockery::mock(AbstractHttpService::class)->makePartial();
        $mockHttpService->shouldAllowMockingProtectedMethods();

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody->getContents')->andReturn('{"key": "value"}');

        $result = $mockHttpService->toJson($response);
        $this->assertEquals(['key' => 'value'], $result);
    }
}
