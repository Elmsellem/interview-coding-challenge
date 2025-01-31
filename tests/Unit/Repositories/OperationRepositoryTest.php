<?php

declare(strict_types=1);

namespace Elmsellem\Tests\Unit\Repositories;

use Elmsellem\Enums\{ClientType, Currency, OperationType};
use Elmsellem\Models\Operation;
use Elmsellem\Repositories\OperationRepository;
use Elmsellem\Support\FileSystem\AbstractFileReader;
use Elmsellem\Tests\ReflectionHelper;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class OperationRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use OperationRepositoryDataProvider;

    /**
     * @throws ReflectionException
     */
    public function testGetAll()
    {
        $mock = Mockery::mock(OperationRepository::class)->makePartial();

        $mockedCache = ['key' => 'value'];
        ReflectionHelper::setProtectedProperty($mock, 'cache', $mockedCache);

        $this->assertEquals($mockedCache, $mock->getAll());
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('findByProvider')]
    public function testFindBy(array $cache, array $filter, array $expected)
    {
        $mock = Mockery::mock(OperationRepository::class)->makePartial();
        ReflectionHelper::setProtectedProperty($mock, 'cache', $cache);

        $result = $mock->findBy($filter);

        $this->assertEquals($expected, $result);
    }

    public function testGenerateOperationKey(): void
    {
        $operation = new Operation();
        $operation->setDate('2025-01-01');
        $operation->setUserId(1);
        $operation->setClientType(ClientType::PRIVATE);
        $operation->setOperationType(OperationType::DEPOSIT);
        $operation->setAmount('100.00');
        $operation->setCurrency(Currency::from('EUR'));

        $expectedKey = '2025-01-01_1_private_deposit_100.00_EUR';
        $this->assertEquals($expectedKey, OperationRepository::generateOperationKey($operation));
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('loadDataProvider')]
    public function testLoadData(null|array $cache, ?callable $readerData, array $expectedCache): void
    {
        $mockReader = Mockery::mock(AbstractFileReader::class);
        $mock = Mockery::mock(OperationRepository::class)->makePartial();

        ReflectionHelper::setProtectedProperty($mock, 'reader', $mockReader);
        ReflectionHelper::setProtectedProperty($mock, 'cache', $cache);

        if (isset($readerData)) {
            $mockReader->shouldReceive('fetchData')->once()->andReturn($readerData());
        }

        ReflectionHelper::invokeProtectedMethod($mock, 'loadData');

        $this->assertEquals($expectedCache, ReflectionHelper::getProtectedProperty($mock, 'cache'));
    }
}
