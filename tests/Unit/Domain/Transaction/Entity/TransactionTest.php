<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Transaction\Entity;

use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Shared\ValueObject\LedgerId;
use App\Domain\Transaction\ValueObject\TransactionType;
use App\Domain\Ledger\Entity\Ledger;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TransactionTest extends TestCase
{
    public function testTransactionIsCreatedWithCorrectData(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $ledger = new Ledger(new LedgerId());
        $type = TransactionType::CREDIT;
        $money = new Money(1000, Currency::EUR);

        $transaction = Transaction::create($uuid, $ledger, $type, $money);

        $this->assertEquals($uuid, (string) $transaction->getId());
        $this->assertSame($ledger, $transaction->getLedger());
        $this->assertEquals($type, $transaction->getType());
        $this->assertEquals($money, $transaction->getAmount());
        $this->assertInstanceOf(\DateTimeImmutable::class, $transaction->getCreatedAt());
    }
}
