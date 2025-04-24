<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\Transaction\Command\RecordTransactionCommand;
use App\Application\Transaction\Handler\RecordTransactionHandler;
use App\DataFixtures\LedgerFixture;
use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Ledger\Repository\LedgerBalanceRepositoryInterface;
use App\Domain\Ledger\Repository\LedgerRepositoryInterface;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Transaction\Repository\TransactionRepositoryInterface;
use App\Domain\Transaction\ValueObject\TransactionType;
use App\Tests\KernelTestCaseWithFixtures;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class RecordTransactionHandlerTest extends KernelTestCaseWithFixtures
{
    public function testTransactionIsRecordedAndBalanceUpdated(): void
    {
        /** @var LedgerBalanceRepositoryInterface $balanceRepository */
        $balanceRepository = self::getContainer()->get(LedgerBalanceRepositoryInterface::class);

        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];
        $ledgerId = $ledger->getId();

        $command = new RecordTransactionCommand(
            Uuid::v4()->toRfc4122(),
            $ledgerId,
            TransactionType::CREDIT,
            5000,
            Currency::EUR
        );

        $handler = self::getContainer()->get(RecordTransactionHandler::class);
        $handler->__invoke($command);

        $balance = $balanceRepository->find($ledger, Currency::EUR);

        $this->assertNotNull($balance);
        $this->assertEquals(5000, $balance->getBalanceInCents());

        $transactions = $this->em->getRepository(Transaction::class)->findBy(['ledger' => $ledger]);
        $this->assertCount(1, $transactions);
        $this->assertEquals(5000, $transactions[0]->getAmount()->amountInCents);
    }

    public function testDuplicateTransactionIsIgnored(): void
    {
        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];
        $ledgerId = $ledger->getId();

        $transactionId = Uuid::v4()->toRfc4122();

        $handler = self::getContainer()->get(RecordTransactionHandler::class);

        $command = new RecordTransactionCommand(
            $transactionId,
            $ledgerId,
            TransactionType::CREDIT,
            2000,
            Currency::EUR
        );

        $handler->__invoke($command);
        $handler->__invoke($command);

        $balanceRepo = self::getContainer()->get(LedgerBalanceRepositoryInterface::class);
        $balance = $balanceRepo->find($ledger, Currency::EUR);

        $this->assertEquals(2000, $balance->getBalanceInCents());
    }
}
