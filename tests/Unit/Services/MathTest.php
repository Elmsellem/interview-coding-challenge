<?php

namespace Elmsellem\Tests\Unit\Services;

use Elmsellem\Services\Math;
use PHPUnit\Framework\Attributes\{After, Before};
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
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

    protected function setUp(): void
    {
        bcscale(10);
    }

    public function testAdd(): void
    {
        $this->assertEquals('5.0000000000', Math::add('2.5', '2.5'));
        $this->assertEquals('0.0000000000', Math::add('0', '0'));
    }

    public function testSubtract(): void
    {
        $this->assertEquals('1.0000000000', Math::subtract('3.5', '2.5'));
        $this->assertEquals('-2.5000000000', Math::subtract('0', '2.5'));
    }

    public function testMultiply(): void
    {
        $this->assertEquals('6.2500000000', Math::multiply('2.5', '2.5'));
        $this->assertEquals('0.0000000000', Math::multiply('0', '100'));
    }

    public function testDivide(): void
    {
        $this->assertEquals('1.0000000000', Math::divide('5', '5'));
        $this->assertEquals('0.5000000000', Math::divide('1', '2'));
    }

    public function testRoundUp(): void
    {
        $this->assertEquals('2.457', Math::roundUp('2.4567', 3));
        $this->assertEquals('3', Math::roundUp('2.4456'));
    }

    public function testCompare(): void
    {
        $this->assertEquals(0, Math::compare('2.5', '2.5'));
        $this->assertEquals(1, Math::compare('3.5', '2.5'));
        $this->assertEquals(-1, Math::compare('2.5', '3.5'));
    }

    public function testGt(): void
    {
        $this->assertTrue(Math::gt('3.5', '2.5'));
        $this->assertFalse(Math::gt('2.5', '3.5'));
        $this->assertFalse(Math::gt('2.5', '2.5'));
    }
}
