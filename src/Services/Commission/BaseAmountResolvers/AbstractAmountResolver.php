<?php

namespace Elmsellem\Services\Commission\BaseAmountResolvers;

use Elmsellem\Models\Operation;

abstract class AbstractAmountResolver
{
    public function __construct(array $options = [])
    {
    }

    abstract public function getCommissionBaseAmount(Operation $operation): string;
}
