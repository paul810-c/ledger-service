<?php

declare(strict_types=1);

namespace App\Domain\Transaction\ValueObject;

enum TransactionType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';

    public static function fromString(string $type): self
    {
        return self::from(strtolower($type));
    }

    public static function types(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
