<?php

use Elmsellem\Enums\{ClientType, OperationType};
use Elmsellem\Services\Commission\Calculators\PercentageCommission;
use Elmsellem\Services\Commission\BaseAmountResolvers\PrivateWithdrawAmountResolver;

return [
    'commissionRules' => [
        [
            'operationType' => OperationType::DEPOSIT,
            'clientType' => ClientType::PRIVATE,
            'commissionCalculator' => [
                'class' => PercentageCommission::class,
                'options' => [
                    'commission' => 0.03,
                ],
            ],
            'commissionAmountResolver' => null,
        ],
        [
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
        [
            'operationType' => OperationType::WITHDRAW,
            'clientType' => ClientType::PRIVATE,
            'commissionCalculator' => [
                'class' => PercentageCommission::class,
                'options' => [
                    'commission' => 0.3,
                ],
            ],
            'commissionAmountResolver' => [
                'class' => PrivateWithdrawAmountResolver::class,
                'options' => [
                    'maxFreeAmount' => 1000,
                    'weeklyFreeOperationsNumber' => 3,
                ],
            ],
        ],
        [
            'operationType' => OperationType::WITHDRAW,
            'clientType' => ClientType::BUSINESS,
            'commissionCalculator' => [
                'class' => PercentageCommission::class,
                'options' => [
                    'commission' => 0.5,
                ],
            ],
            'commissionAmountResolver' => null,
        ],
    ],

    'baseCurrency' => 'EUR',

    'currencies' => [
        'EUR',
        'USD',
        'JPY',
    ],

    'currencyDecimalPlaces' => [
        'EUR' => 2,
        'USD' => 2,
        'JPY' => 0,
    ],
];
