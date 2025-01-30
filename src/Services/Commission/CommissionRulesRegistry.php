<?php

namespace Elmsellem\Services\Commission;

use Elmsellem\Enums\{ClientType, OperationType};
use InvalidArgumentException;

class CommissionRulesRegistry
{
    private array $rules = [];

    public function __construct()
    {
        $this->loadRules();
    }

    protected function loadRules(): void
    {
        $rules = config('app')['commissionRules'] ?? [];

        foreach ($rules as $rule) {
            $this->addRule(
                $rule['operationType'],
                $rule['clientType'],
                $rule['commissionCalculator'],
                $rule['commissionAmountResolver'] ?? null,
            );
        }
    }

    public function addRule(
        OperationType $operationType,
        ClientType    $clientType,
        array         $calculatorConfig,
        ?array        $amountResolverConfig = null,
    ): void
    {
        $calculator = new $calculatorConfig['class']($calculatorConfig['options']);
        $amountResolver = $amountResolverConfig ?
            new $amountResolverConfig['class']($amountResolverConfig['options']) :
            null;

        $this->rules[$operationType->value][$clientType->value] = new CommissionHandler($calculator, $amountResolver);
    }

    public function getCommissionHandler(OperationType $operationType, ClientType $clientType): CommissionHandler
    {
        if (isset($this->rules[$operationType->value][$clientType->value])) {
            return $this->rules[$operationType->value][$clientType->value];
        }

        throw new InvalidArgumentException(sprintf(
            'No commission rule found for operation type "%s" and client type "%s".',
            $operationType->value,
            $clientType->value,
        ));
    }
}
