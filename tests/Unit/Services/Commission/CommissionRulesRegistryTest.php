<?php

namespace Elmsellem\Tests\Unit\Services\Commission;

use Elmsellem\Models\Operation;
use Elmsellem\Services\{Commission\CommissionHandler, Commission\CommissionRulesRegistry};
use Elmsellem\Tests\ReflectionHelper;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class CommissionRulesRegistryTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use CommissionRulesRegistryDataProvider;

    public function testLoadRules(): void
    {
        $mock = Mockery::mock(CommissionRulesRegistry::class)->makePartial();
        $rules = config('app')['commissionRules'] ?? [];

        foreach ($rules as $rule) {
            $mock->shouldReceive('addRule')
                ->once()
                ->with(
                    $rule['operationType'],
                    $rule['clientType'],
                    $rule['commissionCalculator'],
                    $rule['commissionAmountResolver'] ?? null,
                );
        }

        $mock->loadRules();
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('addRuleProvider')]
    public function testAddRule(array $rule): void
    {
        $registry = new CommissionRulesRegistry();

        $registry->addRule(
            $rule['operationType'],
            $rule['clientType'],
            $rule['commissionCalculator'],
            $rule['commissionAmountResolver'] ?? null,
        );

        $rules = ReflectionHelper::getProtectedProperty($registry, 'rules');

        $handler = $rules[$rule['operationType']->value][$rule['clientType']->value];
        $calculator = ReflectionHelper::getProtectedProperty($handler, 'calculator');
        $amountResolver = ReflectionHelper::getProtectedProperty($handler, 'amountResolver');

        $this->assertInstanceOf(CommissionHandler::class, $handler);
        $this->assertInstanceOf($rule['commissionCalculator']['class'], $calculator);

        $rule['commissionAmountResolver'] ?
            $this->assertInstanceOf($rule['commissionAmountResolver']['class'], $amountResolver) :
            $this->assertNull($amountResolver);
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('getCommissionHandlerProvider')]
    public function testGetCommissionHandler(Operation $operation, bool $exception): void
    {
        $registry = new CommissionRulesRegistry();
        $handlerMock = Mockery::mock(CommissionHandler::class);
        if (!$exception) {
            ReflectionHelper::setProtectedProperty($registry, 'rules', [
                $operation->getOperationType()->value => [
                    $operation->getClientType()->value => $handlerMock,
                ],
            ]);
        } else {
            $this->expectException(InvalidArgumentException::class);
        }

        $handler = $registry->getCommissionHandler($operation->getOperationType(), $operation->getClientType());

        $this->assertTrue($handlerMock === $handler);
    }
}
