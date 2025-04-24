<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

// todos: maybe move this to a db table
enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';

    public static function fromString(string $code): self
    {
        return self::from(strtoupper($code));
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
