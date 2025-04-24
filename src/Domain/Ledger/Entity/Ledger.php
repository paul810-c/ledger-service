<?php

declare(strict_types=1);

namespace App\Domain\Ledger\Entity;

use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Shared\ValueObject\LedgerId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ledgers')]
class Ledger
{
    #[ORM\Id]
    #[ORM\Column(type: 'ledger_id')]
    private LedgerId $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(LedgerId $id)
    {
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(): self
    {
        return new self(new LedgerId());
    }

    public function getId(): LedgerId
    {
        return $this->id;
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
