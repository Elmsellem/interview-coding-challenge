<?php

namespace Elmsellem\Services\Commission\CommissionBaseAmountResolvers;

use Elmsellem\Models\Operation;

interface AmountResolverInterface
{
    public function getCommissionBaseAmount(Operation $operation): float;
}
