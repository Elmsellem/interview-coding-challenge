<?php

declare(strict_types=1);

namespace Elmsellem\Tests\Unit\Services\Commission\BaseAmountResolvers;

use Elmsellem\Enums\{ClientType, Currency, OperationType};
use Elmsellem\Models\Operation;

trait PrivateWithdrawAmountResolverDataProvider
{
    public static function getCommissionBaseAmountProvider(): array
    {
        $operation = new Operation();
        $operation->setDate('2025-01-01');
        $operation->setUserId(1);
        $operation->setClientType(ClientType::PRIVATE);
        $operation->setOperationType(OperationType::WITHDRAW);
        $operation->setAmount('1200.00');
        $operation->setCurrency(Currency::from('USD'));

        return [
            'get base amount with free amount' => [
                'weeklyFreeOperationsNumber' => 3,
                'baseCurrency' => Currency::from('EUR'),
                'operation' => $operation,
                'operationNumber' => 1,
                'currentWeek' => [
                    'monday' => '2024-12-30',
                    'sunday' => '2025-01-05',
                ],
                'weekOperations' => [
                    $operation,
                    clone $operation,
                    clone $operation,
                ],
                'calculatedAmount' => '200.00',
                'expectedAmount' => '190.0000000000',
            ],
            'get base amount without free amount' => [
                'weeklyFreeOperationsNumber' => 3,
                'baseCurrency' => Currency::from('EUR'),
                'operation' => $operation,
                'operationNumber' => 4,
                'currentWeek' => [
                    'monday' => '2024-12-30',
                    'sunday' => '2025-01-05',
                ],
                'weekOperations' => [
                    clone $operation,
                    clone $operation,
                    clone $operation,
                    $operation,
                ],
                'calculatedAmount' => null,
                'expectedAmount' => '1200.00',
            ],
        ];
    }

    public static function getBaseWithFreeAmountProvider(): array
    {
        return [
            'Return 0 for first operation less than 1000 EUR' => [
                'position' => 1,
                'operationAmount' => '500.00',
                'weekOperations' => [
                    (new Operation())->setAmount('500.00')->setCurrency(Currency::from('EUR')),
                ],
                'baseCurrency' => Currency::from('EUR'),
                'maxFreeAmount' => 1000,
                'expectedAmount' => '0',
            ],
            'Return 0 if the remaining free amount ≤ 500 EUR in the second operation' => [
                'position' => 2,
                'operationAmount' => '500.00',
                'weekOperations' => [
                    (new Operation())->setAmount('500.00')->setCurrency(Currency::from('EUR')),
                    (new Operation())->setAmount('500.00')->setCurrency(Currency::from('EUR')),
                ],
                'baseCurrency' => Currency::from('EUR'),
                'maxFreeAmount' => 1000,
                'expectedAmount' => '0',
            ],
            'Return 400 if the remaining free amount is 100 EUR in the second operation' => [
                'position' => 2,
                'operationAmount' => '500.00',
                'weekOperations' => [
                    (new Operation())->setAmount('900.00')->setCurrency(Currency::from('EUR')),
                    (new Operation())->setAmount('500.00')->setCurrency(Currency::from('EUR')),
                ],
                'baseCurrency' => Currency::from('EUR'),
                'maxFreeAmount' => 1000,
                'expectedAmount' => '400.0000000000',
            ],
            'Return 500 if the remaining free amount is 0 EUR in the last operation' => [
                'position' => 3,
                'operationAmount' => '500.00',
                'weekOperations' => [
                    (new Operation())->setAmount('500.00')->setCurrency(Currency::from('EUR')),
                    (new Operation())->setAmount('500.00')->setCurrency(Currency::from('EUR')),
                    (new Operation())->setAmount('500.00')->setCurrency(Currency::from('EUR')),
                ],
                'baseCurrency' => Currency::from('EUR'),
                'maxFreeAmount' => 1000,
                'expectedAmount' => '500.00',
            ],
        ];
    }
}
