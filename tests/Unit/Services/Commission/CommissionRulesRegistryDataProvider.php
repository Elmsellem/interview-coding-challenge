<?php

declare(strict_types=1);

namespace Elmsellem\Tests\Unit\Services\Commission;

use Elmsellem\Enums\{ClientType, OperationType};
use Elmsellem\Models\Operation;
use Elmsellem\Services\Commission\Calculators\PercentageCommission;

trait CommissionRulesRegistryDataProvider
{
    public static function addRuleProvider(): array
    {
        return [
            'rule without amount resolver' => [
                'rule' => [
                    'operationType' => OperationType::DEPOSIT,
                    'clientType' => ClientType::BUSINESS,
                    'commissionCalculator' => [
                        'class' => PercentageCommission::class,
                        'options' => [
                            'commission' => 0.03,
                        ],
                    ],
                    'commissionAmountResolver' => null,
                ],
            ],
            'rule with amount resolver' => [
                'rule' => [
                    'operationType' => OperationType::WITHDRAW,
                    'clientType' => ClientType::PRIVATE,
                    'commissionCalculator' => [
                        'class' => PercentageCommission::class,
                        'options' => [
                            'commission' => 0.3,
                        ],
                    ],
                    'commissionAmountResolver' => [
                        'class' => FakeAmountResolver::class,
                        'options' => [
                            'maxFreeAmount' => 1000,
                            'weeklyFreeOperationsNumber' => 3,
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function getCommissionHandlerProvider(): array
    {
        $operation = new Operation();
        $operation->setOperationType(OperationType::WITHDRAW);
        $operation->setClientType(ClientType::PRIVATE);

        return [
            'When handler exist' => [
                'operation' => $operation,
                'exception' => false,
            ],
            'When handler not exit' => [
                'operation' => $operation,
                'exception' => true,
            ],
        ];
    }
}
