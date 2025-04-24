<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Ledger\Repository\LedgerRepositoryInterface;
use App\Domain\Shared\ValueObject\LedgerId;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineLedgerRepository implements LedgerRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function save(Ledger $ledger): void
    {
        $this->em->persist($ledger);
    }

    public function find(LedgerId $id): ?Ledger
    {
        return $this->em->getRepository(Ledger::class)->find($id);
    }

    public function findOrFail(LedgerId $id): Ledger
    {
        $ledger = $this->find($id);

        if (!$ledger) {
            throw new \RuntimeException(sprintf('Ledger not found: %s') . $id->__toString());
        }

        return $ledger;
    }
}
