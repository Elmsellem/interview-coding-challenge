<?php

namespace Elmsellem\Tests\Unit\Services\Commission;

use Elmsellem\Models\Operation;
use Elmsellem\Services\{Commission\BaseAmountResolvers\AbstractAmountResolver,
    Commission\Calculators\AbstractCommission,
    Commission\CommissionHandler};
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommissionHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    #[DataProvider('calculateProvider')]
    public function testCalculate(bool $setResolver, string $baseAmount): void
    {
        $operation = (new Operation())->setAmount('100.00');
        $calculator = Mockery::mock(AbstractCommission::class);

        if ($setResolver) {
            $amountResolver = Mockery::mock(AbstractAmountResolver::class);
            $amountResolver->shouldReceive('getCommissionBaseAmount')
                ->once()
                ->with($operation)
                ->andReturn($baseAmount);
        }

        $handler = new CommissionHandler($calculator, $amountResolver ?? null);

        $calculator->shouldReceive('calculate')
            ->once()
            ->with($baseAmount)
            ->andReturn('10.00');

        $result = $handler->calculate($operation);

        $this->assertEquals('10.00', $result);
    }

    public static function calculateProvider(): array
    {
        return [
            'when base amount resolver is present' => [
                'setResolver' => true,
                'baseAmount' => '50.00',
            ],
            'when base amount resolver is null' => [
                'setResolver' => false,
                'baseAmount' => '100.00',
            ],
        ];
    }
}
