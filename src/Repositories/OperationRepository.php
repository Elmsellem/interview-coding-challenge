<?php

namespace Elmsellem\Repositories;

use Elmsellem\Enums\{ClientType, Currency, OperationType};
use Elmsellem\Models\Operation;
use Elmsellem\Support\FileSystem\AbstractFileReader;
use Elmsellem\Support\FileSystem\ReaderFactory;
use Exception;

class OperationRepository
{
    private static ?array $cache = null;
    private AbstractFileReader $reader;

    /**
     * @throws Exception
     */
    public function __construct(private readonly string $filePath)
    {
        $this->reader = ReaderFactory::createFromPath($this->filePath);
    }

    public function getAll(): array
    {
        if (isset(self::$cache)) {
            return self::$cache;
        }

        foreach ($this->reader->fetchData() as $data) {
            $operation = new Operation();
            $operation->setDate($data[0])
                ->setUserId($data[1])
                ->setClientType(ClientType::from($data[2]))
                ->setOperationType(OperationType::from($data[3]))
                ->setAmount($data[4])
                ->setCurrency(Currency::from($data[5]));

            self::$cache[] = $operation;
        }

        return self::$cache ?? [];
    }
}
