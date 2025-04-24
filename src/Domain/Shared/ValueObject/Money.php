<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use Brick\Money\Money as BrickMoney;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
readonly class Money
{
    #[ORM\Column(name: 'amount_in_cents', type: 'bigint')]
    public int $amountInCents;

    #[ORM\Column(name: 'currency', type: 'string', length: 3)]
    public string $currency;

    public function __construct(int $amountInCents, Currency|string $currency)
    {
        $this->amountInCents = $amountInCents;
        $this->currency = ($currency instanceof Currency) ? $currency->value : strtoupper($currency);
    }

    public function toBrick(): BrickMoney
    {
        return BrickMoney::ofMinor($this->amountInCents, $this->currency);
    }

    public function getMajorAmount(): string
    {
        return $this->toBrick()->getAmount();
    }
}
