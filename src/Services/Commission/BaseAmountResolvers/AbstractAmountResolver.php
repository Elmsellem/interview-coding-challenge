<?php

namespace Elmsellem\Services\Commission\BaseAmountResolvers;

use Elmsellem\Models\Operation;

abstract class AbstractAmountResolver
{
    public function __construct(array $options = [])
    {
    }

    /**
     * Get the base amount for commission calculation
     */
    abstract public function getCommissionBaseAmount(Operation $operation): string;
}
