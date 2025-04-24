<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\Transaction\Command\RecordTransactionCommand;
use App\Application\Transaction\Handler\RecordTransactionHandler;
use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Ledger\Repository\LedgerBalanceRepositoryInterface;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Transaction\ValueObject\TransactionType;
use App\Tests\KernelTestCaseWithFixtures;
use Symfony\Component\Uid\Uuid;

class RecordDebitTransactionTest extends KernelTestCaseWithFixtures
{
    public function testDebitTransactionDecreasesBalance(): void
    {
        /** @var LedgerBalanceRepositoryInterface $balanceRepository */
        $balanceRepository = self::getContainer()->get(LedgerBalanceRepositoryInterface::class);

        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];
        $ledgerId = $ledger->getId();

        $handler = self::getContainer()->get(RecordTransactionHandler::class);

        // first credit 10000 cents (100.00) to make sure
        $creditCommand = new RecordTransactionCommand(
            Uuid::v4()->toRfc4122(),
            $ledgerId,
            TransactionType::CREDIT,
            10_000,
            Currency::EUR
        );
        $handler->__invoke($creditCommand);

        // then debit 3000 cents (30.00)
        $debitCommand = new RecordTransactionCommand(
            Uuid::v4()->toRfc4122(),
            $ledgerId,
            TransactionType::DEBIT,
            3000,
            Currency::EUR
        );
        $handler->__invoke($debitCommand);

        $balance = $balanceRepository->find($ledger, Currency::EUR);
        $this->assertNotNull($balance);
        $this->assertEquals(7000, $balance->getBalanceInCents());

        $transactions = $this->em->getRepository(Transaction::class)->findBy(['ledger' => $ledger]);
        $this->assertCount(2, $transactions);
    }
}
