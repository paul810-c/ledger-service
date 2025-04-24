<?php

declare(strict_types=1);

namespace App\Application\Ledger\Query;

use App\Domain\Shared\ValueObject\LedgerId;

final readonly class GetLedgerBalancesQuery
{
    public function __construct(
        public LedgerId $ledgerId
    ) {}
}