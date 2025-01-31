<?php

namespace Elmsellem\Repositories;

use Elmsellem\Enums\{ClientType, Currency, OperationType};
use Elmsellem\Models\Operation;
use Elmsellem\Support\FileSystem\{AbstractFileReader, ReaderFactory};
use Exception;

class OperationRepository
{
    protected static ?array $cache = null;
    protected AbstractFileReader $reader;
    public static string $filePath;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->reader = ReaderFactory::createFromPath(self::$filePath);
        $this->loadData();
    }

    public function getAll(): array
    {
        return self::$cache;
    }

    public function findBy(array $filter): array
    {
        return array_filter(self::$cache, function (Operation $item) use ($filter) {
            $condition = true;

            if ($filter['userId']) {
                $condition = $item->getUserId() === $filter['userId'];
            }

            if ($filter['operationType']) {
                $condition = $condition && $item->getOperationType() === $filter['operationType'];
            }

            if ($filter['startDate']) {
                $condition = $condition && $item->getDate() >= $filter['startDate'];
            }

            if ($filter['endDate']) {
                $condition = $condition && $item->getDate() <= $filter['endDate'];
            }

            return $condition;
        });
    }

    public static function generateOperationKey(Operation $operation): string
    {
        return sprintf(
            '%s_%s_%s_%s_%s_%s',
            $operation->getDate(),
            $operation->getUserId(),
            $operation->getClientType()->value,
            $operation->getOperationType()->value,
            $operation->getAmount(),
            $operation->getCurrency()->value,
        );
    }

    protected function loadData(): void
    {
        if (isset(self::$cache)) {
            return;
        }

        self::$cache = [];
        foreach ($this->reader->fetchData() as $data) {
            $operation = new Operation();
            $operation->setDate($data[0])
                ->setUserId($data[1])
                ->setClientType(ClientType::from($data[2]))
                ->setOperationType(OperationType::from($data[3]))
                ->setAmount($data[4])
                ->setCurrency(Currency::from($data[5]));

            $key = self::generateOperationKey($operation);

            self::$cache[$key] = $operation;
        }
    }
}
