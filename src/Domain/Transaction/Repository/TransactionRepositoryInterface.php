<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Repository;

use App\Domain\Transaction\Entity\Transaction;

interface TransactionRepositoryInterface
{
    public function save(Transaction $transaction): void;

    public function exists(string $transactionId): bool;
}
