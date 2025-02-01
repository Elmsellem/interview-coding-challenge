<?php

namespace Elmsellem\Services\Commission\BaseAmountResolvers;

use DateMalformedStringException;
use DateTime;
use Elmsellem\Enums\{Currency, OperationType};
use Elmsellem\Models\Operation;
use Elmsellem\Repositories\OperationRepository;
use Elmsellem\Services\{CurrencyConversionInterface, CurrencyConversionService, Math};
use GuzzleHttp\Exception\GuzzleException;

class PrivateWithdrawAmountResolver extends AbstractAmountResolver
{
    protected OperationRepository $operationRepository;
    protected CurrencyConversionInterface $currencyService;
    protected float $maxFreeAmount;
    protected int $weeklyFreeOperationsNumber;
    protected Currency $baseCurrency;

    public function __construct(array $options = [])
    {
        parent::__construct();

        $this->maxFreeAmount = $options['maxFreeAmount'];
        $this->weeklyFreeOperationsNumber = $options['weeklyFreeOperationsNumber'];
        $this->operationRepository = new OperationRepository();
        $this->currencyService = new CurrencyConversionService();
        $this->baseCurrency = Currency::from(config('app')['baseCurrency']);
    }

    /**
     * @inheritDoc
     *
     * @throws DateMalformedStringException
     * @throws GuzzleException
     */
    public function getCommissionBaseAmount(Operation $operation): string
    {
        $monday = new DateTime($operation->getDate());
        $sunday = new DateTime($operation->getDate());
        $monday = $monday->modify('Monday this week')->format('Y-m-d');
        $sunday = $sunday->modify('Sunday this week')->format('Y-m-d');

        $weekOperations = $this->operationRepository->findBy([
            'userId' => $operation->getUserId(),
            'operationType' => OperationType::WITHDRAW,
            'startDate' => $monday,
            'endDate' => $sunday,
        ]);

        /** Return the operation amount if it exceeds the weekly free operations limit. */
        $operationNumber = $this->getOperationNumber($operation, $weekOperations);
        if ($operationNumber > $this->weeklyFreeOperationsNumber) {
            return $operation->getAmount();
        }

        $amount = $this->currencyService->convert(
            $operation->getCurrency(),
            $this->baseCurrency,
            $operation->getAmount(),
        );

        $baseAmount = $this->getBaseWithFreeAmount($operationNumber, $amount, $weekOperations);

        return $this->currencyService->convert(
            $this->baseCurrency,
            $operation->getCurrency(),
            $baseAmount,
        );
    }

    /**
     * Get the operation order number from a list of operations.
     */
    protected function getOperationNumber(Operation $operation, array $listOperations): int|false
    {
        $key = OperationRepository::generateOperationKey($operation);
        $keys = array_keys($listOperations);
        $index = array_search($key, $keys);

        return is_int($index) ? $index + 1 : false;
    }

    /**
     * Get the base amount for commission calculation after applying the maximum free commission amount.
     *
     * @throws GuzzleException
     */
    protected function getBaseWithFreeAmount(int $position, string $operationAmount, array $weekOperations): string
    {
        $operationsUntilNow = array_slice($weekOperations, 0, $position);

        /** Get the total operation amount for the current week in the base currency. */
        $totalAmountUntilNow = array_reduce(
            $operationsUntilNow,
            function (float $carry, Operation $op) {
                return Math::add($carry, $this->currencyService->convert(
                    $op->getCurrency(),
                    $this->baseCurrency,
                    $op->getAmount(),
                ));
            },
            0,
        );

        /** Returns the operation amount if the free commission was already applied in the previous operations. */
        $previousTotal = Math::subtract($totalAmountUntilNow, $operationAmount);
        if ($previousTotal >= $this->maxFreeAmount) {
            return $operationAmount;
        }

        /** Get and return the remaining available free commission. */
        $restFreeAmount = Math::subtract($this->maxFreeAmount, $previousTotal);

        return Math::gt($operationAmount, $restFreeAmount) ? Math::subtract($operationAmount, $restFreeAmount) : 0;
    }
}
