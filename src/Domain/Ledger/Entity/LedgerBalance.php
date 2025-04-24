<?php

declare(strict_types=1);

namespace App\Domain\Ledger\Entity;

use App\Domain\Shared\ValueObject\Currency;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ledger_balances')]
class LedgerBalance
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Ledger::class)]
    #[ORM\JoinColumn(name: 'ledger_id', referencedColumnName: 'id')]
    private Ledger $ledger;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(name: 'balance_in_cents', type: 'bigint')]
    private int $balanceInCents;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Ledger $ledger, string $currency)
    {
        $this->ledger = $ledger;
        $this->currency = $currency;
        $this->balanceInCents = 0;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateBalance(int $adjustmentInCents): void
    {
        $newBalance = $this->balanceInCents + $adjustmentInCents;

        if ($newBalance < 0) {
            throw new \DomainException('Insufficient funds');
        }

        $this->setBalanceInCents($newBalance);
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    public static function create(Ledger $ledger, string|Currency $currency): self
    {
        $code = $currency instanceof Currency ? $currency->value : strtoupper($currency);
        return new self($ledger, $code);
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }

    public function setLedger(Ledger $ledger): self
    {
        $this->ledger = $ledger;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getBalanceInCents(): int
    {
        return $this->balanceInCents;
    }

    public function setBalanceInCents(int $balanceInCents): self
    {
        $this->balanceInCents = $balanceInCents;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
}
