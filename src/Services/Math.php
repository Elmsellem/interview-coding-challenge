<?php

declare(strict_types=1);

namespace Elmsellem\Services;

class Math
{
    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b);
    }

    public static function subtract(string $a, string $b): string
    {
        return bcsub($a, $b);
    }

    public static function multiply(string $a, string $b): string
    {
        return bcmul($a, $b);
    }

    public static function divide(string $a, string $b): string
    {
        return bcdiv($a, $b);
    }

    public static function roundUp(string $number, int $precision = 0): string {
        $factor = bcpow('10', (string)$precision);
        $scaled = bcmul($number, $factor, $precision + 1);
        $rounded = bcadd($scaled, '0.9', 0);

        return bcdiv($rounded, $factor, $precision);
    }

    public static function compare(string $a, string $b): int
    {
        return bccomp($a, $b);
    }
}
