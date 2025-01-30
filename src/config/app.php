<?php

use Elmsellem\Enums\{ClientType, OperationType};
use Elmsellem\Services\Commission\Calculators\PercentageCommission;

return [
    'commissionRules' => [
        [
            'operationType' => OperationType::DEPOSIT,
            'clientType' => ClientType::PRIVATE,
            'commissionCalculator' => PercentageCommission::class,
            'commissionAmountResolver' => null,
            'options' => [
                'commission' => 0.03,
            ],
        ],
        [
            'operationType' => OperationType::DEPOSIT,
            'clientType' => ClientType::BUSINESS,
            'commissionCalculator' => PercentageCommission::class,
            'commissionAmountResolver' => null,
            'options' => [
                'commission' => 0.03,
            ],
        ],
        [
            'operationType' => OperationType::WITHDRAW,
            'clientType' => ClientType::BUSINESS,
            'commissionCalculator' => PercentageCommission::class,
            'commissionAmountResolver' => null,
            'options' => [
                'commission' => 0.5,
            ],
        ],
    ],

    'currencies' => [
        'EUR',
        'USD',
        'JPY',
    ],
];
