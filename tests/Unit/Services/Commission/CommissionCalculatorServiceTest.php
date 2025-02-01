<?php

namespace Elmsellem\Tests\Unit\Services\Commission;

use Elmsellem\Enums\{ClientType, Currency, OperationType};
use Elmsellem\Models\Operation;
use Elmsellem\Services\{Commission\CommissionCalculatorService,
    Commission\CommissionHandler,
    Commission\CommissionRulesRegistry};
use Mockery;
use PHPUnit\Framework\{Attributes\After, Attributes\Before, TestCase};
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class CommissionCalculatorServiceTest extends TestCase
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

    public function testCalculateCommission(): void
    {
        bcscale(10);
        $registryMock = Mockery::mock(CommissionRulesRegistry::class);
        $commissionHandler = Mockery::mock(CommissionHandler::class);
        $calculator = new CommissionCalculatorService($registryMock);

        $operation = new Operation();
        $operation->setOperationType(OperationType::DEPOSIT);
        $operation->setClientType(ClientType::PRIVATE);
        $operation->setCurrency(Currency::from('EUR'));

        $registryMock->shouldReceive('getCommissionHandler')
            ->once()
            ->with($operation->getOperationType(), $operation->getClientType())
            ->andReturn($commissionHandler);

        $commissionHandler->shouldReceive('calculate')
            ->once()
            ->with($operation)
            ->andReturn('0.023');

        $result = $calculator->calculateCommission($operation);

        $this->assertEquals('0.03', $result);
    }
}
