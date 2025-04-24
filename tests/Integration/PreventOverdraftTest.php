<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\Transaction\Command\RecordTransactionCommand;
use App\Application\Transaction\Handler\RecordTransactionHandler;
use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Transaction\ValueObject\TransactionType;
use App\Tests\KernelTestCaseWithFixtures;
use Symfony\Component\Uid\Uuid;

class PreventOverdraftTest extends KernelTestCaseWithFixtures
{
    public function testDebitFailsIfInsufficientFunds(): void
    {
        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];
        $ledgerId = $ledger->getId();

        $handler = self::getContainer()->get(RecordTransactionHandler::class);

        $command = new RecordTransactionCommand(
            Uuid::v4()->toRfc4122(),
            $ledgerId,
            TransactionType::DEBIT,
            50000,
            Currency::EUR
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $handler->__invoke($command);
    }
}
