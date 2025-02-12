<?php

namespace Elmsellem\Services\Commission;

use Elmsellem\Models\Operation;
use Elmsellem\Services\Commission\Calculators\AbstractCommission;
use Elmsellem\Services\Commission\BaseAmountResolvers\AbstractAmountResolver;

class CommissionHandler
{
    public function __construct(
        protected AbstractCommission      $calculator,
        protected ?AbstractAmountResolver $amountResolver
    ) {
    }

    public function calculate(Operation $operation): string
    {
        $amount = $operation->getAmount();
        if (!is_null($this->amountResolver)) {
            $amount = $this->amountResolver->getCommissionBaseAmount($operation);
        }

        return $this->calculator->calculate($amount);
    }
}
