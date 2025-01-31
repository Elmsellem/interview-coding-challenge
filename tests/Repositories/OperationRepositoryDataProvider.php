<?php

declare(strict_types=1);

namespace Elmsellem\Tests\Repositories;

use Elmsellem\Enums\{ClientType, Currency, OperationType};
use Elmsellem\Models\Operation;
use Generator;

trait OperationRepositoryDataProvider
{
    public static function findByProvider(): array
    {
        $operation1 = (new Operation())->setDate('2025-01-01')->setUserId(1)->setOperationType(OperationType::DEPOSIT);
        $operation2 = (new Operation())->setDate('2025-01-02')->setUserId(2)->setOperationType(OperationType::WITHDRAW);
        $operation3 = (new Operation())->setDate('2025-01-03')->setUserId(1)->setOperationType(OperationType::WITHDRAW);

        $cache = [
            'key1' => $operation1,
            'key2' => $operation2,
            'key3' => $operation3,
        ];

        return [
            'filter by userId' => [
                'cache' => $cache,
                'filter' => ['userId' => 1, 'operationType' => null, 'startDate' => null, 'endDate' => null],
                'expected' => ['key1' => $operation1, 'key3' => $operation3],
            ],
            'filter by operationType' => [
                'cache' => $cache,
                'filter' => ['userId' => null, 'operationType' => OperationType::DEPOSIT, 'startDate' => null, 'endDate' => null],
                'expected' => ['key1' => $operation1],
            ],
            'filter by startDate' => [
                'cache' => $cache,
                'filter' => ['userId' => null, 'operationType' => null, 'startDate' => '2025-01-02', 'endDate' => null],
                'expected' => ['key2' => $operation2, 'key3' => $operation3],
            ],
            'filter by endDate' => [
                'cache' => $cache,
                'filter' => ['userId' => null, 'operationType' => null, 'startDate' => null, 'endDate' => '2025-01-02'],
                'expected' => ['key1' => $operation1, 'key2' => $operation2],
            ],
            'filter by userId and operationType' => [
                'cache' => $cache,
                'filter' => ['userId' => 1, 'operationType' => OperationType::WITHDRAW, 'startDate' => null, 'endDate' => null],
                'expected' => ['key3' => $operation3],
            ],
            'filter by userId and date range' => [
                'cache' => $cache,
                'filter' => ['userId' => 1, 'operationType' => null, 'startDate' => '2025-01-01', 'endDate' => '2025-01-02'],
                'expected' => ['key1' => $operation1],
            ],
            'filter with no matches' => [
                'cache' => $cache,
                'filter' => ['userId' => 3, 'operationType' => null, 'startDate' => null, 'endDate' => null],
                'expected' => [],
            ],
        ];
    }

    public static function loadDataProvider(): array
    {
        $operation1 = (new Operation())->setDate('2014-12-31')
            ->setUserId(4)
            ->setClientType(ClientType::PRIVATE)
            ->setOperationType(OperationType::WITHDRAW)
            ->setAmount('10.00')
            ->setCurrency(Currency::from('EUR'));

        $operation2 = (new Operation())->setDate('2014-12-20')
            ->setUserId(2)
            ->setClientType(ClientType::BUSINESS)
            ->setOperationType(OperationType::DEPOSIT)
            ->setAmount('20.00')
            ->setCurrency(Currency::from('USD'));

        return [
            'when cache is no set yet' => [
                'cache' => null,
                'readerData' => function (): Generator {
                    yield ['2014-12-31', '4', 'private', 'withdraw', '10.00', 'EUR'];
                    yield ['2014-12-20', '2', 'business', 'deposit', '20.00', 'USD'];
                },
                'expectedCache' => [
                    '2014-12-31_4_private_withdraw_10.00_EUR' => $operation1,
                    '2014-12-20_2_business_deposit_20.00_USD' => $operation2,
                ],
            ],
            'when cache is already set' => [
                'cache' => [
                    '2014-12-31_4_private_withdraw_10.00_EUR' => $operation1,
                ],
                'readerData' => null,
                'expectedCache' => [
                    '2014-12-31_4_private_withdraw_10.00_EUR' => $operation1,
                ],
            ],
            'when cache is already set but its empty' => [
                'cache' => [],
                'readerData' => null,
                'expectedCache' => [],
            ],
        ];
    }
}
