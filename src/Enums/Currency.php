<?php

namespace Elmsellem\Enums;

use InvalidArgumentException;

class Currency
{
    private static ?array $instances = null;

    private function __construct(public readonly string $value)
    {
    }

    private static function loadCases(): void
    {
        if (isset(self::$instances)) {
            return;
        }

        $currencies = config('app')['currencies'] ?? [];
        self::$instances = [];

        foreach ($currencies as $value) {
            self::$instances[$value] = new self($value);
        }
    }

    public static function cases(): array
    {
        self::loadCases();

        return array_values(self::$instances);
    }

    public static function from(string $value): self
    {
        self::loadCases();
        if (!isset(self::$instances[$value])) {
            throw new InvalidArgumentException('Invalid enum value: ' . $value);
        }

        return self::$instances[$value];
    }
}
