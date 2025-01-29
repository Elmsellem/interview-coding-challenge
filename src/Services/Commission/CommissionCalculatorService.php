<?php

namespace Elmsellem\Services\Commission;

use Elmsellem\Models\Operation;

class CommissionCalculatorService
{
    protected CommissionRulesRegistry $rulesRegistry;

    public function __construct(CommissionRulesRegistry $ruleRegistry)
    {
        $this->rulesRegistry = $ruleRegistry;
    }

    public function calculateCommission(Operation $operation): float
    {
        $rule = $this->rulesRegistry->getCommissionHandler(
            $operation->getOperationType(),
            $operation->getClientType(),
        );

        return $rule->calculate($operation);
    }
}
