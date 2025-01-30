<?php

namespace Elmsellem\Services\Commission\Calculators;

class PercentageCommission extends AbstractCommission
{
    protected float $commission;

    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->commission = $options['commission'];
    }

    public function calculate(float $amount): float
    {
        return $amount * ($this->commission / 100);
    }
}
