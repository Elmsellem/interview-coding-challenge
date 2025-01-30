<?php

namespace Elmsellem\Services\Commission\Calculators;

use Elmsellem\Services\Math;

class PercentageCommission extends AbstractCommission
{
    protected float $commission;

    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->commission = $options['commission'];
    }

    public function calculate(string $amount): string
    {
        return Math::multiply($amount, Math::divide($this->commission, 100));
    }
}
