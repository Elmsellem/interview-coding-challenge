<?php

declare(strict_types=1);

namespace Elmsellem\Tests\Unit\Services\Commission\BaseAmountResolvers;

use DateMalformedStringException;
use Elmsellem\Enums\{ClientType, Currency, OperationType};
use Elmsellem\Models\Operation;
use Elmsellem\Repositories\OperationRepository;
use Elmsellem\Services\Commission\BaseAmountResolvers\PrivateWithdrawAmountResolver;
use Elmsellem\Services\CurrencyConversionInterface;
use Elmsellem\Services\Math;
use Elmsellem\Tests\ReflectionHelper;
use GuzzleHttp\Exception\GuzzleException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\{Attributes\After, Attributes\Before, Attributes\DataProvider, TestCase};
use ReflectionException;

class PrivateWithdrawAmountResolverTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use PrivateWithdrawAmountResolverDataProvider;

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
     * @throws ReflectionException
     * @throws DateMalformedStringException
     * @throws GuzzleException
     */
    #[DataProvider('getCommissionBaseAmountProvider')]
    public function testGetCommissionBaseAmount(
        int       $weeklyFreeOperationsNumber,
        Currency  $baseCurrency,
        Operation $operation,
        int       $operationNumber,
        array     $currentWeek,
        array     $weekOperations,
        ?string   $calculatedAmount,
        string    $expectedAmount,
    )
    {
        bcscale(10);
        $resolverMock = Mockery::mock(PrivateWithdrawAmountResolver::class)->makePartial();
        $resolverMock->shouldAllowMockingProtectedMethods();
        $repoMock = Mockery::mock(OperationRepository::class);
        $currencyServiceMock = Mockery::mock(CurrencyConversionInterface::class);

        ReflectionHelper::setProtectedProperty($resolverMock, 'weeklyFreeOperationsNumber', $weeklyFreeOperationsNumber);
        ReflectionHelper::setProtectedProperty($resolverMock, 'operationRepository', $repoMock);
        ReflectionHelper::setProtectedProperty($resolverMock, 'currencyService', $currencyServiceMock);
        ReflectionHelper::setProtectedProperty($resolverMock, 'baseCurrency', $baseCurrency);

        $repoMock->shouldReceive('findBy')
            ->once()
            ->with([
                'userId' => $operation->getUserId(),
                'operationType' => OperationType::WITHDRAW,
                'startDate' => $currentWeek['monday'],
                'endDate' => $currentWeek['sunday'],
            ])
            ->andReturn($weekOperations);

        $resolverMock->shouldReceive('getOperationNumber')
            ->once()
            ->with($operation, $weekOperations)
            ->andReturn($operationNumber);

        if ($calculatedAmount) {
            $convertedAmount = Math::add($operation->getAmount(), '10');
            $currencyServiceMock->shouldReceive('convert')
                ->once()
                ->with($operation->getCurrency(), $baseCurrency, $operation->getAmount())
                ->andReturn($convertedAmount);

            $resolverMock->shouldReceive('getBaseWithFreeAmount')
                ->once()
                ->with($operationNumber, $convertedAmount, $weekOperations)
                ->andReturn($calculatedAmount);

            $currencyServiceMock->shouldReceive('convert')
                ->once()
                ->with($baseCurrency, $operation->getCurrency(), $calculatedAmount)
                ->andReturn(Math::subtract($calculatedAmount, '10'));
        }

        $result = $resolverMock->getCommissionBaseAmount($operation);

        $this->assertEquals($expectedAmount, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetOperationNumber(): void
    {
        $resolverMock = Mockery::mock(PrivateWithdrawAmountResolver::class);

        $operation = (new Operation())->setDate('2025-01-01')
            ->setUserId(2)
            ->setClientType(ClientType::PRIVATE)
            ->setOperationType(OperationType::WITHDRAW)
            ->setAmount('10.00')
            ->setCurrency(Currency::from('EUR'));

        $result1 = ReflectionHelper::invokeProtectedMethod($resolverMock, 'getOperationNumber', [
            $operation,
            [
                '2025-01-01_1_private_withdraw_10.00_EUR' => true,
                '2025-01-01_2_private_withdraw_10.00_EUR' => true,
                '2025-01-01_3_private_withdraw_10.00_EUR' => true,
            ],
        ]);

        $result2 = ReflectionHelper::invokeProtectedMethod($resolverMock, 'getOperationNumber', [$operation, []]);

        $this->assertEquals(2, $result1);
        $this->assertFalse($result2);
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('getBaseWithFreeAmountProvider')]
    public function testGetBaseWithFreeAmount(
        int      $position,
        string   $operationAmount,
        array    $weekOperations,
        Currency $baseCurrency,
        float    $maxFreeAmount,
        string   $expectedAmount,
    )
    {
        bcscale(10);
        $resolverMock = Mockery::mock(PrivateWithdrawAmountResolver::class);
        $currencyServiceMock = Mockery::mock(CurrencyConversionInterface::class);

        ReflectionHelper::setProtectedProperty($resolverMock, 'baseCurrency', $baseCurrency);
        ReflectionHelper::setProtectedProperty($resolverMock, 'maxFreeAmount', $maxFreeAmount);
        ReflectionHelper::setProtectedProperty($resolverMock, 'currencyService', $currencyServiceMock);

        foreach ($weekOperations as $operation) {
            $currencyServiceMock->shouldReceive('convert')
                ->once()
                ->with($operation->getCurrency(), $baseCurrency, $operation->getAmount())
                ->andReturn($operation->getAmount());
        }

        $result = ReflectionHelper::invokeProtectedMethod($resolverMock, 'getBaseWithFreeAmount', [
            $position,
            $operationAmount,
            $weekOperations,
        ]);

        $this->assertEquals($expectedAmount, $result);
    }
}
