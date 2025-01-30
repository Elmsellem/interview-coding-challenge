<?php

namespace Elmsellem\DTOs;

class ExchangeRatesDTO
{
    /**
     * @param int $timestamp
     * @param string $base
     * @param string $date
     * @param array<string, float> $rates
     */
    public function __construct(
        public int $timestamp,
        public string $base,
        public string $date,
        public array $rates,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            timestamp: $data['timestamp'] ?? 0,
            base: $data['base'] ?? '',
            date: $data['date'] ?? '',
            rates: $data['rates'] ?? []
        );
    }
}
