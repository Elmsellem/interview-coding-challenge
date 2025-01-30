<?php

namespace Elmsellem\Services\Commission;

use Elmsellem\Models\Operation;
use Elmsellem\Services\Math;

class CommissionCalculatorService
{
    protected CommissionRulesRegistry $rulesRegistry;

    public function __construct(CommissionRulesRegistry $ruleRegistry)
    {
        $this->rulesRegistry = $ruleRegistry;
    }

    public function calculateCommission(Operation $operation): string
    {
        $rule = $this->rulesRegistry->getCommissionHandler(
            $operation->getOperationType(),
            $operation->getClientType(),
        );

        $precision = config('app')['currencyDecimalPlaces'][$operation->getCurrency()->value];

        return Math::roundUp($rule->calculate($operation), $precision);
    }
}
