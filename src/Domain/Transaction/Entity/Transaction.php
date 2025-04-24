<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Entity;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\Transaction\ValueObject\TransactionType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'transactions')]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Ledger::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Ledger $ledger;

    #[ORM\Column(type: 'string', enumType: TransactionType::class)]
    private TransactionType $type;

    #[ORM\Embedded(class: Money::class, columnPrefix: false)]
    private Money $amount;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $id, Ledger $ledger, TransactionType $type, Money $amount)
    {
        $this->id = $id;
        $this->ledger = $ledger;
        $this->type = $type;
        $this->amount = $amount;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        string $transactionId,
        Ledger $ledger,
        TransactionType $type,
        Money $amount
    ): self {
        return new self($transactionId, $ledger, $type, $amount);
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function setAmount(Money $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
