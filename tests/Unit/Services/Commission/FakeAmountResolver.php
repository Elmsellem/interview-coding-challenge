<?php

namespace Elmsellem\Tests\Unit\Services\Commission;

use Elmsellem\Models\Operation;
use Elmsellem\Services\Commission\BaseAmountResolvers\AbstractAmountResolver;

class FakeAmountResolver extends AbstractAmountResolver
{
    public function getCommissionBaseAmount(Operation $operation): string
    {
        return $operation->getAmount();
    }
}
