<?php

namespace Elmsellem\Tests\Unit\Services\Commission\Calculators;

use Elmsellem\Services\Commission\Calculators\PercentageCommission;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

class PercentageCommissionTest extends TestCase
{
    private int $currentBcScale;

    #[Before]
    protected function saveBcScale(): void
    {
        $this->currentBcScale = bcscale();
    }

    #[After]
    protected function revertBcScale(): void
    {
        bcscale($this->currentBcScale);
    }

    public function testCalculate(): void
    {
        bcscale(10);
        $calculator = new PercentageCommission([
            'commission' => 0.03
        ]);

        $result = $calculator->calculate('200.00');

        $this->assertEquals('0.0600000000', $result);
    }
}