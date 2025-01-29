<?php

namespace Elmsellem\Services\Commission;

use Elmsellem\Models\Operation;
use Elmsellem\Services\Commission\Calculators\AbstractCommission;
use Elmsellem\Services\Commission\CommissionBaseAmountResolvers\AmountResolverInterface;

class CommissionHandler
{
    public function __construct(
        protected AbstractCommission       $calculator,
        protected ?AmountResolverInterface $amountResolver)
    {
    }

    public function calculate(Operation $operation): float
    {
        $amount = $operation->getAmount();
        if (!is_null($this->amountResolver)) {
            $amount = $this->amountResolver->getCommissionBaseAmount($operation);
        }

        return $this->calculator->calculate($amount);
    }
}
