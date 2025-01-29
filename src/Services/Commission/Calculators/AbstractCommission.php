<?php

namespace Elmsellem\Services\Commission\Calculators;

abstract class AbstractCommission
{
    public function __construct(array $options = [])
    {
    }

    abstract public function calculate(float $amount): float;
}
