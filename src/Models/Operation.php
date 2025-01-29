<?php

namespace Elmsellem\Models;

use Elmsellem\Enums\{ClientType, Currency, OperationType};

class Operation
{
    protected string $date;
    protected int $userId;
    protected ClientType $clientType;
    protected OperationType $operationType;
    protected float $amount;
    protected Currency $currency;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getClientType(): ClientType
    {
        return $this->clientType;
    }

    public function setClientType(ClientType $clientType): self
    {
        $this->clientType = $clientType;
        return $this;
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function setOperationType(OperationType $operationType): self
    {
        $this->operationType = $operationType;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
}
