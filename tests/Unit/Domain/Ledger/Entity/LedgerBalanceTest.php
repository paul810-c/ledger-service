<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Ledger\Entity;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Ledger\Entity\LedgerBalance;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Shared\ValueObject\LedgerId;
use PHPUnit\Framework\TestCase;

class LedgerBalanceTest extends TestCase
{
    public function testBalanceCanBeIncreased(): void
    {
        $ledger = new Ledger(new LedgerId());
        $balance = LedgerBalance::create($ledger, Currency::EUR);

        $balance->updateBalance(5000);

        $this->assertEquals(5000, $balance->getBalanceInCents());
    }

    public function testBalanceCanBeDecreased(): void
    {
        $ledger = new Ledger(new LedgerId());
        $balance = LedgerBalance::create($ledger, Currency::EUR);
        $balance->updateBalance(5000);

        $balance->updateBalance(-3000);

        $this->assertEquals(2000, $balance->getBalanceInCents());
    }

    public function testThrowsExceptionOnInsufficientFunds(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $ledger = new Ledger(new LedgerId(),);
        $balance = LedgerBalance::create($ledger, Currency::EUR);

        $balance->updateBalance(-100);
    }
}
