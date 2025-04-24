<?php

declare(strict_types=1);

namespace App\Domain\Ledger\Repository;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Ledger\Entity\LedgerBalance;
use App\Domain\Shared\ValueObject\LedgerId;

interface LedgerBalanceRepositoryInterface
{
    public function save(LedgerBalance $balance): void;

    public function find(Ledger $ledger, Currency $currency): ?LedgerBalance;

    public function lockRowForUpdate(Ledger $ledger, string $currency): LedgerBalance;

    public function updateLedgerBalance(
        Ledger $ledger,
        string $currency,
        int $adjustmentInCents
    ): void;

    public function findByLedgerId(LedgerId $ledgerId): array;

    public function findOrCreateForUpdate(Ledger $ledger, string $currency): LedgerBalance;
}
