<?php

namespace Elmsellem\Services\Commission\CommissionBaseAmountResolvers;

use Elmsellem\Models\Operation;

abstract class AbstractAmountResolver
{
    public function __construct(array $options = [])
    {
    }

    abstract public function getCommissionBaseAmount(Operation $operation): float;
}
