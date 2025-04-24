<?php

declare(strict_types=1);

namespace App\Application\Transaction\Command;

use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Transaction\ValueObject\TransactionType;
use App\Domain\Shared\ValueObject\LedgerId;

readonly class RecordTransactionCommand
{
    public function __construct(
        public string $transactionId,
        public LedgerId $ledgerId,
        public TransactionType $type,
        public int $amountInCents,
        public Currency $currency,
    ) {}
}