<?php

declare(strict_types=1);

namespace App\Domain\Ledger\Repository;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Shared\ValueObject\LedgerId;

interface LedgerRepositoryInterface
{
    public function save(Ledger $ledger): void;

    public function find(LedgerId $id): ?Ledger;

    public function findOrFail(LedgerId $id): Ledger;
}
